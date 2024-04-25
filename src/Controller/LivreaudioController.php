<?php

namespace App\Controller;

use App\Entity\CategorieLivreaudio;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etablissement;


use App\Entity\Livreaudio;

class LivreaudioController extends AbstractController
{
    #[Route('/livreaudio', name: 'app_livreaudio')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $repository = $entityManager->getRepository(Livreaudio::class);
        $etablissement = $this->getUser()->getEtablissement();
        $idetablissement = $etablissement->getId();
        $livreaudio  = $repository->findBy(['etablissement' => $etablissement],['nom' => 'ASC']);
        $categorielivreaudio =  $entityManager->getRepository(CategorieLivreaudio::class)->findBy(['etablissement' => $etablissement],['nom' => 'ASC']);
 
        return $this->render('livreaudio/index.html.twig', [
            'livreaudio' => $livreaudio , 'categorielivreaudio' => $categorielivreaudio]);
    }

    #[Route('/ajouterlivreaudio', name: 'ajouter_livreaudio')]
    public function ajouterlivreaudio(EntityManagerInterface $entityManager): Response
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
        $catlivreaudio = $request->get("catlivreaudio");
        $categorielivreaudio =  $entityManager->getRepository(CategorieLivreaudio::class)->findById($catlivreaudio)[0];
    

       $file1 = $request->files->get('logo');
       

       // Vérifiez si les fichiers ont été téléchargés
       if ($file1) {
           // Traitez les fichiers comme vous le souhaitez
           $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
           

           // Déplacez les fichiers téléchargés vers le dossier de destination
           $file1->move($this->getParameter('livreaudio_directory'), $fileName1);
          

           // Répondre avec un message de succès ou rediriger vers une autre page
         //  return new Response('Fichiers téléchargés avec succès !');
         $fileName = 'images/livreaudio/' . $fileName1;
       }
       else 
       $fileName = 'images/no_image.png';
       
       
       

       
        $livreaudio = new Livreaudio();
        
        $livreaudio->setEtablissement($etablissement);
        $livreaudio->setNom($nom);
        $livreaudio->setIp($ip);
        $livreaudio->setPort($port);
       $livreaudio->setCategorie($categorielivreaudio);
       $livreaudio->setPays($pays);
       $livreaudio->setPays($pays);
       $livreaudio->setProtocole($protocole);
       $livreaudio->setLogo($fileName);
        if (isset($active) and !empty($active)) 
           $livreaudio->setActive(1);
       else 
           $livreaudio->setActive(0);
       

        $entityManager->persist($livreaudio);
        $entityManager->flush();

        return $this->redirectToRoute('app_livreaudio');
        
    }

    #[Route('/livreaudio/supprimer/{id}', name: 'supprimer_livreaudio')]
    public function supprimerlivreaudio(EntityManagerInterface $entityManager, int $id): Response
    {
       $livreaudio = $entityManager->getRepository(Livreaudio::class)->find($id);

        if (!$livreaudio) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $entityManager->remove($livreaudio);
        $entityManager->flush();

        return $this->redirectToRoute('app_livreaudio');
    }

    #[Route('/modifierlivreaudio', name: 'modifier_livreaudio')]
    public function modifierlivreaudio(EntityManagerInterface $entityManager): Response
    {   
        $repository = $entityManager->getRepository(Livreaudio::class);
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
       $livreaudio  = $repository->findBy(['etablissement' => $etablissement]);
       // var_dump($livreaudio);die();
        foreach ($livreaudio as $tele) {
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
        $catlivreaudio = $request->get("listcatlivreaudio");
            if (isset($catlivreaudio) and !empty($catlivreaudio)) {
                foreach ($catlivreaudio as $key => $k) {
                    $catlivreaudio  = $repository->findById($key);
                    $kc =  $entityManager->getRepository(CategorieLivreaudio::class)->findById($k)[0];
                    $catlivreaudio[0]->setCategorie($kc);
                    $entityManager->persist($catlivreaudio[0]);
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
                    $k->move($this->getParameter('livreaudio_directory'), $fileName);
                    $fileName = 'images/livreaudio/' . $fileName;
                } else {
                    $fileName = $thatlistfile[0]->getLogo();
                }
                $thatlistfile[0]->setLogo($fileName);
                $entityManager->persist($thatlistfile[0]);
                $entityManager->flush();
                }
            }
         
       
            
       

       
      

        return $this->redirectToRoute('app_livreaudio');
        
    }

   


}


