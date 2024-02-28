<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etablissement;
use App\Entity\Chambre;


class ChambreController extends AbstractController
{
    #[Route('/chambre', name: 'app_chambre')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $repository = $entityManager->getRepository(Chambre::class);
        $etablissement = $this->getUser()->getEtablissement();
        $idetablissement = $etablissement->getId();
        $chambres  = $repository->findBy(['etablissement' => $etablissement]);
        return $this->render('chambre/index.html.twig', [
            'chambres' => $chambres,
        ]);
    }
}
