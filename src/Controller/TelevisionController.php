<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Television;

class TelevisionController extends AbstractController
{
    #[Route('/television', name: 'app_television')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Television::class);
        $etablissement = $this->getUser()->getEtablissement();
        $television  = $repository->findBy(['etablissement' => $etablissement],['numero' => 'ASC']);
       // var_dump($television);die();
        return $this->render('television/index.html.twig', [
            'television' => $television,
        ]);
    }

    #[Route('/ajouterchaine', name: 'ajouter_chaine')]
    public function ajouterchaine(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
    
        $request = Request::createFromGlobals();

        $nom = $request->get("nom");
        $ip = $request->get("ip");
        $port = $request->get("port");
        $numero = $request->get("numero");
        $pays = $request->get("pays");
        $protocole = $request->get("protocole");
        $active = $request->get("active");
        $gratuite = $request->get("gratuite");
    

       $file1 = $request->files->get('logo');
       

       // Vérifiez si les fichiers ont été téléchargés
       if ($file1) {
           // Traitez les fichiers comme vous le souhaitez
           $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
           

           // Déplacez les fichiers téléchargés vers le dossier de destination
           $file1->move($this->getParameter('chaines_directory'), $fileName1);
          

           // Répondre avec un message de succès ou rediriger vers une autre page
         //  return new Response('Fichiers téléchargés avec succès !');
         $fileName = 'chaines/' . $fileName1;
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

        return $this->redirectToRoute('app_television');
        
    }

    #[Route('/chaine/supprimer/{id}', name: 'supprimer_chaine')]
    public function supprimerchaine(EntityManagerInterface $entityManager, int $id): Response
    {
        $television = $entityManager->getRepository(Television::class)->find($id);

        if (!$television) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $entityManager->remove($television);
        $entityManager->flush();

        return $this->redirectToRoute('app_television');
    }

    #[Route('/modifierchaine', name: 'modifier_chaine')]
    public function modifierchaine(EntityManagerInterface $entityManager): Response
    {   
        $repository = $entityManager->getRepository(Television::class);
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
        $television  = $repository->findBy(['etablissement' => $etablissement]);
       // var_dump($television);die();
        foreach ($television as $tele) {
            $tele->setActive('0');
            $tele->setGratuite('1');
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
                    $entityManager->persist($protocole_television[0]);
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
        $file1 = $request->files->get('listelogo');
            if (isset($file1) and !empty($file1)) {
                foreach ($file1 as $key => $k) {
                $thatlistfile = $repository->findById($key);
                if (!empty($k)) {
                    $fileName = md5(uniqid()) . '.' . $k->guessExtension();
                    $k->move($this->getParameter('chaines_directory'), $fileName);
                    $fileName = 'chaines/' . $fileName;
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


}


