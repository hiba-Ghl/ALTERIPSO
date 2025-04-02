<?php

namespace App\Controller;

use App\Entity\ConfigApp;
use App\Entity\Etablissement;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SuperAdminController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/super/admin', name: 'app_super_admin')]
    public function index(): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
            $etablissement = $this->getUser()->getEtablissement();
        $appConfig  = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);
        $etablissements =  $this->entityManager->getRepository(Etablissement::class)->findAll();
        return $this->render('super_admin/index.html.twig', [
            'appConfig' => $appConfig,
            'etablissements' => $etablissements,
            'user' => $this->getUser(),
        ]);
    }
}
