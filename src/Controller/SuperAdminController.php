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

    /**
     * Constructeur du contrôleur
     * Initialise l'EntityManager pour interagir avec la base de données.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Route pour accéder au tableau de bord du super administrateur.
     * Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion.
     * Récupère les configurations de l'application et la liste des établissements.
     */
    #[Route('/super/admin', name: 'app_super_admin')]
    public function index(): Response
    {
        // Vérification de l'authentification de l'utilisateur
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Récupération de l'établissement de l'utilisateur connecté
        $etablissement = $this->getUser()->getEtablissement();

        // Récupération des configurations de l'application pour cet établissement
        $appConfig  = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

        // Récupération de tous les établissements enregistrés
        $etablissements =  $this->entityManager->getRepository(Etablissement::class)->findAll();
        $etablissementUser = [];
        foreach($etablissements as $et  )
        {
            $users = $this->entityManager->getRepository(User::class)->findBy(["etablissement"=> $et]);
            foreach ($users as $user) {
                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    $etablissementUser[] = ['etablissement' => $et,'user' => $user ];
                }
            }
        }
        // Affichage de la vue du tableau de bord avec les données récupérées
        return $this->render('super_admin/index.html.twig', [
            'appConfig' => $appConfig,
            'etablissements' => $etablissementUser,
            'user' => $this->getUser(),
        ]);
    }
}
