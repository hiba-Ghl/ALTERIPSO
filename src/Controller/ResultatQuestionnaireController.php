<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ServiceEtablissement;
use App\Entity\Questionnaire;
use App\Entity\ResultatQuestionnaire;
use App\Entity\Chambre;

class ResultatQuestionnaireController extends AbstractController
{
    
    private $entityManager;
    private $request;

    public function __construct(EntityManagerInterface $entityManager)
     {
         // Initialise la propriété $entityManager avec l'injection de dépendance
         $this->entityManager = $entityManager;
         $this->request = Request::createFromGlobals();
     }

    #[Route('/resultat/questionnaire', name: 'app_resultat_questionnaire')]
   public function index(Request $request): Response
{
    // Récupère l'établissement de l'utilisateur actuellement connecté
    $etablissement = $this->getUser()->getEtablissement();
    // Récupère les services de l'établissement, triés par nom en ordre croissant
    $serviceetablissement = $this->entityManager->getRepository(ServiceEtablissement::class)->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);
    // Récupère les chambres de l'établissement
    $chambres = $this->entityManager->getRepository(Chambre::class)->findBy(['etablissement' => $etablissement]);

    // Récupération des paramètres de filtrage de la requête HTTP
    $chambreId = $request->query->get('chambre_id');
    $serviceId = $request->query->get('service_id');
    $startDate = $request->query->get('start_date');
    $endDate = $request->query->get('end_date');

    // Construire la requête pour obtenir les résultats des questionnaires
    $queryBuilder = $this->entityManager->getRepository(ResultatQuestionnaire::class)
        ->createQueryBuilder('rq')
        ->join('rq.questionnaire', 'q')
        ->where('q.etablissement = :etablissement')
        ->setParameter('etablissement', $etablissement);

    if ($chambreId) {
        $queryBuilder->join('rq.chambre', 'c')
            ->andWhere('c.id = :chambreId')
            ->setParameter('chambreId', $chambreId);
    }

    if ($serviceId) {
        $queryBuilder->join('rq.service', 's')
            ->andWhere('s.id = :serviceId')
            ->setParameter('serviceId', $serviceId);
    }

    if ($startDate && $endDate) {
        $queryBuilder->andWhere('rq.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);
    }

    $resultQuestions = $queryBuilder->getQuery()->getResult();

    // Créez un tableau pour stocker les votes par questionnaire
    $voteCounts = [];
    foreach ($resultQuestions as $result) {
        $questionnaire = $result->getQuestionnaire();
        if ($questionnaire) {
            $vote = $result->getVote();
            $questionId = $questionnaire->getId();

            if (!isset($voteCounts[$questionId])) {
                $voteCounts[$questionId] = [0, 0, 0, 0, 0];
            }

            $voteCounts[$questionId][$vote]++;
        }
    }

    // Filtrer les questionnaires par service, chambre et dates
    $queryBuilder = $this->entityManager->getRepository(Questionnaire::class)
        ->createQueryBuilder('q')
        ->where('q.etablissement = :etablissement')
        ->setParameter('etablissement', $etablissement);

    // Jointure pour les résultats de questionnaire
    $queryBuilder->leftJoin('q.resultatQuestionnaires', 'rq');

    if ($chambreId) {
        $queryBuilder->leftJoin('rq.chambre', 'c')
            ->andWhere('c.id = :chambreId')
            ->setParameter('chambreId', $chambreId);
    }

    if ($serviceId) {
        $queryBuilder->leftJoin('rq.service', 's')
            ->andWhere('s.id = :serviceId')
            ->setParameter('serviceId', $serviceId);
    }

    if ($startDate && $endDate) {
        $queryBuilder->andWhere('rq.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);
    }

    $questionnaires = $queryBuilder->orderBy('q.position', 'ASC')
        ->getQuery()
        ->getResult();

    // Convertir voteCounts en format JSON pour JavaScript
    $voteCountsJson = json_encode($voteCounts, JSON_THROW_ON_ERROR);

    //rendu d'une vue
    return $this->render('resultat_questionnaire/index.html.twig', [
        'resultQuestions' => $resultQuestions,
        'serviceetablissement' => $serviceetablissement,
        'questionnaires' => $questionnaires,
        'voteCounts' => $voteCounts,
        'voteCountsJson' => $voteCountsJson,
        'chambres' => $chambres,
        'selectedChambreId' => $chambreId,
        'selectedServiceId' => $serviceId,
        'startDate' => $startDate,
        'endDate' => $endDate
    ]);
}


    


}