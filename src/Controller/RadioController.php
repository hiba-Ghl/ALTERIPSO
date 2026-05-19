<?php

namespace App\Controller;

use App\Entity\CategorieRadio;
use App\Entity\Categories;
use App\Entity\Chambre;
use App\Entity\ConfigApp;
use App\Entity\Favoris;
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
        $categorieFavori = $this->getRadioFavoriteCategory($entityManager, $etablissement);
        $favorisRadio = $entityManager->getRepository(Favoris::class)->findBy([
            'Etablissement' => $etablissement,
        ]);
        $favorisRadioIds = [];
        foreach ($favorisRadio as $favori) {
            $isRadioFavorite = false;

            if ($categorieFavori !== null && $favori->getCategorie() === $categorieFavori) {
                $isRadioFavorite = true;
            }

            if ($favori->getNomCategorie() !== null && strtolower($favori->getNomCategorie()) === 'radio') {
                $isRadioFavorite = true;
            }

            if ($isRadioFavorite && $favori->getIdElement() !== null) {
                $favorisRadioIds[] = $favori->getIdElement();
            }
        }
        $chambre = $entityManager->getRepository(Chambre::class)->findBy(['etablissement' =>$etablissement]);
        $chambreArray = [];
        foreach ($chambre as $chambre) {
            $chambreArray[] = ['id'=>$chambre->getId(),'nom' => $chambre->getNom(),
                        'ip' => $chambre->getIp(),
                        'Mac' => $chambre->getMac(),
        ];
        }
        $chambreJson = json_encode($chambreArray);
        return $this->render('radio/index.html.twig', [
            'radio' => $radio , 'categorieradio' => $categorieradio,'appConfig' => $appConfig,'user' => $this->getUser(),
            'chambre' => $chambreJson,
            'favorisRadioIds' => $favorisRadioIds,

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
        $favoris = $request->get("favoris", '0');
        $categorieradio =  $entityManager->getRepository(CategorieRadio::class)->findById($catradio)[0];
        $categorieFavori = $this->getRadioFavoriteCategory($entityManager, $etablissement);

        if ((string) $favoris === '1' && !$this->canPersistNewFavorite($entityManager, $etablissement)) {
            $this->addFlash('success', 'Vous avez atteint la limite maximale de 6 favoris.');
            return $this->redirectToRoute('app_radio');
        }
    

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

        if ((string) $favoris === '1') {
            $favoriRadio = new Favoris();
            $favoriRadio->setEtablissement($etablissement);
            $favoriRadio->setCategorie($categorieFavori);
            $favoriRadio->setNomCategorie($categorieFavori?->getNom() ?? 'radio');
            $favoriRadio->setIdElement($radio->getId());
            $favoriRadio->setNomElement($radio->getNom());

            $entityManager->persist($favoriRadio);
            $entityManager->flush();
        }

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

        $categorieFavori = $this->getRadioFavoriteCategory($entityManager, $etablissement);
        $favoris = $entityManager->getRepository(Favoris::class)->findBy([
            'Etablissement' => $etablissement,
            'idElement' => $radio->getId(),
        ]);

        foreach ($favoris as $favori) {
            if ($categorieFavori === null || $favori->getCategorie() === $categorieFavori || strtolower((string) $favori->getNomCategorie()) === 'radio') {
                $entityManager->remove($favori);
            }
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
                $categorieFavori = $this->getRadioFavoriteCategory($entityManager, $etablissement);
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

        $favoris = $request->get("listefavoris", []);
        if (isset($favoris)) {
            $remainingFavoriteSlots = $this->getRemainingFavoriteSlots($entityManager, $etablissement);
            $favoriteLimitReached = false;

            foreach ($radio as $tele) {
                $radioFavoris = $entityManager->getRepository(Favoris::class)->findBy([
                    'Etablissement' => $etablissement,
                    'idElement' => $tele->getId(),
                ]);

                $isFavorite = array_key_exists($tele->getId(), $favoris);

                if ($isFavorite) {
                    if (empty($radioFavoris)) {
                        if ($remainingFavoriteSlots <= 0) {
                            $favoriteLimitReached = true;
                            continue;
                        }

                        $remainingFavoriteSlots--;

                        $favoriRadio = new Favoris();
                        $favoriRadio->setEtablissement($etablissement);
                        $favoriRadio->setIdElement($tele->getId());
                        $favoriRadio->setCategorie($categorieFavori);
                        $favoriRadio->setNomCategorie($categorieFavori?->getNom() ?? 'radio');
                        $favoriRadio->setNomElement($tele->getNom());

                        $entityManager->persist($favoriRadio);
                    } else {
                        foreach ($radioFavoris as $favoriRadio) {
                            $favoriRadio->setCategorie($categorieFavori);
                            $favoriRadio->setNomCategorie($categorieFavori?->getNom() ?? 'radio');
                            $favoriRadio->setNomElement($tele->getNom());

                            $entityManager->persist($favoriRadio);
                        }
                    }
                } elseif (!empty($radioFavoris)) {
                    foreach ($radioFavoris as $favoriRadio) {
                        if ($categorieFavori === null || $favoriRadio->getCategorie() === $categorieFavori || strtolower((string) $favoriRadio->getNomCategorie()) === 'radio') {
                            $entityManager->remove($favoriRadio);
                        }
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
       
            $repository = $entityManager->getRepository(Chambre::class);
            $queues = array();
            $radio = $entityManager->getRepository(Radio::class)
                ->findOneBy(['etablissement' => $etablissement, 'id' => $idRadio]);
            if (!$radio) {
                return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
            }
            $request = Request::createFromGlobals();
            $check = $request->get("checked");
            if (isset($check) and !empty($check)) {
                    foreach($check as $key1 => $k)
                    {
                            $lancer = $entityManager->getRepository(LancerRadio::class)->findOneBy(['idchambre' => $k]);
                            if($lancer)
                                $entityManager->remove($lancer);
                            $lancer = new LancerRadio();
                            $lancer->setIdchambre($k);
                            $lancer->setIdRadio($idRadio);
                            $entityManager->persist($lancer);
                            $boxs  = $repository->findById($k);
                            $chambre= $boxs[0]->getNom();
                            $queue = $etablissement->getId() . '.' . $chambre . '.service';
                        } 
                        $entityManager->flush();
                        array_push($queues,$queue);
                        $idradio = $radio->getId();
                        $message = "radio%%".$idradio."%%";
                        $Manager = new PushRabbit();
                        $Manager->MakeRabbitCall($queues, $message); 
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
    $repository = $entityManager->getRepository(Chambre::class);
    $queues = array();
    $annonce = $entityManager->getRepository(Radio::class)
        ->findOneBy(['etablissement' => $etablissement, 'id' => $idRadio]);
    if (!$annonce) {
        return new JsonResponse(['error' => 'annonce not found'], Response::HTTP_NOT_FOUND);
    }
    $request = Request::createFromGlobals();
    $check = $request->get("checked");
    if (isset($check) and !empty($check)) {
            foreach($check as $key1 => $k)
            {
                $lancer = $entityManager->getRepository(LancerRadio::class)->findOneBy(['idRadio'=>$idRadio,'idchambre' => $k]);
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
        $message = "arreter_radio%%".$idRadio;
        $Manager = new PushRabbit();
        $Manager->MakeRabbitCall($queues, $message);
    }
    return $this->redirectToRoute('app_radio');
}


    private function getRadioFavoriteCategory(EntityManagerInterface $entityManager, $etablissement): ?Categories
    {
        return $entityManager->getRepository(Categories::class)->findOneBy([
            'etablissement' => $etablissement,
            'nom' => 'radio',
        ]);
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


#[Route('/radio/GetAllchambreLancerRadio/{idRadio}',name:'GetAllchambreLancerRadio',methods:'GET')]

public function GetAllchambreLancerRadio(EntityManagerInterface $entityManager, int $idRadio){
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    if( !$this->getUser())
        return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();

    $annonce = $entityManager->getRepository(Radio::class)
        ->findOneBy(['etablissement' => $etablissement, 'id' => $idRadio]);
    if (!$annonce) {
        return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
    }
    $lancer = $entityManager->getRepository(LancerRadio::class)->findBy(['idRadio'=>$idRadio]);
    $arrayLancerRadio = [];
    foreach ($lancer as $l) {
        $ex = $entityManager->getRepository(Chambre::class)->findOneBy(["id"=>$l->getIdchambre()]);
        $arrayLancerRadio[] = ['id'=>$ex->getId(),'nom' => $ex->getNom(),
                        'ip' => $ex->getIp(),
                        'Mac' => $ex->getMac(),];
        }
        $LancerRadioJson = json_encode($arrayLancerRadio);

    return new JsonResponse(['RadioLancer' => $arrayLancerRadio]);
}


}


