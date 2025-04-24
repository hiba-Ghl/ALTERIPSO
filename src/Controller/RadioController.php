<?php

namespace App\Controller;

use App\Entity\CategorieRadio;
use App\Entity\Chambre;
use App\Entity\ConfigApp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etablissement;
use App\Entity\LancerAnnonce;
use App\Entity\LancerRadio;
use App\Entity\Lancerservice;
use App\Entity\LancerTV;
use App\Entity\Radio;
use App\Push\PushRabbit;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class RadioController extends AbstractController
{


    #[Route('/radio', name: 'app_radio')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

        $Acce = $this->getUser()->getRADIO() && $configApp->getEnableRADIO()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        $repository = $entityManager->getRepository(Radio::class);
        $radio  = $repository->findBy(['etablissement' => $etablissement],['nom' => 'ASC']);
        $categorieradio =  $entityManager->getRepository(CategorieRadio::class)->findBy(['etablissement' => $etablissement],['nom' => 'ASC']);
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $chembre = $entityManager->getRepository(Chambre::class)->findBy(['etablissement' =>$etablissement]);
        $chembreArray = [];
        foreach ($chembre as $chambre) {
            $chembreArray[] = ['id'=>$chambre->getId(),'nom' => $chambre->getNom(),
                        'ip' => $chambre->getIp(),
                        'Mac' => $chambre->getMac(),
        ];
        }
        $chembreJson = json_encode($chembreArray);
        return $this->render('radio/index.html.twig', [
            'radio' => $radio , 'categorieradio' => $categorieradio,'appConfig' => $appConfig,'user' => $this->getUser(),
            'chembre' => $chembreJson,

        ]);
    }

    #[Route('/ajouterradio', name: 'ajouter_radio')]
    public function ajouterradio(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

        $Acce = $user->getAjoutRadio() && $user->getRADIO() && $configApp->getEnableRADIO()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
    
        $request = Request::createFromGlobals();

        $nom = $request->get("nom");
        $ip = $request->get("ip");
        $port = $request->get("port");
        //$numero = $request->get("numero");
        $pays = $request->get("pays");
        $protocole = $request->get("protocole");
        $active = $request->get("active");
        $catradio = $request->get("catradio");
        $categorieradio =  $entityManager->getRepository(CategorieRadio::class)->findById($catradio)[0];
    

       $file1 = $request->files->get('logo');
       

       // Vérifiez si les fichiers ont été téléchargés
       if ($file1) {
           // Traitez les fichiers comme vous le souhaitez
           $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
           

           // Déplacez les fichiers téléchargés vers le dossier de destination
           $file1->move($this->getParameter('radio_directory'), $fileName1);
          

           // Répondre avec un message de succès ou rediriger vers une autre page
         //  return new Response('Fichiers téléchargés avec succès !');
         $fileName = 'images/radio/' . $fileName1;
       }
       else 
       $fileName = 'images/no_image.png';
       
       
       

       
        $radio = new Radio();
        
        $radio->setEtablissement($etablissement);
        $radio->setNom($nom);
        $radio->setIp($ip);
        $radio->setPort($port);
       $radio->setCategorie($categorieradio);
       $radio->setPays($pays);
       $radio->setPays($pays);
       $radio->setProtocole($protocole);
       $radio->setLogo($fileName);
        if (isset($active) and !empty($active)) 
           $radio->setActive(1);
       else 
           $radio->setActive(0);
       

        $entityManager->persist($radio);
        $entityManager->flush();

        return $this->redirectToRoute('app_radio');
        
    }

    #[Route('/radio/supprimer/{id}', name: 'supprimer_radio')]
    public function supprimerradio(EntityManagerInterface $entityManager, int $id): Response
    {
       $radio = $entityManager->getRepository(Radio::class)->find($id);
       if( !$this->getUser())
       return $this->redirectToRoute('app_login');
       $etablissement = $this->getUser()->getEtablissement();
       $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
       $Acce = $this->getUser()->getSupprimerRadio() && $this->getUser()->getRADIO() && $configApp->getEnableRADIO()=="1";
       if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');       }
        if (!$radio) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $entityManager->remove($radio);
        $entityManager->flush();

        return $this->redirectToRoute('app_radio');
    }

    #[Route('/modifierradio', name: 'modifier_radio')]
    public function modifierradio(EntityManagerInterface $entityManager): Response
    {   
        $repository = $entityManager->getRepository(Radio::class);
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getModifierRadio() && $this->getUser()->getRADIO() && $configApp->getEnableRADIO()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
       $radio  = $repository->findBy(['etablissement' => $etablissement]);
       // var_dump($radio);die();
        foreach ($radio as $tele) {
            $tele->setActive('0');
            $entityManager->persist($tele);
            $entityManager->flush();
          }
        $request = Request::createFromGlobals();

      
    
        $nom = $request->get("listenom");
                 if (isset($nom) and !empty($nom)) {
                    foreach ($nom as $key => $k) {
                        $nom_television  = $repository->findById($key);
                         $nom_television[0]->setNom($k);
                         $entityManager->persist($nom_television[0]);
                         $entityManager->flush();
                    }
                 }
        $ip = $request->get("listeip");
            if (isset($ip) and !empty($ip)) {
                foreach ($ip as $key => $k) {
                    $ip_television  = $repository->findById($key);
                    $ip_television[0]->setIp($k);
                    $entityManager->persist($ip_television[0]);
                    $entityManager->flush();
                }
            }
        $port = $request->get("listeport");
            if (isset($port) and !empty($port)) {
                foreach ($port as $key => $k) {
                    $port_television  = $repository->findById($key);
                    $port_television[0]->setPort($k);
                    $entityManager->persist($port_television[0]);
                    $entityManager->flush();
                }
            }
        $catradio = $request->get("listcatradio");
            if (isset($catradio) and !empty($catradio)) {
                foreach ($catradio as $key => $k) {
                    $catradio  = $repository->findById($key);
                    $kc =  $entityManager->getRepository(CategorieRadio::class)->findById($k)[0];
                    $catradio[0]->setCategorie($kc);
                    $entityManager->persist($catradio[0]);
                    $entityManager->flush();
                }
            }
        $pays = $request->get("listepays");
            if (isset($pays) and !empty($pays)) {
                foreach ($pays as $key => $k) {
                    $pays_television  = $repository->findById($key);
                    $pays_television[0]->setPays($k);
                    $entityManager->persist($pays_television[0]);
                    $entityManager->flush();
                }
            }
        $protocole = $request->get("listeprotocole");
            if (isset($protocole) and !empty($protocole)) {
                foreach ($protocole as $key => $k) {
                    $protocole_television  = $repository->findById($key);
                    $protocole_television[0]->setProtocole($k);
                    $entityManager->persist($protocole_television[0]);
                    $entityManager->flush();
                }
            }
        $active = $request->get("listeactive");
            if (isset($active) and !empty($active)) {
                foreach ($active as $key => $k) {
                    $active_television  = $repository->findById($key);
                    $active_television[0]->setActive("1");
                    $entityManager->persist($active_television[0]);
                    $entityManager->flush();
                }
            }
      
        $file1 = $request->files->get('listelogo');
            if (isset($file1) and !empty($file1)) {
                foreach ($file1 as $key => $k) {
                $thatlistfile = $repository->findById($key);
                if (!empty($k)) {
                    $fileName = md5(uniqid()) . '.' . $k->guessExtension();
                    $k->move($this->getParameter('radio_directory'), $fileName);
                    $fileName = 'images/radio/' . $fileName;
                } else {
                    $fileName = $thatlistfile[0]->getLogo();
                }
                $thatlistfile[0]->setLogo($fileName);
                $entityManager->persist($thatlistfile[0]);
                $entityManager->flush();
                }
            }
         
       
            
       

       
      

        return $this->redirectToRoute('app_radio');
        
    }

   
    private function entityToArray($entity) {
        $getterMethods = get_class_methods($entity);
        $data = [];
        foreach ($getterMethods as $method) {
            if (strpos($method, 'get') === 0 && $method !== 'getId') {
                $property = lcfirst(substr($method, 3));
                $value = $entity->$method();
                $data[$property] = $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value;
            }
        }
        $data['id'] = $entity->getId();
        return $data;
     }        
        #[Route('/radio/LancerRadio/{idRadio}', name: 'LancerRadio')]
        public function lancerRadio(EntityManagerInterface $entityManager, int $idRadio)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
            if( !$this->getUser())
                return $this->redirectToRoute('app_login');
            $etablissement = $this->getUser()->getEtablissement();
            $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        //     $Acce = $this->getUser()->getSERVICE() && $this->getUser()->getLancerArretService() && $configApp->getEnableSERVICE() === '1';
        //  if (!$Acce) {
        //      $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        //      return $this->redirectToRoute('home');
        //  }
            $repository = $entityManager->getRepository(Chambre::class);
            $queues = array();
            $radio = $entityManager->getRepository(Radio::class)
                ->findOneBy(['etablissement' => $etablissement, 'id' => $idRadio]);
            if (!$radio) {
                return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
            }
            $request = Request::createFromGlobals();
            $chambre = $request->get("chambre");
            $check = $request->get("checked");
            if (isset($chambre) and !empty($chambre) and isset($check) and !empty($check)) {
                foreach ($chambre as $key => $k) {
                    foreach($check as $key1 => $k1)
                    {
                        if($k == $k1)
                        {
                            $lancer = $entityManager->getRepository(LancerRadio::class)->findOneBy(['idChembre' => $k]);
                            if($lancer)
                            {
                                $entityManager->remove($lancer);
                                $entityManager->flush();
                            }
                            $lancer = new LancerRadio();
                            $lancer->setIdChembre($k);
                            $lancer->setIdRadio($idRadio);
                            $entityManager->persist($lancer);
                            $entityManager->flush();
                            $boxs  = $repository->findById($k);
                            $chambre= $boxs[0]->getNom();
                            $queue = $etablissement->getId() . '.' . $chambre . '.service';
                            array_push($queues,$queue);
                            $idradio = $radio->getId();
                            $message = "radio%%".$idradio."%%";
                            $Manager = new PushRabbit();
                            $Manager->MakeRabbitCall($queues, $message); 
                    } 
                }
            }
        }
            return $this->redirectToRoute('app_radio');
    }
