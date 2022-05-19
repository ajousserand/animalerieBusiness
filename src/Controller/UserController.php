<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BasketService;

class UserController extends AbstractController
{

    public function __construct(private BasketService $basketService)
    {
        
    }
    #[Route('/user', name: 'app_user')]
    public function show(): Response
    {
        $user = $this->getUser();
        return $this->render('user/show.html.twig', [
            'user'=> $user
        ]);
    }

    #[Route('/user/delete', name: 'app_user_delete')]
    public function delete(EntityManagerInterface $em): Response
    {
        /** @var User $user  */
        $user = $this->getUser();

        foreach($user->getCommands() as $command){
            $command->setUser(NULL);
            $em->persist($command);
        }


        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('app_logout');
    }

    #[Route('/user/panier', name: 'app_user_basket')]
    public function showBasket(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $basket = $this->basketService->getbasket($user);
        return $this->render('products/basket.html.twig',[
            'basket'=>$basket,
        ]);
    }



}
