<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Entity\Categories;
use App\Entity\ConfigApp;
use App\Entity\Favoris;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etablissement;
use App\Entity\Historiquegratuite;
use App\Entity\LancerAnnonce;
use App\Entity\LancerRadio;
use App\Entity\Lancerservice;
use App\Entity\LancerTV;
use App\Entity\Television;
use App\Push\PushRabbit;
use Symfony\Component\HttpFoundation\JsonResponse;

class TelevisionController extends AbstractController
{
    #[Route('/television', name: 'app_television')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
          return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

        $Acce = $this->getUser()->getTELEVISION() && $configApp->getEnableTELEVISION()=="1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
        $repository = $entityManager->getRepository(Television::class);
        $categorieTelevision = $this->getTelevisionCategory($entityManager, $etablissement);

        $idetablissement = $etablissement->getId();
        $television  = $repository->findBy(['etablissement' => $etablissement],['numero' => 'ASC']);
        $favorisTelevision = $entityManager->getRepository(Favoris::class)->findBy(['Etablissement' => $etablissement]);
        $hasTelevisionCategoryBackfill = false;
        if ($categorieTelevision !== null) {
            foreach ($favorisTelevision as $favori) {
                $isTelevisionFavorite = $favori->getNomCategorie() !== null && strtolower($favori->getNomCategorie()) === 'télevision';
                if ($isTelevisionFavorite && $favori->getCategorie() === null) {
                    $favori->setCategorie($categorieTelevision);
                    $entityManager->persist($favori);
                    $hasTelevisionCategoryBackfill = true;
                }
            }

            if ($hasTelevisionCategoryBackfill) {
                $entityManager->flush();
            }
        }
        $favorisTelevisionIds = [];
        foreach ($favorisTelevision as $favori) {
            $isTelevisionFavorite = ($categorieTelevision !== null && $favori->getCategorie() !== null && $favori->getCategorie()->getId() === $categorieTelevision->getId())
                || ($favori->getNomCategorie() !== null && strtolower($favori->getNomCategorie()) === 'télevision');

            if ($isTelevisionFavorite && $favori->getIdElement() !== null) {
                $favorisTelevisionIds[] = $favori->getIdElement();
            }
        }
       //////////////// date debut et fin cas gratuité defini  avec type gratuité//////////////////////
       $directory_xml = $this->getParameter('xml_directory');
        if (file_exists($directory_xml."\chaine_gratuite_".$idetablissement.".xml")) {
            $fichier = $directory_xml.'\chaine_gratuite_'.$idetablissement.'.xml';
            $xml = simplexml_load_file($fichier);
            $dd = $xml->date_debut;
            $df = $xml->date_fin;
            $typegratuite = $xml->typegratuite;
        } else {
            $dd = '10-09-1990 13:35:00';
            $df = '10-09-1990 13:35:00';
            $typegratuite = '0';
        }
//dd($directory_xml."chaine_gratuite_".$idetablissement.".xml");
        $chambre = $entityManager->getRepository(Chambre::class)->findBy(['etablissement' =>$etablissement]);
        
