<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Questionnaire;
use App\Entity\ServiceEtablissement;
use App\Entity\User;

class QuestionnaireController extends AbstractController
{
     // Déclaration de la propriété privée $entityManager
     private $entityManager;
     private $request;
 
     // Constructeur de la classe, injecte l'EntityManagerInterface
     public function __construct(EntityManagerInterface $entityManager)
     {
         // Initialise la propriété $entityManager avec l'injection de dépendance
         $this->entityManager = $entityManager;
         $this->request = Request::createFromGlobals();
     }
     #[Route('/questionnaire', name: 'app_questionnaire')]
     public function index(EntityManagerInterface $entityManager): Response
     {
         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
     
         // Récupération de l'établissement de l'utilisateur connecté
         $etablissement = $this->getUser()->getEtablissement();
         $user = $this->getUser();
         $userRoles = $user->getRoles(); // Récupère les rôles de l'utilisateur connecté
         $userId = $user->getId();

     
         if (!$etablissement) {
             throw $this->createAccessDeniedException('Vous n\'êtes associé à aucun établissement.');
         }
     
         // Récupération des services et des questionnaires de l'établissement
         $serviceEtablissementRepo = $entityManager->getRepository(ServiceEtablissement::class);
         $questionnaireRepo = $entityManager->getRepository(Questionnaire::class);
     
         $servicesEtablissement = $serviceEtablissementRepo->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);
         $questionnaires = $questionnaireRepo->findByEtablissement($etablissement);
     
