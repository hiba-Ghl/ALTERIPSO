<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etablissement;

class EtablissementController extends AbstractController
{
    #[Route('/etablissement', name: 'app_etablissement')]
    public function index(): Response
    {
        return $this->render('etablissement/index.html.twig', [
            'controller_name' => 'EtablissementController',
        ]);
    }

    #[Route('/modifierbackground', name: 'modifierbackground')]
    public function modifierbackground(EntityManagerInterface $entityManager): Response
    {
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
        
        
       
       $file = $request->files->get('backgound');
       //var_dump($file);die();

       // Vérifiez si les fichiers ont été téléchargés
       if ($file) {
           // Traitez les fichiers comme vous le souhaitez
           $fileName = md5(uniqid()) . '.' . $file->guessExtension();

           // Déplacez les fichiers téléchargés vers le dossier de destination
           $file->move($this->getParameter('etablissement_directory'), $fileName);
           

           // Répondre avec un message de succès ou rediriger vers une autre page
         //  return new Response('Fichiers téléchargés avec succès !');
         $fileName = 'etablissement/' . $fileName;
         

         
        $etablissement->setBackground($fileName);
       }

       $entityManager->persist($etablissement);
       $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
