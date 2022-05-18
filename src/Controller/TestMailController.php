<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController extends AbstractController
{
    #[Route('/test/mail', name: 'app_test_mail')]
    public function index(MailerInterface $mailer): Response
    {
      
        $mail = new Email();
        $mail->from('formationSymfony63@gmail.com')
              ->to('capriatti.tony@gmail.com')
              ->subject('Reinitialisation de mot de passe')
              ->html('<a href="">Réinitialisation de mot de passe</a>');
            
        $mailer->send($mail);

        return $this->redirectToRoute('app_homepage');
    }
}