#[Route('/radio/ArreteRadio/{idRadio}',name:'ArretRadio')]
public function RemoveRadio(EntityManagerInterface $entityManager, int $idRadio)
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    if( !$this->getUser())
     return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
//     $Acce = $this->getUser()->getSERVICE() && $this->getUser()->getLancerArretService() && $configApp->getEnableSERVICE() === '1';
//  if (!$Acce) {
//      $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
//      return $this->redirectToRoute('home');
//  }
    $repository = $entityManager->getRepository(Chambre::class);
    $queues = array();
    $annonce = $entityManager->getRepository(Radio::class)
        ->findOneBy(['etablissement' => $etablissement, 'id' => $idRadio]);
    if (!$annonce) {
        return new JsonResponse(['error' => 'annonce not found'], Response::HTTP_NOT_FOUND);
    }
    $request = Request::createFromGlobals();
    $chambre = $request->get("chambre");
    $check = $request->get("checked");
    if (isset($chambre) and !empty($chambre) and isset($check) and !empty($check)) {
        foreach ($chambre as $key => $k) {
            foreach($check as $key1 => $k1)
            {
                if($k == $k1)
                {
                $lancer = $entityManager->getRepository(LancerRadio::class)->findOneBy(['idRadio'=>$idRadio,'idChembre' => $k]);
                if($lancer)
                {
                $entityManager->remove($lancer);
                $entityManager->flush();
                $boxs  = $repository->findById($k);
                $chambre= $boxs[0]->getNom();
                $queue = $etablissement->getId() . '.' . $chambre . '.service';
                array_push($queues,$queue);  
                $message = "arreter_radio%%".$idRadio."%%".$k;
;               $Manager = new PushRabbit();
                $Manager->MakeRabbitCall($queues, $message);
                }

            }
    }
}
}
    return $this->redirectToRoute('app_annonce');
}



#[Route('/radio/GetAllchambreLancerRadio/{idRadio}',name:'GetAllchambreLancerRadio',methods:'GET')]

public function GetAllchambreLancerRadio(EntityManagerInterface $entityManager, int $idRadio){
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    if( !$this->getUser())
        return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
//     $Acce = $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
//  if (!$Acce) {
//      $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
//      return $this->redirectToRoute('home');
//  }
    $annonce = $entityManager->getRepository(Radio::class)
        ->findOneBy(['etablissement' => $etablissement, 'id' => $idRadio]);
    if (!$annonce) {
        return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
    }
    $lancer = $entityManager->getRepository(LancerRadio::class)->findBy(['idRadio'=>$idRadio]);
    $arrayLancerRadio = [];
    foreach ($lancer as $l) {
        $ex = $entityManager->getRepository(Chambre::class)->findOneBy(["id"=>$l->getIdChembre()]);
        $arrayLancerRadio[] = ['id'=>$ex->getId(),'nom' => $ex->getNom(),
                        'ip' => $ex->getIp(),
                        'Mac' => $ex->getMac(),];
        }
        $LancerRadioJson = json_encode($arrayLancerRadio);

    return new JsonResponse(['RadioLancer' => $arrayLancerRadio]);
}


}


