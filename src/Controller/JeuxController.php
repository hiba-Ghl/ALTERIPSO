<?php

namespace App\Controller;

use App\Entity\ConfigApp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Support;
use App\Entity\Jeux;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
class JeuxController extends AbstractController
{
    #[Route('/jeux', name: 'app_jeux')]
    public function index(EntityManagerInterface $entityManager): Response
    {
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
     $support =  $etablissement->getSupports();
     $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
 
     
      $valider = $request->get("valider");

       if (isset($valider)) {

        $fileName = 'images/no_image.png';
        $nom = $request->get("nom");
        $package = $request->get("package");
        $active = $request->get("active");
        $position = $request->get("position");
        $protocole = $request->get("protocole");
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
         
          $entityManager->persist($jeux);
          $entityManager->flush();
          //return $this->redirectToRoute('app_jeux');
          return $this->redirectToRoute('app_jeux', ['ongletActif' => $protocole]);
       }
 
       return $this->render('jeux/ajouter.html.twig', array('supports' => $support,'appConfig' => $appConfig));
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
       $valider = $request->get("valider");
       if (isset($valider)) {
        
        $fileName = $jeux->getLogo();
        $nom = $request->get("nom");
        $package = $request->get("package");
        //$active = $request->get("active");
        //$position = $request->get("position");
        $protocole = $request->get("protocole");
        $file1 = $request->files->get('logo');
        if ($file1) {
         $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();   
         $file1->move($this->getParameter('jeux_directory'), $fileName1);
         $fileName = 'images/jeux/' . $fileName1;
 
         }
          
          $jeux->setEtablissement($etablissement);
          $jeux->setNom($nom);
          //$jeux->setActive($active);
          $jeux->setPackage($package);
          //$jeux->setPosition($position);
          $jeux->setProtocole($protocole);
          $jeux->setLogo($fileName);
         
          $entityManager->persist($jeux);
          $entityManager->flush();
          //return $this->redirectToRoute('app_jeux');
          return $this->redirectToRoute('app_jeux', ['ongletActif' => $protocole]);
     
       }
 
 
     
 
       return $this->render('jeux/modifier.html.twig', array('jeux' => $jeux,'supports' => $support,
       'appConfig' => $appConfig
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
        $entityManager->remove($jeux);
        $entityManager->flush();
 
        return $this->redirectToRoute('app_jeux');
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
      $jeuxs =  $this->getUser()->getEtablissement()->getApplications();
  
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
      //  var_dump($box2);die();
  
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

     //var_dump($ongletActif);die();

     // Ajouter l'onglet actif comme paramètre de la redirection
     return $this->redirectToRoute('app_jeux', ['ongletActif' => $ongletActif]);
 
      
    }
  
  
}