         // Récupération du service général
         $serviceGeneral = $serviceEtablissementRepo->findOneBy(['nom' => 'Géneral', 'etablissement' => $etablissement]);
         $idGeneral = $serviceGeneral ? $serviceGeneral->getId() : null;

        

     
         return $this->render('questionnaire/index.html.twig', [
             'etablissement' => $etablissement,
             'serviceetablissement' => $servicesEtablissement,
             'questionnaire' => $questionnaires,
             'idgeneral' => $idGeneral,
             
         ]);
     }
     
     
    //--------------------Ajouter une question---------------------------------------
    #[Route('/questionnaire/ajouter', name: 'app_ajouter_question')]
    public function ajouterquestion(Request $request): Response
    {
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
        $serviceetablissement  = $this->entityManager->getRepository(ServiceEtablissement::class)->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);
        $questions = $this->entityManager->getRepository(Questionnaire::class)->findBy(['etablissement' => $etablissement], ['position' => 'ASC']);
    
        // Retrieve existing positions by service
        $positionsByService = [];
        $firstFreePositionByService = [];
    
        foreach ($serviceetablissement as $service) {
            $serviceId = $service->getId();
            $questionnaires = $this->entityManager->getRepository(Questionnaire::class)->findBy(['service' => $serviceId]);
            $positions = array_map(function ($questionnaire) {
                return $questionnaire->getPosition();
            }, $questionnaires);

            $positionsByService[$serviceId] = $positions;

        // Calculate the first free position
        $firstFreePosition = 1;
        while (in_array($firstFreePosition, $positions)) {
            $firstFreePosition++;
        }
        $firstFreePositionByService[$serviceId] = $firstFreePosition;
        }
    
        // Retrieve existing questions by service
        $questionsByService = [];
        foreach ($serviceetablissement as $service) {
            $serviceId = $service->getId();
            $questionsByService[$serviceId] = $this->entityManager->getRepository(Questionnaire::class)->findBy(['service' => $serviceId]);
        }
    
        // Create a new Questionnaire object
        $info = new Questionnaire();
    
        // Retrieve form data
        $position = $request->get("Position");
        $serviceId = $request->get("Service");
        $copieQuestionnaire = $request->get("copie");
        $active = $request->get("Active");
        $francais = $request->get("fr");
        $anglais = $request->get("EN");
        $espagnol = $request->get("ES");
        $italiano = $request->get("IT");
        $china = $request->get("ZH");
        $russian = $request->get("RU");
        $Deutsch = $request->get("DE");
        $portugal = $request->get("PT");
        $arabe = $request->get("AR");
    
        $valider = $request->get("valider");
        if ($request->isMethod('POST') && isset($valider)) {
            if ($copieQuestionnaire && strpos($copieQuestionnaire, 'copie_') === 0) {
                $sourceServiceId = explode('_', $copieQuestionnaire)[1];
                $sourceQuestions = $questionsByService[$sourceServiceId];
    
                $newPosition = 1;
                if (!empty($questionsByService[$serviceId])) {
                    $maxPosition = max($positionsByService[$serviceId]);
                    $newPosition = $maxPosition + 1;
                }
    
                foreach ($sourceQuestions as $sourceQuestion) {
                    $newQuestion = clone $sourceQuestion; // Clone the source question
                    $newQuestion->setService($this->entityManager->getRepository(ServiceEtablissement::class)->find($serviceId));
                    $newQuestion->setPosition($newPosition);
                    $newPosition++;
                    $newQuestion->setActive($active);
                    $this->entityManager->persist($newQuestion);
                }
            } else {
                $info->setEtablissement($etablissement);
                $info->setService($this->entityManager->getRepository(ServiceEtablissement::class)->find($serviceId));
                
                if (empty($questionsByService[$serviceId])) {
                    // If no questions exist for the service, set position directly
                    $info->setPosition($position);
                } else {
                    // Increment position based on existing questions
                    // $maxPosition = max($positionsByService[$serviceId]);
                    // $info->setPosition($maxPosition + 1);
                    $info->setPosition($position);
                }
    
                $info->setActive($active);
                $info->setFr($francais);
                $info->setEn($anglais);
                $info->setIt($italiano);
                $info->setEs($espagnol);
                $info->setZh($china);
                $info->setPt($portugal);
                $info->setAr($arabe);
                $info->setRu($russian);
                $info->setDe($Deutsch);
                $info->setQuestion($copieQuestionnaire);
    
                $this->entityManager->persist($info);
            }
            
            $this->entityManager->flush();
            return $this->redirectToRoute('app_questionnaire', ['serviceId' => $serviceId]);
        }
    
        return $this->render('questionnaire/ajouter.html.twig', [
            'user' => $user,
            'etablissement' => $etablissement,
            'serviceetablissement' => $serviceetablissement,
            'questions' => $questions,
            'positionsByService' => $positionsByService,
            'questionsByService' => $questionsByService,
            'firstFreePositionByService' => $firstFreePositionByService,
        ]);
    }
    

    //----------------Supprimer une question----------------------------------------
    
    #[Route('/questionnaire/supprimer/{id}', name: 'app_supprimer_question')]
   public function supprimerquestion(EntityManagerInterface $entityManager, int $id): Response
   {
       $question = $entityManager->getRepository(Questionnaire::class)->find($id);

       if (!$question) {
           throw $this->createNotFoundException(
               'No question found for id '.$id
           );
       }

       $entityManager->remove($question);
       $entityManager->flush();

       return $this->redirectToRoute('app_questionnaire');
   }

   //-----------------Modifier une question----------------------------------------
   
   #[Route('/questionnaire/modifier/{id}', name: 'app_modifier_question')]
   public function modifierquestion(EntityManagerInterface $entityManager, int $id): Response
   {
      $request = Request::createFromGlobals();
      $etablissement = $this->getUser()->getEtablissement();
      $questionnaire = $entityManager->getRepository(Questionnaire::class)->find($id);
      $serviceetablissement  = $this->entityManager->getRepository(ServiceEtablissement::class)->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);
      $question = $this->entityManager->getRepository(Questionnaire::class)->findBy(['etablissement' => $etablissement], ['position' => 'ASC']);
      $info = $entityManager->getRepository(Questionnaire::class)->findById($id)[0];

      // Récupérer les positions existantes par service
      $positionsByService = [];

      foreach ($serviceetablissement as $service) {
          $serviceId = $service->getId();
          $questionnaires = $this->entityManager->getRepository(Questionnaire::class)->findBy(['service' => $serviceId]);
          $positionsByService[$serviceId] = array_map(function ($questionnaire) {
              return $questionnaire->getPosition();
          }, $questionnaires);
      }


      $listback = [];
      foreach ($question as $q) {
      $listback[] = $q->getId();
      }
      $position = $request->get("Position");
      $serviceId = $request->get("Service");
      $service = $this->entityManager->getRepository(ServiceEtablissement::class)->findById($serviceId);
      $question = $request->get("copie");
      $active = $request->get("Active");
      $francais = $request->get("fr");
      $anglais = $request->get("en");
      $espagnol = $request->get("es");
      $italiano = $request->get("it");
      $china = $request->get("chi");
      $russian = $request->get("rus");
      $Deutsch = $request->get("dtsh");
      $portugal = $request->get("pr");
      $arabe = $request->get("ar");
      

      $valider = $request->get("valider");
      if ($request->isMethod('POST') && isset($valider)) {
          $info->setEtablissement($etablissement);
          $info->setService($service[0]);
          $info->setPosition($position);
          $info->setActive($active);
          $info->setFr($francais);
          $info->setEn($anglais);
          $info->setIt($italiano);
          $info->setEs($espagnol);
          $info->setZh($china);
          $info->setPt($portugal);
          $info->setAr($arabe);
          $info->setRu($russian);
          $info->setDe($Deutsch);
          $info->setQuestion($question);

         $entityManager->persist($info);
         $entityManager->flush();
        //  return $this->redirectToRoute('app_questionnaire');
        return $this->redirectToRoute('app_questionnaire', ['serviceId' => $serviceId]);
      }
      
      return $this->render('questionnaire/modifier.html.twig', array('service' => $service,'questionnaire' => $questionnaire,'info' => $info, 'listback' => $listback, 'serviceetablissement' => $serviceetablissement, 'question' => $question,  'positionsByService' =>  $positionsByService ));
   }
      
    //-----------------------Update button active----------------------------------
    // Méthode pour mettre à jour Activation du service en chambre
    #[Route('/update-active-service', name: 'update_active', methods: ['POST'])]
    public function updateActiveService(Request $request, EntityManagerInterface $entityManager)
    {
        $box = $request->get('list');
        if (isset($box) && !empty($box)) {
            foreach ($box as $key => $value) {
                $questionnaire = $entityManager->getRepository(Questionnaire::class)->find($key);
                if ($questionnaire) {
                    $questionnaire->setActive(true);
                    $entityManager->persist($questionnaire);
                }
            }
        }
    
        // Mise à jour des autres questionnaires comme inactifs
        $etablissement = $this->getUser()->getEtablissement();
        $services = $entityManager->getRepository(ServiceEtablissement::class)->findBy(['etablissement' => $etablissement]);
        foreach ($services as $service) {
            $questionnaires = $entityManager->getRepository(Questionnaire::class)->findBy(['service' => $service]);
            foreach ($questionnaires as $questionnaire) {
                if (!isset($box[$questionnaire->getId()])) {
                    $questionnaire->setActive(false);
                    $entityManager->persist($questionnaire);
                }
            }
        }
    
        $entityManager->flush();
    
        return $this->redirectToRoute('app_questionnaire');
    }
    
    

}