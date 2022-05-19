<?php

namespace App\Controller;

use App\Entity\Command;
use App\Service\BasketService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandController extends AbstractController
{
    public function __construct(private BasketService $basketService)
    {

    }

    #[Route('/panier', name: 'app_panier')]
    public function panier(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $basket = $this->basketService->addProductToBasket($Product,$user);
       
        return $this->render('command/panier.html.twig', [
            'panier' => $basket,
        ]);
    }

}
