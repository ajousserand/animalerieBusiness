<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductPicture;
use App\Form\ProductType;
use App\Repository\ProductPictureRepository;
use App\Repository\ProductRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('admin/product/crud')]
class ProductCrudController extends AbstractController
{

    public function __construct(private PaginatorInterface $pagination, private SluggerInterface $slugger)
    {
        
    }
    #[Route('/', name: 'app_product_crud_index', methods: ['GET'])]
    public function index(Request $request,ProductRepository $productRepository): Response
    {
        $productEntities = $productRepository->findAll();
        $pagination = $this->pagination->paginate(
            $productEntities,
            $request->query->getInt('page', 1),
            8
        );
        return $this->render('product_crud/index.html.twig', [
            'products' => $pagination
        ]);
    }

    #[Route('/new', name: 'app_product_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product);
            return $this->redirectToRoute('app_product_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product_crud/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_crud_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product_crud/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository, ProductPictureRepository $productPictureRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $productPicture */
            $picture = new ProductPicture();
            $productPicture = $form->get('image')->getData();
            if($productPicture){
              
                $originalFilename = pathinfo($productPicture->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$productPicture->guessExtension();
                try {
                    $productPicture->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    new Exception('Un probleme est arrivé pendant le chargement');
                
                }
                $picture->setPath($newFilename);
                $picture->setProduct($product);
                $picture->setLibelle($productPicture);
            }
            $productPictureRepository->add($picture);
            $productRepository->add($product);
            return $this->redirectToRoute('app_product_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product_crud/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product);
        }

        return $this->redirectToRoute('app_product_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
