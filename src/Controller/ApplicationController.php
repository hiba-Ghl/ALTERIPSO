<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Support;
use App\Entity\Application;
use App\Entity\ConfigApp;
use App\Entity\Categories;
use App\Entity\Favoris;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
class ApplicationController extends AbstractController
{
    /*#[Route('/application', name: 'app_application')]
    public function index(EntityManagerInterface $entityManager): Response
    {
      
     $support =  $this->getUser()->getEtablissement()->getSupports();
     $application =  $this->getUser()->getEtablissement()->getApplications();
         return $this->render('application/index.html.twig', [
            'supports' => $support,'applications' => $application
        ]);
    }*/
    #[Route('/application', name: 'app_application')]
    public function index(EntityManagerInterface $entityManager): Response
    {
      if( !$this->getUser())
      return $this->redirectToRoute('app_login');
      $etablissement = $this->getUser()->getEtablissement();
      $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
      $support = $etablissement->getSupports();
        $Acce = $this->getUser()->getAPPLICATION()  && $configApp->getEnableAPPLICATION() == "1";
     if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');     }
        $applicationsCollection = $etablissement->getApplications();
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);
        $categorieApplication = $entityManager->getRepository(Categories::class)->findOneBy([
            'etablissement' => $etablissement,
            'nom' => 'application'
        ]);
        $favorisApplication = $entityManager->getRepository(Favoris::class)->findBy([
            'Etablissement' => $etablissement,
        ]);

        $favorisApplicationIds = [];
        foreach ($favorisApplication as $favori) {
            $isApplicationFavorite = false;

            if ($categorieApplication !== null && $favori->getCategorie() === $categorieApplication) {
                $isApplicationFavorite = true;
            }

            if ($favori->getNomCategorie() !== null && strtolower($favori->getNomCategorie()) === 'application') {
                $isApplicationFavorite = true;
            }

            if ($isApplicationFavorite && $favori->getIdElement() !== null) {
                $favorisApplicationIds[] = $favori->getIdElement();
            }
        }
    
        
        // Convertir la PersistentCollection en tableau PHP
        $applicationsArray = $applicationsCollection->toArray();
    
        
        // Trier les applications par position
        usort($applicationsArray, function($a, $b) {
            return $a->getPosition() <=> $b->getPosition();
        });
        return $this->render('application/index.html.twig', [
            'supports' => $support,
            'applications' => $applicationsArray,
            'appConfig' =>$appConfig,
            'favorisApplicationIds' => $favorisApplicationIds,
        ]);
    }
    


 #[Route('/application/ajouter', name: 'app_ajouter_application')]
