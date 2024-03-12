<?php

namespace App\Controller;

use App\Entity\CategorieRadio;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etablissement;


use App\Entity\Radio;

class RadioController extends AbstractController
{
    #[Route('/radio', name: 'app_radio')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $repository = $entityManager->getRepository(Radio::class);
        $etablissement = $this->getUser()->getEtablissement();
        $idetablissement = $etablissement->getId();
        $radio  = $repository->findBy(['etablissement' => $etablissement],['nom' => 'ASC']);
        $categorieradio =  $entityManager->getRepository(CategorieRadio::class)->findBy(['etablissement' => $etablissement],['nom' => 'ASC']);
 
        return $this->render('radio/index.html.twig', [
            'radio' => $radio , 'categorieradio' => $categorieradio]);
    }

    #[Route('/ajouterradio', name: 'ajouter_radio')]
    public function ajouterradio(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
    
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
         $fileName = 'radio/' . $fileName1;
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
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
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
                    $fileName = 'radio/' . $fileName;
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

   


}


