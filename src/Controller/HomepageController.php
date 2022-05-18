<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CommandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;

class HomepageController extends AbstractController
{

    public function __construct(private ProductRepository $productRepository,
                                private CommandRepository $commandRepository,
                                private UserRepository $userRepository,
                                private CategoryRepository $categoryRepository)
    {
        
    }
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }

    #[Route('/admin', name: 'app_homepage_admin')]
    public function indexAdmin(): Response
    {
        $commandEntities = $this->commandRepository->findBy([],['createdAt'=>'DESC'],5);
        $userEntities = $this->userRepository->findBy([],['createdAt'=>'DESC'],5);
        return $this->render('homepage/admin-index.html.twig', [
            'commands'=>$commandEntities,
            'users'=>$userEntities
        ]);
    }
}
