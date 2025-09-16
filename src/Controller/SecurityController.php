<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils,Request $request): Response
    {
        // redirection vers la page d'accueil si deja authentifier
        if ($this->getUser()) {
            return $this->redirectToRoute('home'); 
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        // if($lastUsername)
        //     dd($lastUsername);
       /* return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);*/
         return $this->render('security/login.html.twig', [ 
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

   /* #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
       
       
    }*/

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // dd($_SESSION);
        // Ajoutez ici le code personnalisé pour la déconnexion
        // Par exemple, enregistrer des journaux, mettre à jour la base de données, etc.
        
        // Redirigez l'utilisateur vers une page spécifique après la déconnexion
        return $this->redirectToRoute('app_login'); // Remplacez 'home' par le nom de la route de votre choix
    }

    #[Route('/check-remember-me', name: 'check_remember_me')]
    public function checkRememberMe(Security $security): Response
    {
        $user = $security->getUser();

        if ($user) {
            return new Response('L’utilisateur est connecté avec Remember Me');
        } else {
            return new Response('L’utilisateur n’est pas connecté');
        }
    }
}
