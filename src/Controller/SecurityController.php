<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

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
        // Ajoutez ici le code personnalisé pour la déconnexion
        // Par exemple, enregistrer des journaux, mettre à jour la base de données, etc.
        
        // Redirigez l'utilisateur vers une page spécifique après la déconnexion
        return $this->redirectToRoute('app_login'); // Remplacez 'home' par le nom de la route de votre choix
    }
}
