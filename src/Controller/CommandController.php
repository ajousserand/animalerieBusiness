<?php

namespace App\Controller;

use App\Entity\Command;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function panier(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $panier = new Command();
        $panier->setUser($user);
        $panier->setCreatedAt(new DateTime('now'));
        $panier->setStatus(100);



        return $this->render('command/panier.html.twig', [
            'controller_name' => 'CommandController',
        ]);
    }

    #[Route('/panier', name: 'app_panier')]
    public function createCommand(): Response
    {
        $user = $this->getUser();
        $panier = new Command();



        return $this->render('command/panier.html.twig', [
            'controller_name' => 'CommandController',
        ]);
    }
}
