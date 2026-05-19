<?php

namespace App\Controller;

use App\Entity\ConfigApp;
use App\Entity\Categories;
use App\Entity\Favoris;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Support;
use App\Entity\Jeux;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class JeuxController extends AbstractController
{
    #[Route('/jeux', name: 'app_jeux')]
    public function index(EntityManagerInterface $entityManager): Response
    {
      // dd($this->getParameter('jeux_directory'));
      if( !$this->getUser())
      return $this->redirectToRoute('app_login');
      $etablissement = $this->getUser()->getEtablissement();
      $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
      $Acce = $this->getUser()->getJEUX() && $configApp->getEnableJEUX()=="1";
        if (!$Acce) {
          $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
          return $this->redirectToRoute('home');         }
        $support =$etablissement->getSupports();
        $jeuxsCollection = $etablissement->getJeuxes();
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
      $categorieJeux = $entityManager->getRepository(Categories::class)->findOneBy([
        'etablissement' => $etablissement,
        'nom' => 'jeux'
      ]);
      $favorisJeux = $entityManager->getRepository(Favoris::class)->findBy([
        'Etablissement' => $etablissement,
      ]);

      $favorisJeuxIds = [];
      foreach ($favorisJeux as $favori) {
        $isFavorite = false;

        if ($categorieJeux !== null && $favori->getCategorie() === $categorieJeux) {
          $isFavorite = true;
        }

        if ($favori->getNomCategorie() !== null && strtolower($favori->getNomCategorie()) === 'jeux') {
          $isFavorite = true;
        }

        if ($isFavorite && $favori->getIdElement() !== null) {
          $favorisJeuxIds[] = $favori->getIdElement();
        }
      }
        // Convertir la PersistentCollection en tableau PHP
        $jeuxsArray = $jeuxsCollection->toArray();
    
        // Trier les jeuxs par position
        usort($jeuxsArray, function($a, $b) {
            return $a->getPosition() <=> $b->getPosition();
        });
        return $this->render('jeux/index.html.twig', [
            'supports' => $support,
            'jeuxs' => $jeuxsArray,
            'appConfig' => $appConfig,  
            'user' => $this->getUser(),
          'favorisJeuxIds' => $favorisJeuxIds,
        ]);
    }
    


    #[Route('/jeux/ajouter', name: 'app_ajouter_jeux')]
    public function ajouterjeux(EntityManagerInterface $entityManager): Response
    {
     $request = Request::createFromGlobals();
     if( !$this->getUser())
     return $this->redirectToRoute('app_login');
     $etablissement = $this->getUser()->getEtablissement();
     $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

     $Acce = $this->getUser()->getAjouterJeux() && $this->getUser()->getJEUX() && $configApp->getEnableJEUX()=="1";
     if (!$Acce) {
      $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
      return $this->redirectToRoute('home');      }
    //  $support =  $etablissement->getSupports();
     $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
 
      $supports = $entityManager->getRepository(Support::class)->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);
      $categorieJeux = $entityManager->getRepository(Categories::class)->findOneBy([
          'etablissement' => $etablissement,
          'nom' => 'jeux'
      ]);

    $positionsBySupport = [];
    $firstFreePositionBySupport = [];

    foreach ($supports as $support) {
        $protocole = $support->getProtocole();

        $jeuxes = $entityManager->getRepository(Jeux::class)->findBy([
            'protocole' => $protocole,
            'etablissement' => $etablissement
        ]);

        $positions = array_map(function ($app) {
            return $app->getPosition();
        }, $jeuxes);

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
          $this->addFlash('success', 'Le jeu doit d\'abord être ajouté en favori.');
          return $this->render('jeux/ajouter.html.twig', [
            'supports' => $supports,
            'appConfig' => $appConfig,
            'positionsBySupport' => $positionsBySupport,
            'firstFreePositionBySupport' => $firstFreePositionBySupport,
            'formData' => $formData,
          ]);
        }

        if (!$this->canPersistNewFavorite($entityManager, $etablissement)) {
          $this->addFlash('success', 'Vous avez atteint la limite maximale de 6 favoris.');
          return $this->render('jeux/ajouter.html.twig', [
            'supports' => $supports,
            'appConfig' => $appConfig,
            'positionsBySupport' => $positionsBySupport,
            'firstFreePositionBySupport' => $firstFreePositionBySupport,
            'formData' => $formData,
          ]);
        }

        $file1 = $request->files->get('logo');
        if ($file1) {
         $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();   
         $file1->move($this->getParameter('jeux_directory'), $fileName1);
         $fileName = 'images/jeux/' . $fileName1;
 
         }
          $jeux = new Jeux();
          $jeux->setEtablissement($etablissement);
          $jeux->setNom($nom);
          $jeux->setActive($active);
          $jeux->setPackage($package);
          $jeux->setPosition($position);
          $jeux->setProtocole($protocole);
          $jeux->setLogo($fileName);
          $supportId = $entityManager->getRepository(Support::class)->findOneBy(['etablissement'=>$etablissement,'protocole' => $protocole]);

          $entityManager->persist($jeux);
          $entityManager->flush();

        $favoriJeux = new Favoris();
        $favoriJeux->setEtablissement($etablissement);
        $favoriJeux->setCategorie($categorieJeux);
        $favoriJeux->setNomCategorie($categorieJeux?->getNom() ?? 'jeux');
        $favoriJeux->setIdElement($jeux->getId());
        $favoriJeux->setNomElement($jeux->getNom());

        $entityManager->persist($favoriJeux);
        $entityManager->flush();
          //return $this->redirectToRoute('app_jeux');
          return $this->redirectToRoute('app_jeux', ['ongletActif' => $supportId->getId()]);
       }
 
       return $this->render('jeux/ajouter.html.twig', array('supports' => $supports,'appConfig' => $appConfig,'positionsBySupport' => $positionsBySupport,
        'firstFreePositionBySupport' => $firstFreePositionBySupport,
        'formData' => [
          'nom' => '',
          'package' => '',
          'active' => '0',
          'position' => '',
          'protocole' => $supports[0]->getProtocole() ?? '',
          'favoris' => '0',
        ],));
    }

    #[Route('/jeux/modifier/{id}', name: 'app_modifier_jeux')]
    public function modifierjeux(EntityManagerInterface $entityManager, int $id): Response
    {
       $request = Request::createFromGlobals();
       if( !$this->getUser())
       return $this->redirectToRoute('app_login');
       $etablissement = $this->getUser()->getEtablissement();
       $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

       $Acce = $this->getUser()->getModifierJeux() && $this->getUser()->getJEUX() && $configApp->getEnableJEUX()=="1";
       if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');        }
       $support =  $etablissement->getSupports();
       $jeux = $entityManager->getRepository(Jeux::class)->findById($id)[0]; 
       $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);           
         $categorieJeux = $entityManager->getRepository(Categories::class)->findOneBy([
           'etablissement' => $etablissement,
           'nom' => 'jeux'
         ]);
         $favorisJeux = $entityManager->getRepository(Favoris::class)->findBy([
           'Etablissement' => $etablissement,
           'idElement' => $jeux->getId(),
         ]);
         $jeuxFavori = !empty($favorisJeux);
       $valider = $request->get("valider");
       if (isset($valider)) {
        
        $fileName = $jeux->getLogo();
        $nom = $request->get("nom");
        $package = $request->get("package");
        //$active = $request->get("active");
        //$position = $request->get("position");
        $protocole = $request->get("protocole");
        $favoris = $request->get("favoris", '0');
        $file1 = $request->files->get('logo');
        if ($file1) {
         $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();   
         $file1->move($this->getParameter('jeux_directory'), $fileName1);
         $fileName = 'images/jeux/' . $fileName1;
 
         }
         $supportId = $entityManager->getRepository(Support::class)->findOneBy(['etablissement'=>$etablissement,'protocole' => $protocole]);

          $jeux->setEtablissement($etablissement);
          $jeux->setNom($nom);
          //$jeux->setActive($active);
          $jeux->setPackage($package);
          //$jeux->setPosition($position);
          $jeux->setProtocole($protocole);
          $jeux->setLogo($fileName);
         
          $entityManager->persist($jeux);

            if ((string) $favoris === '1') {
              if (empty($favorisJeux)) {
                if (!$this->canPersistNewFavorite($entityManager, $etablissement)) {
                  $this->addFlash('success', 'Vous avez atteint la limite maximale de 6 favoris.');
                } else {
                  $favoriJeux = new Favoris();
                  $favoriJeux->setEtablissement($etablissement);
                  $favoriJeux->setIdElement($jeux->getId());

                  $favoriJeux->setCategorie($categorieJeux);
                  $favoriJeux->setNomCategorie($categorieJeux?->getNom() ?? 'jeux');
                  $favoriJeux->setNomElement($jeux->getNom());

                  $entityManager->persist($favoriJeux);
                }
              } else {
                foreach ($favorisJeux as $favoriJeu) {
                  $favoriJeu->setCategorie($categorieJeux);
                  $favoriJeu->setNomCategorie($categorieJeux?->getNom() ?? 'jeux');
                  $favoriJeu->setNomElement($jeux->getNom());

                  $entityManager->persist($favoriJeu);
                }
              }
            } elseif (!empty($favorisJeux)) {
              foreach ($favorisJeux as $favoriJeu) {
                $entityManager->remove($favoriJeu);
              }
            }

          $entityManager->flush();
          //return $this->redirectToRoute('app_jeux');
          return $this->redirectToRoute('app_jeux', ['ongletActif' => $supportId->getId()]);
     
       }
 
 
     
 
       return $this->render('jeux/modifier.html.twig', array('jeux' => $jeux,'supports' => $support,
       'appConfig' => $appConfig,
       'jeuxFavori' => $jeuxFavori
      ));
    }
 
    #[Route('/jeux/supprimer/{id}', name: 'app_supprimer_jeux')]
    public function supprimerjeux(EntityManagerInterface $entityManager, int $id): Response
    {
      if( !$this->getUser())
      return $this->redirectToRoute('app_login');
      $etablissement = $this->getUser()->getEtablissement();
      $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

      $Acce = $this->getUser()->getSupprimerJeux() && $this->getUser()->getJEUX() && $configApp->getEnableJEUX()=="1";
       if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');        }
        $jeux = $entityManager->getRepository(Jeux::class)->find($id);
        if (!$jeux) {
            throw $this->createNotFoundException(
                'No jeux found for id '.$id
            );
        }

        $categorieJeux = $entityManager->getRepository(Categories::class)->findOneBy([
          'etablissement' => $etablissement,
          'nom' => 'jeux'
        ]);

        $favoris = $entityManager->getRepository(Favoris::class)->findBy([
          'Etablissement' => $etablissement,
          'idElement' => $jeux->getId(),
        ]);

        foreach ($favoris as $favori) {
          if ($categorieJeux === null || $favori->getCategorie() === $categorieJeux || strtolower((string) $favori->getNomCategorie()) === 'jeux') {
            $entityManager->remove($favori);
          }
        }

        $entityManager->remove($jeux);
        $entityManager->flush();
 
        return $this->redirectToRoute('app_jeux');
    }

    private function canPersistNewFavorite(EntityManagerInterface $entityManager, $etablissement): bool
    {
      $totalFavoris = $entityManager->getRepository(Favoris::class)->count([
        'Etablissement' => $etablissement,
      ]);

      return $totalFavoris < 6;
    }

    #[Route('/jeux/modifierhome', name: 'app_modifier_homejeux')]
    public function modifierhomejeux(Request $request,EntityManagerInterface $entityManager)
    {
      if( !$this->getUser())
      return $this->redirectToRoute('app_login');
      $etablissement = $this->getUser()->getEtablissement();
      $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

      $Acce = $this->getUser()->getSauvegarderJeux() && $this->getUser()->getJEUX() && $configApp->getEnableJEUX()=="1";
      if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');       }
      $jeuxs =  $this->getUser()->getEtablissement()->getJeuxes();
  
      foreach ($jeuxs as $jeux) {
        $jeux->setActive('0');
        $entityManager->persist($jeux);
        $entityManager->flush();
      }
  
  
      $listeactive = $request->get('listeactive');
  
      if (isset($listeactive) and !empty($listeactive)) {
        foreach ($listeactive as $key => $k) {
  
          
          $that = $entityManager->getRepository(Jeux::class)->findById($key);   
  
          $that[0]->setActive("1");
          $entityManager->persist($that[0]);
          $entityManager->flush();
        }
      }
  
      $listeposition = $request->get('listeposition');
  
      if (isset($listeposition) and !empty($listeposition)) {
        foreach ($listeposition as $key => $k) {
  
            $that2 = $entityManager->getRepository(Jeux::class)->findById($key);   
  
          $that2[0]->setPosition($k);
          $entityManager->persist($that2[0]);
          $entityManager->flush();
        }
      }
  
  
      
    
     // Récupérer l'onglet actif depuis la requête
     //$ongletActif = $request->query->get('ongletActif');
     $ongletActif = $request->request->get('ongletActif');


     // Ajouter l'onglet actif comme paramètre de la redirection
     return $this->redirectToRoute('app_jeux', ['ongletActif' => $ongletActif]);
 
      
    }
  
  
}
