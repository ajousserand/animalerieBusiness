<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Form\GivePasswordType;
use App\Form\ResetPasswordType;
use App\Repository\ResetPasswordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResetController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $em, private ResetPasswordRepository $resetPasswordRepository)
    {
        
    }

    #[Route('/reset', name: 'app_reset')]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        
        $resetForm = $this->createform(GivePasswordType::class);
        $resetForm->handleRequest($request);
        
        if ($resetForm->isSubmitted() && $resetForm->isValid()) { 
            $data = $resetForm->getData()['email'];
            $user = $this->userRepository->findOneBy(['email'=> $data]);
            
            if($user === null){
                return $this->redirectToRoute('app_reset');
            }else{
                $reset = new ResetPassword();
                $reset->setToken(uniqid());
                $reset->setCreatedAt( new DateTime('now'));
                $reset->setIsReset(false);
                $reset->setUser($user);
                $em->persist($reset);
                $em->flush();

                //127.0.0.1:8000/tto/{token}

                $url = $this->generateUrl('app_reset_password', ['token' => $reset->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);

                $mail = new Email();
                $mail->from('formationSymfony63@gmail.com')
                     ->to($user->getEmail())
                     ->subject('Reinitialisation de mot de passe')
                     ->html('<a href="'.$url.'"> Réinitialisation de mot de passe </a>');
            
        $mailer->send($mail);

        return $this->redirectToRoute('app_homepage');
            }
        }

        return $this->render('reset/index.html.twig', [
            'resetForm' => $resetForm->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function reset(Request $request, EntityManagerInterface $em, string $token, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $reset = $this->resetPasswordRepository->findOneBy(['token'=>$token]);
        if(empty($reset)){
            return $this->redirectToRoute('app_homepage');
        }else{
            $user = $reset->getUser();

            if($reset->getIsReset()===false){
                $resetPassword = $this->createform(ResetPasswordType::class);
                $resetPassword->handleRequest($request);
            
                if ($resetPassword->isSubmitted() && $resetPassword->isValid()) { 
                    $data = $resetPassword->getData();
                    
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $resetPassword->get('plainPassword')->getData()
                        )
                        );
    
                    $reset->setIsReset(true);
                    
                    $em->flush();
                    return $this->redirectToRoute('app_homepage');
    
                }
            }else{
                return $this->redirectToRoute('app_homepage');
            }
            
        }
        
        

        return $this->render('reset/resetPassword.html.twig', [
            'resetPasswordForm' => $resetPassword->createView(),
        ]);
    }
}
