<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ServiceEtablissement;
use App\Entity\Chambre;
use App\Entity\ConfigApp;
use App\Entity\Television;
use App\Push\PushRabbit;
use App\Repository\ChambreRepository;

class ChambreController extends AbstractController
{
    #[Route('/chambre', name: 'app_chambre')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getSupportConnect() && $configApp->getEnableSupportConnect()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        $repository = $entityManager->getRepository(Chambre::class);
        $repositorys = $entityManager->getRepository(ServiceEtablissement::class);
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $idetab = $etablissement->getId();
        $chambres  = $repository->findBy(['etablissement' => $etablissement]);
        $ar = array();
        $Manager = new PushRabbit();
        $ar=$Manager->isConnected($idetab,$chambres);
        $serviceetablissement  = $repositorys->findBy(['etablissement' => $etablissement]);
        return $this->render('chambre/index.html.twig', [
            'chambres' => $chambres,'etablissement' =>$etablissement,
            'serviceetablissement'=>$serviceetablissement,'ar'=>$ar,'appConfig' => $appConfig,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/chambre/checkin', name: 'app_checkin')]
    public function checkin(EntityManagerInterface $entityManager): Response
   {
       if( !$this->getUser())
       return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getSupportConnect() && $configApp->getEnableSupportConnect()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        $request = Request::createFromGlobals();
        $repository = $entityManager->getRepository(Chambre::class);
        $etatcheckin = $request->get("etatcheckin");
        $idetablissement = $this->getUser()->getEtablissement()->getId();
        $box = $request->get('box');
        $queues = array();

       // var_dump($box);//die();
       if (isset($box) and !empty($box)) {

           foreach ($box as $key => $k) {

            $boxs  = $repository->findById($key);
               $chambre= $boxs[0]->getNom();
               $queue = $idetablissement . '.' . $chambre . '.service';
               array_push($queues,$queue);          
               if($etatcheckin=="checkin"){
               $boxs[0]->setCheckval('1');
               $boxs[0]->setDrois('1/1/1/1/1/1/1/1/1/1');
               }
               elseif($etatcheckin=="checkout"){
                  $boxs[0]->setCheckval('0');
                  $boxs[0]->setDrois('0/0/0/0/0/0/0/0/0/0');
                  }

        }
     
                 $message = 'update_categories';  
                $Manager = new PushRabbit();
                $Manager->MakeRabbitCall($queues, $message);         
               // var_dump($queues);die();     
           
       }

       return $this->redirectToRoute('app_chambre');
   }
   #[Route('/chambre/redemarrer', name: 'app_redemarrer')]
   public function redemarrer(EntityManagerInterface $entityManager): Response
   {
        $request = Request::createFromGlobals();
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $repository = $entityManager->getRepository(Chambre::class);
        $Acce = $this->getUser()->getRedimarerSupport() && $this->getUser()->getSupportConnect()&& $configApp->getEnableSupportConnect()=="1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        $idetablissement = $this->getUser()->getEtablissement()->getId();
       $box = $request->get('id');
       $queues = array();
       if (isset($box) and !empty($box)) {
            $boxs  = $repository->findById($box);
            $chambre = $boxs[0]->getNom();
            $queue = $idetablissement . '.' . $chambre . '.service';
            array_push($queues,$queue);          
        }
                $message = 'shell%%reboot';  
                $Manager = new PushRabbit();
                $Manager->MakeRabbitCall($queues, $message);         
              // var_dump($queues);die();     
       return $this->redirectToRoute('app_chambre');
   }
   
   #[Route('/chambre/supprimer/{id}', name: 'app_supprimer_chambre')]
   public function supprimerchambre(EntityManagerInterface $entityManager, int $id): Response
   {
       $chambre = $entityManager->getRepository(Chambre::class)->find($id);
       if( !$this->getUser())
       return $this->redirectToRoute('app_login');
       $etablissement = $this->getUser()->getEtablissement();
       $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);       $Acce = $this->getUser()->getSupprimerSupport() && $this->getUser()->getSupportConnect() && $configApp->getEnableSupportConnect()=="1";
       if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');
       }
       if (!$chambre) {
           throw $this->createNotFoundException(
               'No room found for id '.$id
           );
       }

       $entityManager->remove($chambre);
       $entityManager->flush();

       return $this->redirectToRoute('app_chambre');
   }

