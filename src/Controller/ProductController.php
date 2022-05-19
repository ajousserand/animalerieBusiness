<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\BasketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{

    public function __construct(private ProductRepository $productRepository, private PaginatorInterface $paginator,private BasketService $basketService)
    {
        
    }

    #[Route('/products', name: 'app_products')]
    public function index(Request $request): Response
    {

        $productEntities = $this->productRepository->findAll();

        $pagination = $this->paginator->paginate(
            $productEntities,
            $request->query->getInt('page', 1),
            8
        );
        
        return $this->render('products/index.html.twig', [
            'products'=>$productEntities,
            'pagination'=> $pagination
        ]);
    }

    #[Route('/product/{id}', name: 'app_show_product')]
    public function show(Request $request, int $id): Response
    {

        $productEntity = $this->productRepository->findOneBy(['id'=>$id]);
        
        
        return $this->render('products/show.html.twig', [
            'product'=>$productEntity,
        ]);
    }


    #[Route('/product/{id}/panier/add', name: 'app_add_basket')]
    public function addBasket(Request $request, Product $product): Response
    {
        $user = $this->getUser();
        $this->basketService->addProductToBasket($product, $user);
        
        return $this->redirectToRoute('app_user_basket');
    }

    #[Route('/product/{id}/panier/remove', name: 'app_remove_basket')]
    public function removeBasket(Request $request, Product $product): Response
    {
        $user = $this->getUser();
        $this->basketService->removeProductToBasket($product, $user);
     
        return $this->redirectToRoute('app_user_basket');
    }
}