public function ajouterapplication(EntityManagerInterface $entityManager): Response
{
    $request = Request::createFromGlobals();

    if (!$this->getUser()) {
        return $this->redirectToRoute('app_login');
    }

    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

    $Acce = $this->getUser()->getAjoutApp() && $this->getUser()->getAPPLICATION() && $configApp?->getEnableAPPLICATION() == "1";
    if (!$Acce) {
        $this->addFlash('success', "Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');
    }
    $supports = $entityManager->getRepository(Support::class)->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);

    $positionsBySupport = [];
    $firstFreePositionBySupport = [];

    foreach ($supports as $support) {
        $protocole = $support->getProtocole();

        $Applicationes = $entityManager->getRepository(Application::class)->findBy([
            'protocole' => $protocole,
            'etablissement' => $etablissement
        ]);

        $positions = array_map(function ($app) {
            return $app->getPosition();
        }, $Applicationes);

        $positionsBySupport[$protocole] = $positions;

        $firstFreePosition = 1;
        while (in_array($firstFreePosition, $positions)) {
            $firstFreePosition++;
        }

        $firstFreePositionBySupport[$protocole] = $firstFreePosition;
    }

    $valider = $request->get("valider");

    if (isset($valider)) {
        $fileName = 'images/no_image.png';
        $nom = $request->get("nom");
        $package = $request->get("package");
        $active = $request->get("active");
        $position = $request->get("position");
        $protocole = $request->get("protocole");
        $favoris = $request->get("favoris", '0');

        $formData = [
            'nom' => $nom,
            'package' => $package,
            'active' => $active,
            'position' => $position,
            'protocole' => $protocole,
            'favoris' => '0',
        ];

        if ((string) $favoris !== '1') {
            $this->addFlash('success', 'L\'application doit d\'abord être ajoutée en favori.');
            return $this->render('application/ajouter.html.twig', [
                'supports' => $supports,
                'appConfig' => $configApp,
                'positionsBySupport' => $positionsBySupport,
                'firstFreePositionBySupport' => $firstFreePositionBySupport,
                'formData' => $formData,
            ]);
        }

        if (!$this->canPersistNewFavorite($entityManager, $etablissement)) {
            $this->addFlash('success', 'Vous avez atteint la limite maximale de 6 favoris.');
            return $this->render('application/ajouter.html.twig', [
                'supports' => $supports,
                'appConfig' => $configApp,
                'positionsBySupport' => $positionsBySupport,
                'firstFreePositionBySupport' => $firstFreePositionBySupport,
                'formData' => $formData,
            ]);
        }

        $file1 = $request->files->get('logo');
        if ($file1) {
            $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();   
            $file1->move($this->getParameter('application_directory'), $fileName1);
            $fileName = 'images/application/' . $fileName1;
        }

        $application = new Application();
        $application->setEtablissement($etablissement);
        $application->setNom($nom);
        $application->setActive($active);
        $application->setPackage($package);
        $application->setPosition($position);
        $application->setProtocole($protocole);
        $application->setLogo($fileName);

        $supportId = $entityManager->getRepository(Support::class)->findOneBy([
            'etablissement' => $etablissement,
            'protocole' => $protocole
        ]);

        $entityManager->persist($application);
        $entityManager->flush();

        $categorieApplication = $entityManager->getRepository(Categories::class)->findOneBy([
            'etablissement' => $etablissement,
            'nom' => 'application'
        ]);

        $favori = new Favoris();
        $favori->setEtablissement($etablissement);
        $favori->setCategorie($categorieApplication);
        $favori->setNomCategorie($categorieApplication?->getNom() ?? 'application');
        $favori->setIdElement($application->getId());
        $favori->setNomElement($application->getNom());

        $entityManager->persist($favori);
        $entityManager->flush();

        return $this->redirectToRoute('app_application', ['ongletActif' => $supportId->getId()]);
    }

    return $this->render('application/ajouter.html.twig', [
        'supports' => $supports,
        'appConfig' => $configApp,
        'positionsBySupport' => $positionsBySupport,
        'firstFreePositionBySupport' => $firstFreePositionBySupport,
        'formData' => [
            'nom' => '',
            'package' => '',
            'active' => '0',
            'position' => '',
            'protocole' => $supports[0]->getProtocole() ?? '',
            'favoris' => '0',
        ],
    ]);
}

    #[Route('/application/modifier/{id}', name: 'app_modifier_application')]
    public function modifierapplication(EntityManagerInterface $entityManager, int $id): Response
    {
       $request = Request::createFromGlobals();
       if( !$this->getUser())
       return $this->redirectToRoute('app_login');
       $etablissement = $this->getUser()->getEtablissement();
       $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
       $Acce = $this->getUser()->getModifierApp() && $this->getUser()->getAPPLICATION() && $configApp->getEnableAPPLICATION() == "1";
       if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');
       }
       $support =   $etablissement->getSupports();
       $application = $entityManager->getRepository(Application::class)->findById($id)[0]; 
       $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);           
       $categorieApplication = $entityManager->getRepository(Categories::class)->findOneBy([
           'etablissement' => $etablissement,
           'nom' => 'application'
       ]);
       $favoriApplication = $entityManager->getRepository(Favoris::class)->findOneBy([
           'Etablissement' => $etablissement,
           'idElement' => $application->getId(),
       ]);
       $applicationFavori = $favoriApplication !== null;
       $valider = $request->get("valider");
       if (isset($valider)) {
        
        $fileName = $application->getLogo();
        $nom = $request->get("nom");
        $package = $request->get("package");
        //$active = $request->get("active");
        //$position = $request->get("position");
        $protocole = $request->get("protocole");
        $favoris = $request->get("favoris", '0');
        $file1 = $request->files->get('logo');
        if ($file1) {
         $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();   
         $file1->move($this->getParameter('application_directory'), $fileName1);
         $fileName = 'images/application/' . $fileName1;
 
         }
          
          $application->setEtablissement($etablissement);
          $application->setNom($nom);
          //$application->setActive($active);
          $application->setPackage($package);
          //$application->setPosition($position);
          $application->setProtocole($protocole);
          $supportId = $entityManager->getRepository(Support::class)->findOneBy(['etablissement'=>$etablissement,'protocole' => $protocole]);

          $application->setLogo($fileName);
         
          $entityManager->persist($application);
          
          if ((string) $favoris === '1') {
              if ($favoriApplication === null && !$this->canPersistNewFavorite($entityManager, $etablissement)) {
                  $this->addFlash('success', 'Vous avez atteint la limite maximale de 6 favoris.');
              } else {
                  if ($favoriApplication === null) {
                      $favoriApplication = new Favoris();
                      $favoriApplication->setEtablissement($etablissement);
                      $favoriApplication->setIdElement($application->getId());
                  }

                  $favoriApplication->setCategorie($categorieApplication);
                  $favoriApplication->setNomCategorie($categorieApplication?->getNom() ?? 'application');
                  $favoriApplication->setNomElement($application->getNom());

                  $entityManager->persist($favoriApplication);
              }
          } elseif ($favoriApplication !== null) {
              $entityManager->remove($favoriApplication);
          }

          $entityManager->flush();

          return $this->redirectToRoute('app_application', ['ongletActif' => $supportId->getId()]);
     
       }
         return $this->render('application/modifier.html.twig', array('application' => $application,'supports' => $support,'appConfig' =>$appConfig, 'applicationFavori' => $applicationFavori));
    }
 
    #[Route('/application/supprimer/{id}', name: 'app_supprimer_application')]
    public function supprimerapplication(EntityManagerInterface $entityManager, int $id): Response
    {
      if( !$this->getUser())
      return $this->redirectToRoute('app_login');
      $etablissement = $this->getUser()->getEtablissement();
      $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $application = $entityManager->getRepository(Application::class)->find($id);
        $Acce = $this->getUser()->getSupprimerApp() && $this->getUser()->getAPPLICATION() && $configApp->getEnableAPPLICATION() == "1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
        if (!$application) {
            throw $this->createNotFoundException(
                'No application found for id '.$id
            );
        }

        $categorieApplication = $entityManager->getRepository(Categories::class)->findOneBy([
            'etablissement' => $etablissement,
            'nom' => 'application'
        ]);

        $favoris = $entityManager->getRepository(Favoris::class)->findBy([
            'Etablissement' => $etablissement,
            'idElement' => $application->getId(),
        ]);

        foreach ($favoris as $favori) {
            if ($categorieApplication === null || $favori->getCategorie() === $categorieApplication || strtolower((string) $favori->getNomCategorie()) === 'application') {
                $entityManager->remove($favori);
            }
        }
 
        $entityManager->remove($application);
        $entityManager->flush();
 
        return $this->redirectToRoute('app_application');
    }

    #[Route('/application/modifierhome', name: 'app_modifier_homeapplication')]
    public function modifierhomeapplication(Request $request,EntityManagerInterface $entityManager)
    {
    
      if( !$this->getUser())
      return $this->redirectToRoute('app_login');
      $etablissement = $this->getUser()->getEtablissement();
      $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
      $Acce = $this->getUser()->getModifierApp() && $this->getUser()->getAPPLICATION() && $configApp->getEnableAPPLICATION() == "1";
      if (!$Acce) {
          $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
          return $this->redirectToRoute('home');        }
      $applications =  $this->getUser()->getEtablissement()->getApplications();
      foreach ($applications as $application) {
        $application->setActive('0');
        $entityManager->persist($application);
        $entityManager->flush();
      }
  
  
      $listeactive = $request->get('listeactive');
  
      if (isset($listeactive) and !empty($listeactive)) {
        foreach ($listeactive as $key => $k) {
  
          
          $that = $entityManager->getRepository(Application::class)->findById($key);   
  
          $that[0]->setActive("1");
          $entityManager->persist($that[0]);
          $entityManager->flush();
        }
      }
  
      $listeposition = $request->get('listeposition');
  
      if (isset($listeposition) and !empty($listeposition)) {
        foreach ($listeposition as $key => $k) {
  
            $that2 = $entityManager->getRepository(Application::class)->findById($key);   
  
          $that2[0]->setPosition($k);
          $entityManager->persist($that2[0]);
          $entityManager->flush();
        }
      }
  
  
      
    
     // Récupérer l'onglet actif depuis la requête
     //$ongletActif = $request->query->get('ongletActif');
     $ongletActif = $request->request->get('ongletActif');


     // Ajouter l'onglet actif comme paramètre de la redirection
     return $this->redirectToRoute('app_application', ['ongletActif' => $ongletActif]);
 
      
    }

    private function canPersistNewFavorite(EntityManagerInterface $entityManager, $etablissement): bool
    {
        $totalFavoris = $entityManager->getRepository(Favoris::class)->count([
            'Etablissement' => $etablissement,
        ]);

        return $totalFavoris < 6;
    }
  
  
}
