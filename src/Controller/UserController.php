<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
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
        $user = $this->getUser();
        $em->remove($user);
        $em->flush();
        return $this->render('common/deleteSuccess.html.twig');
    }


}