        $chambreArray = [];
        foreach ($chambre as $chambre) {
            $chambreArray[] = ['id'=>$chambre->getId(),'nom' => $chambre->getNom(),
                        'ip' => $chambre->getIp(),
                        'Mac' => $chambre->getMac(),
        ];
        }
        $chambreJson = json_encode($chambreArray);
        return $this->render('television/index.html.twig', [
            'television' => $television,'typegratuite' => $typegratuite, 'dd' => $dd, 'df' => $df,'appConfig' => $configApp,       
             'user' => $this->getUser(),
             'chambre' => $chambreJson,
               'favorisTelevisionIds' => $favorisTelevisionIds,

        ]);
    }

    #[Route('/ajouterchaine', name: 'ajouter_chaine')]
    public function ajouterchaine(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $categorieTelevision = $this->getTelevisionCategory($entityManager, $etablissement);
        $Acce = $this->getUser()->getAjoutTV() && $this->getUser()->getTELEVISION() && $configApp->getEnableTELEVISION()=="1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
        $request = Request::createFromGlobals();

        $nom = $request->get("nom");
        $ip = $request->get("ip");
        $port = $request->get("port");
        $numero = $request->get("numero");
        $pays = $request->get("pays");
        $protocole = $request->get("protocole");
        $active = $request->get("active");
        $gratuite = $request->get("gratuite");
        $favoris = $request->get("favoris", '0');

        if ((string) $favoris === '1' && !$this->canPersistNewFavorite($entityManager, $etablissement)) {
            $this->addFlash('success', 'Vous avez atteint la limite maximale de 6 favoris.');
            return $this->redirectToRoute('app_television');
        }
    

       $file1 = $request->files->get('logo');
       

       // Vérifiez si les fichiers ont été téléchargés
       if ($file1) {
           // Traitez les fichiers comme vous le souhaitez
           $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
           

           // Déplacez les fichiers téléchargés vers le dossier de destination
           $file1->move($this->getParameter('chaines_directory'), $fileName1);
          

           // Répondre avec un message de succès ou rediriger vers une autre page
         //  return new Response('Fichiers téléchargés avec succès !');
         $fileName = 'images/chaines/' . $fileName1;
       }
       else 
       $fileName = 'images/no_image.png';
       
       
       

       
        $television = new Television();
        
        $television->setEtablissement($etablissement);
        $television->setNom($nom);
        $television->setIp($ip);
        $television->setPort($port);
        $television->setNumero($numero);
        $television->setPays($pays);
        $television->setPays($pays);
        $television->setProtocole($protocole);
        $television->setLogo($fileName);
        if (isset($active) and !empty($active)) 
            $television->setActive(1);
       else 
            $television->setActive(0);
        if (isset($gratuite) and !empty($gratuite)) 
            $television->setGratuite(1);
       else 
            $television->setGratuite(0);

        $entityManager->persist($television);
        $entityManager->flush();

        if ((string) $favoris === '1' && $this->canPersistNewFavorite($entityManager, $etablissement)) {
            $favoriTelevision = new Favoris();
            $favoriTelevision->setEtablissement($etablissement);
            $favoriTelevision->setNomCategorie('Télevision');
            if ($categorieTelevision !== null) {
                $favoriTelevision->setCategorie($categorieTelevision);
            }
            $favoriTelevision->setIdElement($television->getId());
            $favoriTelevision->setNomElement($television->getNom());

            $entityManager->persist($favoriTelevision);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_television');
        
    }

    #[Route('/chaine/supprimer/{id}', name: 'supprimer_chaine')]
    public function supprimerchaine(EntityManagerInterface $entityManager, int $id): Response
    {
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

        $Acce = $this->getUser()->getSupprimerTV() && $this->getUser()->getTELEVISION() && $configApp->getEnableTELEVISION()=="1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
        $television = $entityManager->getRepository(Television::class)->find($id);

        if (!$television) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $favoris = $entityManager->getRepository(Favoris::class)->findBy([
            'Etablissement' => $etablissement,
            'idElement' => $television->getId(),
        ]);

        foreach ($favoris as $favori) {
            $entityManager->remove($favori);
        }

        $entityManager->remove($television);
        $entityManager->flush();

        return $this->redirectToRoute('app_television');
    }

    #[Route('/modifierchaine', name: 'modifier_chaine')]
    public function modifierchaine(EntityManagerInterface $entityManager): Response
    {   
        $repository = $entityManager->getRepository(Television::class);
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $categorieTelevision = $this->getTelevisionCategory($entityManager, $etablissement);

        $Acce = $this->getUser()->getModifierTv() && $this->getUser()->getTELEVISION() && $configApp->getEnableTELEVISION()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');      
      }
        $television  = $repository->findBy(['etablissement' => $etablissement]);
        foreach ($television as $tele) {
            $tele->setActive('0');
            $tele->setGratuite('0');
            $entityManager->persist($tele);
            $entityManager->flush();
          }
        $request = Request::createFromGlobals();

                $favoris = $request->get("listefavoris", []);

      
    
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
        $numero = $request->get("listenumero");
            if (isset($numero) and !empty($numero)) {
                foreach ($numero as $key => $k) {
                    $numero_television  = $repository->findById($key);
                    $numero_television[0]->setNumero($k);
                    $entityManager->persist($numero_television[0]);
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
        $gratuite = $request->get("listegratuite");
            if (isset($gratuite) and !empty($gratuite)) {
                foreach ($gratuite as $key => $k) {
                    $gratuite_television  = $repository->findById($key);
                    $gratuite_television[0]->setGratuite("1");
                    $entityManager->persist($gratuite_television[0]);
                    $entityManager->flush();
                }
            }

        if (isset($favoris)) {
            $remainingFavoriteSlots = $this->getRemainingFavoriteSlots($entityManager, $etablissement);
            $favoriteLimitReached = false;

            foreach ($television as $tele) {
                $televisionFavoris = $entityManager->getRepository(Favoris::class)->findBy([
                    'Etablissement' => $etablissement,
                    'idElement' => $tele->getId(),
                ]);

                $isFavorite = array_key_exists($tele->getId(), $favoris);

                if ($isFavorite) {
                    if (empty($televisionFavoris)) {
                        if ($remainingFavoriteSlots <= 0) {
                            $favoriteLimitReached = true;
                            continue;
                        }

                        $remainingFavoriteSlots--;

                        $favoriTelevision = new Favoris();
                        $favoriTelevision->setEtablissement($etablissement);
                        $favoriTelevision->setNomCategorie('Télevision');
                        if ($categorieTelevision !== null) {
                            $favoriTelevision->setCategorie($categorieTelevision);
                        }
                        $favoriTelevision->setIdElement($tele->getId());
                        $favoriTelevision->setNomElement($tele->getNom());

                        $entityManager->persist($favoriTelevision);
                    } else {
                        foreach ($televisionFavoris as $favoriTelevision) {
                            $favoriTelevision->setNomCategorie('Télevision');
                            if ($categorieTelevision !== null) {
                                $favoriTelevision->setCategorie($categorieTelevision);
                            }
                            $favoriTelevision->setNomElement($tele->getNom());
                            $entityManager->persist($favoriTelevision);
                        }
                    }
                } elseif (!empty($televisionFavoris)) {
                    foreach ($televisionFavoris as $favoriTelevision) {
                        $entityManager->remove($favoriTelevision);
                    }
                }
            }

            if ($favoriteLimitReached) {
                $this->addFlash('success', 'La limite maximale de 6 favoris a été atteinte. Certains favoris sélectionnés n\'ont pas été enregistrés.');
            }
        }
        $file1 = $request->files->get('listelogo');
            if (isset($file1) and !empty($file1)) {
                foreach ($file1 as $key => $k) {
                $thatlistfile = $repository->findById($key);
                if (!empty($k)) {
                    $fileName = md5(uniqid()) . '.' . $k->guessExtension();
                    $k->move($this->getParameter('chaines_directory'), $fileName);
                    $fileName = 'images/chaines/' . $fileName;
                } else {
                    $fileName = $thatlistfile[0]->getLogo();
                }
                $thatlistfile[0]->setLogo($fileName);
                $entityManager->persist($thatlistfile[0]);
                $entityManager->flush();
                }
            }
        return $this->redirectToRoute('app_television');
        
    }

    #[Route('/envoyer_gratuite', name: 'envoyer_gratuite')]
    public function envoyer_gratuite(EntityManagerInterface $entityManager): Response
    {
        $request = Request::createFromGlobals();
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

        $Acce = $this->getUser()->getGratuiteTv() && $this->getUser()->getTELEVISION() && $configApp->getEnableTELEVISION()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');  
          }
        $idetablissement = $etablissement->getId();
      $typegratuite = $request->get('t_gratuite');
        if ($typegratuite == 'r_defini') {
            $d_debut = $request->get('d_debut');
            $d_fin = $request->get('d_fin');
            $dd = date('d-m-Y H:i:s', strtotime($d_debut));
            $df = date('d-m-Y H:i:s', strtotime($d_fin));
        } else if ($typegratuite == 'r_immediate') {
            $dd = '10-09-2021 13:35:00';
            $df = '10-09-2099 13:35:00';
        }
  
        $dt = date("[j/m/y H:i:s]");
        $directory_logs = $this->getParameter('logs_directory');

        $fp = fopen($directory_logs.'/envoyer_date_'.$idetablissement.'.txt', 'a+'); // ouvrir le fichier ou le créer
        fseek($fp, SEEK_END); // poser le point de lecture à la fin du fichier
        $txt = $dt .'  dd:' . $dd . '  df:' . $df . '  type gratuité : ' . $typegratuite;
        $nouverr = $txt . "\r\n"; // ajouter un retour à la ligne au fichier
        fputs($fp, $nouverr); // ecrire ce texte
        fclose($fp); //fermer le fichier
    
              //if (file_exists("xml\chaine_gratuite_".$idetablissement.".xml")) {
                $directory_xml = $this->getParameter('xml_directory');

                        file_put_contents($directory_xml ."\chaine_gratuite_".$idetablissement.".xml", '<?xml version="1.0" encoding="utf-8"?>
                    <data><date_debut>' . $dd . '</date_debut> 
                    <date_fin>' . $df . '</date_fin>
                    <typegratuite>' . $typegratuite . '</typegratuite> </data>');
               
            
            
                   $ch = curl_init();
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                    curl_setopt($ch, CURLOPT_URL, "http://localhost:1111/package/dauntless_logger/libs/rabbitajax.php?AsyncUpdate=true&idetablissement=$idetablissement");
                    $json_as_string = curl_exec($ch);
                    curl_close($ch);
                    $msg = 1;
               /* } else {
                    $msg = 2;
                }*/
            
             
                $chambres = $entityManager->getRepository(Chambre::class)->findBy(['etablissement' =>$etablissement]);
                $nbbox = count($chambres);
                
      switch ($typegratuite) {
        case 'r_defini':
          $typeg = 'Gratuité avec date début et fin';
          break;
        case 'r_immediate':
          $typeg = 'Gratuité immédiate';
          $dd = date("d-m-Y H:i:s");
          $df = '-';
          break;
        default:
          $typeg = 'default';
      }
      
      $historiquegratuite = new Historiquegratuite();
      $historiquegratuite->setEtablissement($etablissement);
      $historiquegratuite->setDate(date("d-m-Y H:i:s"));
      $historiquegratuite->setDatein($dd);
      $historiquegratuite->setDateout($df);
      $historiquegratuite->setType($typeg);
  
      $entityManager->persist($historiquegratuite);
      $entityManager->flush();
  
  
      return $this->redirectToRoute('app_television', array('msg' => $msg, 'nbbox' => $nbbox));
     // return $this->redirectToRoute('app_television');
    }
    #[Route('/arreter_gratuite', name: 'arreter_gratuite')]
    public function arreter_gratuite(EntityManagerInterface $entityManager): Response
    {
  
        $request = Request::createFromGlobals();
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $idetablissement = $etablissement->getId();
      
      $dd = '10-09-1990 13:35:00';
      $df = '11-09-1990 13:35:00';
  
  
      $dt = date("[j/m/y H:i:s]");
      $directory_logs = $this->getParameter('logs_directory');
      $fp = fopen($directory_logs. '/envoyer_date_'.$idetablissement.'.txt', 'a+'); // ouvrir le fichier ou le créer
      fseek($fp, SEEK_END); // poser le point de lecture à la fin du fichier
      $txt = $dt . '  dd:' . $dd . '  df:' . $df . '  type gratuité : stop ';
      $nouverr = $txt . "\r\n"; // ajouter un retour à la ligne au fichier
      fputs($fp, $nouverr); // ecrire ce texte
      fclose($fp); //fermer le fichier
      $directory_xml = $this->getParameter('xml_directory');
      if (file_exists($directory_xml."\chaine_gratuite_".$idetablissement.".xml")) {
        file_put_contents($directory_xml."\chaine_gratuite_".$idetablissement.".xml", '<?xml version="1.0" encoding="utf-8"?>
          <data><date_debut>' . $dd . '</date_debut> 
          <date_fin>' . $df . '</date_fin> 
          <typegratuite>stop</typegratuite></data>');
  
  
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_URL, "http://localhost:1111/package/dauntless_logger/libs/rabbitajax.php?AsyncUpdate=true&idetablissement=$idetablissement");
        $json_as_string = curl_exec($ch);
        curl_close($ch);
  
        //msg de confirmation = 1 si l'operation est bien passé
        $msg = 3;
      } else {
        //msg de confirmation = 1 si l'operation n'est pas passé
        $msg = 2;
      }
  
      $chambres = $entityManager->getRepository(Chambre::class)->findBy(['etablissement' =>$etablissement]);
                $nbbox = count($chambres);
      $typeg = 'Gratuité arrêtée';
      $df = date("d-m-Y H:i:s");
      $dd = '-';
  
      $historiquegratuite = new historiquegratuite();
      $historiquegratuite->setEtablissement($etablissement);
      $historiquegratuite->setDate(date("d-m-Y H:i:s"));
      $historiquegratuite->setDatein($dd);
      $historiquegratuite->setDateout($df);
      $historiquegratuite->setType($typeg);
  
      $entityManager->persist($historiquegratuite);
      $entityManager->flush();
  
      //return $this->redirectToRoute('tele');
      return $this->redirectToRoute('app_television', array('msg' => $msg, 'nbbox' => $nbbox));
  
      //   return $this->redirectToRoute('tele');
  
    }
  
    
    #[Route('/historique_gratuite', name: 'historique_gratuite')]
    public function historique_gratuite(EntityManagerInterface $entityManager): Response
    {
        $request = Request::createFromGlobals();
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $idetablissement = $etablissement->getId();
        $repository = $entityManager->getRepository(Historiquegratuite::class);
        $historiquegratuite  = $repository->findBy(['etablissement' => $etablissement]);
        $chambres = $entityManager->getRepository(Chambre::class)->findBy(['etablissement' =>$etablissement]);
        $nbbox = count($chambres);
        $date2 = $request->request->get('date2');
        $date1 = $request->request->get('date1');


        if (empty($date1))

        $date1 = date("Y-m-d", strtotime("$date2 -90 day"));

        if (empty($date2))

        $date2 = date('Y-m-d');

        $date2 = date("d-m-Y", strtotime("$date2"));
        $date1 = date("d-m-Y", strtotime("$date1"));



        try{
            $directory_logs = $this->getParameter('logs_directory');

            $handle = fopen($directory_logs."/envoyer_date_".$idetablissement.".txt", "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    // process the line read.
                }
                
                fclose($handle);
            } else {
                // error opening the file.
            }
            $data = $handle;
            $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
            
            return $this->render('television/historique.html.twig', array('date2' => $date2, 'date1' => $date1, 'data' => $data, 'historiquegratuite' => $historiquegratuite,
            'nbbox' => $nbbox,'appConfig' => $appConfig,     
            'user' => $this->getUser(),
        ));
    }catch(\Exception $e) {
        $date2 = $request->request->get('date2');
        $date1 = $request->request->get('date1');


        if (empty($date1))

        $date1 = date("Y-m-d", strtotime("$date2 -90 day"));

        if (empty($date2))

        $date2 = date('Y-m-d');

        $date2 = date("d-m-Y", strtotime("$date2"));
        $date1 = date("d-m-Y", strtotime("$date1"));

        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
            
        return $this->render('television/historique.html.twig', array('date2' => $date2, 'date1' => $date1, 'data' => null, 'historiquegratuite' => null,
        'nbbox' => null,'appConfig' => $appConfig,     
        'user' => $this->getUser(),
    ));
    }
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
        #[Route('/televesion/LancerTv/{idtele}', name: 'LancerTV')]
        public function lancerTV(EntityManagerInterface $entityManager, int $idtele)
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
            if( !$this->getUser())
                return $this->redirectToRoute('app_login');
            $etablissement = $this->getUser()->getEtablissement();
            $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
            $repository = $entityManager->getRepository(Chambre::class);
            $queues = array();
            $television = $entityManager->getRepository(Television::class)
                ->findOneBy(['etablissement' => $etablissement, 'id' => $idtele]);
            if (!$television) {
                return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
            }
            $request = Request::createFromGlobals();
            $check = $request->get("checked");
            if (isset($check) and !empty($check)) {
                    foreach($check as $key1 => $k)
                    {
                            $lancer = $entityManager->getRepository(LancerTV::class)->findOneBy(['idchambre' => $k]);
                            if($lancer)
                                $entityManager->remove($lancer);
                            $lancer = new LancerTV();
                            $lancer->setIdchambre($k);
                            $lancer->setIdTV($idtele);
                            $entityManager->persist($lancer);
                            $boxs  = $repository->findById($k);
                            $chambre= $boxs[0]->getNom();
                            $queue = $etablissement->getId() . '.' . $chambre . '.service';
                           
            }
            array_push($queues,$queue);
            $entityManager->flush();
            $arrayTV = [];
            $arrayTV[] = $this->entityToArray($television);
            $TVJson = json_encode($arrayTV);
            $numChan = $television->getNumero();
            $message = "channel%%".$numChan."%%";
            $Manager = new PushRabbit();
            $Manager->MakeRabbitCall($queues, $message); 
        }
            return $this->redirectToRoute('app_television');
    }


        private function canPersistNewFavorite(EntityManagerInterface $entityManager, $etablissement): bool
        {
            $totalFavoris = $entityManager->getRepository(Favoris::class)->count([
                'Etablissement' => $etablissement,
            ]);

            return $totalFavoris < 6;
        }

        private function getRemainingFavoriteSlots(EntityManagerInterface $entityManager, $etablissement): int
        {
            $totalFavoris = $entityManager->getRepository(Favoris::class)->count([
                'Etablissement' => $etablissement,
            ]);

            return max(0, 6 - $totalFavoris);
        }

        private function getTelevisionCategory(EntityManagerInterface $entityManager, $etablissement): ?Categories
        {
            return $entityManager->getRepository(Categories::class)->findOneBy([
                'etablissement' => $etablissement,
                'nom' => 'Télevision',
            ]);
        }

#[Route('/television/ArreteTV/{idTV}',name:'ArretTV')]
public function RemoveTV(EntityManagerInterface $entityManager, int $idTV)
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    if( !$this->getUser())
     return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $repository = $entityManager->getRepository(Chambre::class);
    $queues = array();
    $annonce = $entityManager->getRepository(Television::class)
        ->findOneBy(['etablissement' => $etablissement, 'id' => $idTV]);
    if (!$annonce) {
        return new JsonResponse(['error' => 'annonce not found'], Response::HTTP_NOT_FOUND);
    }
    $request = Request::createFromGlobals();
    $check = $request->get("checked");
    if (isset($check) and !empty($check)) {
            foreach($check as $key1 => $k)
            {
                $lancer = $entityManager->getRepository(LancerTV::class)->findOneBy(['idTV'=>$idTV,'idchambre' => $k]);
                if($lancer)
                {
                $entityManager->remove($lancer);
                $boxs  = $repository->findById($k);
                $chambre= $boxs[0]->getNom();
                $queue = $etablissement->getId() . '.' . $chambre . '.service';

                    }
}
$entityManager->flush();
array_push($queues,$queue);  
$message = "arreter_radio%%".$idTV."%%".$k;
;               $Manager = new PushRabbit();
$Manager->MakeRabbitCall($queues, $message);
}
return $this->redirectToRoute('app_television');
}



#[Route('/television/GetAllchambreLancerTV/{idTV}',name:'GetAllchambreLancerTV',methods:'GET')]

public function GetAllchambreLancerTV(EntityManagerInterface $entityManager, int $idTV){
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
    $television = $entityManager->getRepository(Television::class)
        ->findOneBy(['etablissement' => $etablissement, 'id' => $idTV]);
    if (!$television) {
        return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
    }
    $lancer = $entityManager->getRepository(LancerTV::class)->findBy(['idTV'=>$idTV]);
    $arrayLancerTV = [];
    foreach ($lancer as $l) {
        $ex = $entityManager->getRepository(Chambre::class)->findOneBy(["id"=>$l->getIdchambre()]);
        $arrayLancerTV[] = ['id'=>$ex->getId(),'nom' => $ex->getNom(),
                        'ip' => $ex->getIp(),
                        'Mac' => $ex->getMac(),];
        }
        $LancerTVJson = json_encode($arrayLancerTV);

    return new JsonResponse(['TVLancer' => $arrayLancerTV]);
}

}
