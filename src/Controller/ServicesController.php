<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Services;

class ServicesController extends AbstractController
{
    #[Route('/services', name: 'app_services')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');      
        $etablissement = $this->getUser()->getEtablissement();     
        $services  = $entityManager->getRepository(Services::class)->findBy(['etablissement' => $etablissement]);
        
           return $this->render('services/index.html.twig', ['services' => $services ]);
    }

    #[Route('/services/ajouter', name: 'app_ajouter_services')]
    public function ajouterservice(EntityManagerInterface $entityManager): Response
    {
        $request = Request::createFromGlobals();
        $etablissement = $this->getUser()->getEtablissement();
        $nom = $request->get("nom");
        $type = $request->get("type");
        $service = $request->get("service");
        $active = $request->get("active");
        $valider = $request->get("valider");
    
        if ($request->isMethod('POST') && isset($valider)) {
            $newService = new Services();
            $newService->setEtablissement($etablissement);
            $newService->setNom($nom);
            $newService->setActive($active);
            $newService->setType($type);
            $entityManager->persist($newService);
            $entityManager->flush();
            return $this->redirectToRoute('app_services');
        }
    
        return $this->render('services/ajouter.html.twig');
    }
    
}