   #[Route('/chambre/ajouter', name: 'app_ajouter_chambre')]
   public function ajouterchambre(EntityManagerInterface $entityManager): Response
   {
    $request = Request::createFromGlobals();
    if( !$this->getUser())
    return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
    $Acce = $this->getUser()->getAjouteSupport()  && $this->getUser()->getSupportConnect()&& $configApp->getEnableSupportConnect()=="1";
    if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');
    }
    $idetablissement = $etablissement->getId();
    $serviceetablissement  =  $entityManager->getRepository(ServiceEtablissement::class)->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);
    $listechaine  =  $entityManager->getRepository(Television::class)->findBy(['etablissement' => $etablissement], ['numero' => 'ASC']);
    $chambres  =  $entityManager->getRepository(Chambre::class)->findBy(['etablissement' => $etablissement]);
    $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
    
    $listback = [];
    foreach ($chambres as $ch) {
    $background = $ch->getBackground();
    if (!in_array($background, $listback)) {
        $listback[] = $background;
        }
    }

    // si on n'a aucun chambre on va prend fond d'ecran d'etablissement
    if(empty($listback))
    {
        $listback[] =$etablissement->getBackground();
    }
    // dd(empty($listback));
      $nom = $request->get("nom");
      $type = $request->get("typesupport");
      $typetv = $request->get("type");
      // $datedebut=$request->get("datedebut");    
      $ip = $request->get("ip");
      $mac = $request->get("mac");
      $etage = $request->get("etage");
      $service = $request->get("Service");
      $back = $request->get("back");
      $typeaffichage = $request->get("typeaffichage");
      $chaine = $request->get("listechaine");
      
      $sq  =  $entityManager->getRepository(ServiceEtablissement::class)->findById($service);


     


      $drois = '1/1/1/1/1/1/1/1/1/1/';

      if ($type == 'Samsung') {
         $support = 'R-TV';
         
        
       
            } elseif ($type == 'Samsung Tizen') {
               $support = 'R-TV';
               
               
               
            } elseif ($type == 'LG') {
               $support = 'R-LG';
               
               
              
            } elseif ($type == 'Philips') {
               $support = 'R-PH';
               
               
              
            } elseif ($type == 'Box-Ip') {
         $support = 'R-BoxIp';
        
      } elseif ($type == 'Box-Coax') {
         $support = 'R-BoxCoax';
        
      }

      $valider = $request->get("valider");
      //var_dump($sq);die();
      $date ='29/02/2024 11:00:00';
    //   dd($valider);

      if (isset($valider)) {
         $box = new Chambre();
         $box->setEtablissement($etablissement);
         $box->setNom($nom);
         $box->setActive('1');
         $box->setDate($date);
         $box->setSupport($support);
         $box->setType($typetv);
         $box->setMac($mac);
         $box->setEtage($etage);
         $box->setIp($ip);
         $box->setDrois($drois);
         $box->setCheckval('1');
         $box->setLangue('fr');
         $box->setservice($sq[0]);
         $box->setCin(' ');
         $box->setCout(' ');
         $box->setClient(' ');
         $box->setBackground($back);
         $box->settypeaffichage($typeaffichage);
         //var_dump($chaine);
         if ($typeaffichage == "2") {
            $ch  =  $entityManager->getRepository(Television::class)->findById($chaine);
            $box->setchaine($ch[0]);
         }
         $entityManager->persist($box);
         $entityManager->flush();
         return $this->redirectToRoute('app_chambre');
      }
      return $this->render('chambre/ajouter.html.twig', array('listechaine' => $listechaine,
     'listback' => $listback,'appConfig'=>$appConfig,
       'serviceasc' => $serviceetablissement,
        'user' => $this->getUser(),
      ));
   }

   #[Route('/chambre/modifier/{id}', name: 'app_modifier_chambre')]
   public function modifierchambre(EntityManagerInterface $entityManager, int $id): Response
   {
      $request = Request::createFromGlobals();
      if( !$this->getUser())
      return $this->redirectToRoute('app_login');
      $etablissement = $this->getUser()->getEtablissement();
      $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
      $Acce = $this->getUser()->getModifierSupport() && $this->getUser()->getSupportConnect()&& $configApp->getEnableSupportConnect()=="1" ;
      if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');
      }
      $idetablissement = $etablissement->getId();
      $serviceetablissement  =  $entityManager->getRepository(ServiceEtablissement::class)->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);
      $listechaine  =  $entityManager->getRepository(Television::class)->findBy(['etablissement' => $etablissement], ['numero' => 'ASC']);
      $chambres  =  $entityManager->getRepository(Chambre::class)->findBy(['etablissement' => $etablissement]);
      $box = $entityManager->getRepository(chambre::class)->findById($id)[0];
      $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

      //var_dump($box);die();
      $listback = [];
      foreach ($chambres as $ch) {
      $background = $ch->getBackground();
      if (!in_array($background, $listback)) {
          $listback[] = $background;
      }
      }
      // si on n'a aucun chambre on va prend fond d'ecran d'etablissement
      if(empty($listback))
      {
          $listback[] =$etablissement->getBackground();
      }
      $nom = $request->get("nom");
      $type = $request->get("typesupport");
      $typetv = $request->get("type");
      // $datedebut=$request->get("datedebut");    
      $ip = $request->get("ip");
      $mac = $request->get("mac");
      $etage = $request->get("etage");
      $service = $request->get("service");
      $back = $request->get("back");
      $servicex  =  $entityManager->getRepository(ServiceEtablissement::class)->findById($service);
      $typeaffichage = $request->get("typeaffichage");
      $chaine = $request->get("listechaine");
      if ($type == 'Samsung') {
         $support = 'R-TV';
      } elseif ($type == 'LG') {
         $support = 'R-LG';
         $constructeur = 'LG';
      } elseif ($type == 'Philips') {
         $support = 'R-PH';
      } elseif ($type == 'Box-Ip') {
         $support = 'R-BoxIp';
     } elseif ($type == 'Box-Coax') {
         $support = 'R-BoxCoax';
      }

      $valider = $request->get("valider");
      if (isset($valider)) {
         $box->setEtablissement($etablissement);
         $box->setNom($nom);
         $box->setSupport($support);
         $box->setType($typetv);
         $box->setMac($mac);
         $box->setEtage($etage);
         $box->setIp($ip);
         $box->setService($servicex[0]);
         $box->setBackground($back);
         $box->settypeaffichage($typeaffichage);
         if ($typeaffichage == "2") {
            $ch  =  $entityManager->getRepository(Television::class)->findById($chaine);
            $box->setchaine($ch[0]);
         }
         $entityManager->persist($box);
         $entityManager->flush();
         return $this->redirectToRoute('app_chambre');
      }


      $service =   $entityManager->getRepository(ServiceEtablissement::class)->findBy(['etablissement' => $idetablissement], ['nom' => 'ASC']);

      return $this->render('chambre/modifier.html.twig', array('listechaine' => $listechaine, 'box' => $box,
      'service' => $service, 'listback' => $listback,'appConfig'=>$appConfig,
      'user' => $this->getUser(),
   ));
   }
   #[Route('/chambre/liste', name: 'app_liste_chambre')]
   public function boxlist(EntityManagerInterface $entityManager): Response
    {
      $repository = $entityManager->getRepository(Chambre::class);
      if( !$this->getUser())
      return $this->redirectToRoute('app_login');
      $etablissement = $this->getUser()->getEtablissement();
      $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
      $Acce = $this->getUser()->getSupportConnect() && $configApp->getEnableSupportConnect()=="1";
      if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');
      }
      $repositorys = $entityManager->getRepository(ServiceEtablissement::class);
      $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
      $chambres  = $repository->findBy(['etablissement' => $etablissement]);
      $serviceetablissement  = $repositorys->findBy(['etablissement' => $etablissement]);
      return $this->render('chambre/listechambre.html.twig', [
          'chambres' => $chambres,'etablissement' =>$etablissement,'serviceetablissement'=>$serviceetablissement,'appConfig'=>$appConfig,
      ]);
       
    }


    #[Route('/service/ajouter', name: 'app_ajouter_service_etablissement')]
    public function ajouterserviceetablissement(EntityManagerInterface $entityManager): Response
    {
     $request = Request::createFromGlobals();
     if( !$this->getUser())
     return $this->redirectToRoute('app_login');
     $etablissement = $this->getUser()->getEtablissement();
     $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
     $Acce = $this->getUser()->getAjouteSupport() && $this->getUser()->getSupportConnect() && $configApp->getEnableSupportConnect()=="1";
     if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');
         }
       $nom = $request->get("nom");
       if($nom)
       {
          $service = new ServiceEtablissement();
          $service->setId(mt_rand(1, 9999));
          $service->setEtablissement($etablissement);
          $service->setNom($nom);
        //  dd($service);
          $entityManager->persist($service);
          $entityManager->flush();
        }
       
       
 
          return $this->redirectToRoute('app_chambre');
    }

    #[Route('/chambre/message', name: 'app_envoyer_message_chambre')]
    public function envoyermessage(EntityManagerInterface $entityManager)
    {
      $request = Request::createFromGlobals();
      if( !$this->getUser())
      return $this->redirectToRoute('app_login');
      $etablissement = $this->getUser()->getEtablissement();
      $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
      $Acce = $this->getUser()->getMessagePersonnel() && $this->getUser()->getSupportConnect() && $configApp->getEnableSupportConnect()=="1";
      if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');
      }
      $repository = $entityManager->getRepository(Chambre::class);
      $idetab= $etablissement->getId();
      $chambres  = $repository->findBy(['etablissement' => $etablissement]);
      $ar = array();
      $Manager = new PushRabbit();
      $ar=$Manager->isConnected($idetab,$chambres);
      $valider = $request->get('valider');
      if (isset($valider)) {
            $box = $request->get('list');
            $msg = $request->get('Question');
            $idetablissement = $this->getUser()->getEtablissement()->getId();
              
            $date = new \DateTime();
            $dateString = $date->format('Y-m-d H:i:s'); 

 
            $queues = array();
            
            if (isset($box) and !empty($box)) {
                foreach ($box as $key => $k) {
                 

                    $boxs  = $repository->findById($key);
                    $chambre= $boxs[0]->getNom();
                    $queue = $idetablissement . '.' . $chambre . '.service';
                    array_push($queues,$queue);  
                    $boxs[0]->setdate($dateString);
                    $entityManager->persist($boxs[0]);
                    $entityManager->flush();
 
                    
                }
                $message = 'rpost%%'.$msg;  
                $Manager = new PushRabbit();
                $Manager->MakeRabbitCall($queues, $message);  
            }
        

     


            return $this->redirectToRoute('app_envoyer_message_chambre');}

            return $this->render('chambre/message.html.twig', array('boxs' => $chambres,'ar'=>$ar,'appConfig'=>$configApp,
            'user' => $this->getUser(),
         ));
    }


    #[Route('/chambre/update', name: 'app_update_chambre')]
    public function updatechambre(EntityManagerInterface $entityManager): Response
   {
        $request = Request::createFromGlobals();
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getSupportConnect()&& $configApp->getEnableSupportConnect()=="1";
      if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');
      }
        $repository = $entityManager->getRepository(Chambre::class);
        $idetablissement = $this->getUser()->getEtablissement()->getId();
        $box = $request->get('box');
        $queues = array();

        
       if (isset($box) and !empty($box)) {
           foreach ($box as $key => $k) {
               $boxs  = $repository->findById($key);
               $chambre= $boxs[0]->getNom();
               $queue = $idetablissement . '.' . $chambre . '.service';
               array_push($queues,$queue);          
                   }
                 
                $message = 'update_categories';  
                $Manager = new PushRabbit();
                $Manager->MakeRabbitCall($queues, $message);         
                 
           
       }

       return $this->redirectToRoute('home');
   }
   

  
}
