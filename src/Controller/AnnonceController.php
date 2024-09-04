<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Chambre;
use App\Entity\HistoriqueAnnonce;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Exception\IntegrityConstraintViolationException;


class AnnonceController extends AbstractController
{
    // la methode index**************************************************************************************************
    #[Route('/annonce', name: 'app_annonce')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        //recuperation de l'etablissement de l'utilisateur actuellement connecte 
        $etablissement = $this->getUser()->getEtablissement();
        //affichage des annonces liees a l'etablissement selon l'ordre croissant de leur champ nom
        $annonce =  $entityManager->getRepository(Annonce::class)->findBy(['etablissement' => $etablissement],['nom' => 'ASC']);
        
        
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonce,
        ]);
    }


    //la methode d'ajout d'une annonce**************************************************************************************************
    #[Route('/annonce/ajouter', name: 'app_ajouter_annonce')]
    public function ajouterAnnonce(EntityManagerInterface $entityManager): Response
    {


        $etablissement = $this->getUser()->getEtablissement();

        $request = Request::createFromGlobals();


        //recuperation des donnees du formulaire 
        $valider = $request->get("valider");
        $nom = $request->get("nom");
        $type = $request->get("type");
        $position = $request->get("position");
        $datedebut = $request->get("datedebut");
        $datefin = $request->get("datefin");
        $duree = $request->get("duree");
        $fr = $request->get("FR");
        $en = $request->get("EN");
        $es = $request->get("ES");
        $pt = $request->get("PT");
        $it = $request->get("IT");
        $ru = $request->get("RU");
        $de = $request->get("DE");
        $zh = $request->get("ZH");
        $ar = $request->get("AR");
        $police = $request->get("font");
        $taille = $request->get("fontSize");
        $style = $request->get("style");

        
        $taille = !empty($taille) ? (int) $taille : null; //convert the string to int
        
        $themee = $request->get("themee"); //themee double e recupere les themse del a base de donnees 
        if ($themee == 'autre')
            $theme = $theme = $request->get("theme"); //theme avec single e est utilisee lorsque l'user ajoute un theme
        else
            $theme = $themee;

        $file = null; //initialisation du file

        if (isset($valider)) {

            $annonce = new Annonce(); // creation d'une instance de l'entite Annonce

            
                // cette partie est pour la colonne URL 
            if ($type == 'Localtv') {
                $file = $request->get("ip");
            } else if ($type == 'Message'){
                $file = $request->get("text");
            } else {
                $fileName = ' ';
                $file1 = $request->files->get('file');
                if ($file1) {
                    $fileName = md5(uniqid()) . '.' . $file1->guessExtension();
                    $file1->move($this->getParameter('annonce_directory'), $fileName);
                    $file = 'annonce/' . $fileName;
                }
            }

            $annonce->setEtablissement($etablissement);

            $annonce->setNom($nom);
            $annonce->setType($type);
            $annonce->setDatedebut($datedebut);
            $annonce->setDatefin($datefin);
            $annonce->setDuree($duree);
            $annonce->setFr($fr);
            $annonce->setEn($en);
            $annonce->setEs($es);
            $annonce->setPt($pt);
            $annonce->setIt($it);
            $annonce->setRu($ru);
            $annonce->setDe($de);
            $annonce->setZh($zh);
            $annonce->setAr($ar);
            $annonce->setTheme($theme);
            $annonce->setPosition($position);
            $annonce->setUrl($file);
            $annonce->setPolice($police);
            $annonce->setTaille($taille);
            $annonce->setStyle($style);


            //la persistance de l'entite et l'enregistrement dans la base dee donnees     
            $entityManager->persist($annonce);
            $entityManager->flush();
            //si tous est valide on redirige vers index     
                return $this->redirectToRoute('app_annonce');
        }

            // Fetch existing themes for the etablissement
        $existingThemes = $entityManager->createQueryBuilder()
        ->select('DISTINCT a.theme')
        ->from(Annonce::class, 'a')
        ->where('a.etablissement = :etablissement')
        ->setParameter('etablissement', $etablissement)
        ->getQuery()
        ->getArrayResult();

        // Extraction des themes du resultats
        $themes = array_column($existingThemes, 'theme'); //this theme refers to column name



        //sinon on reaffiche le formulaire de la creation d'une nouvelle annonce
        return $this->render('annonce/ajouter.html.twig',[
            'themes' => $themes,
        ]);

}







//la methode de suppression d'une annonce**************************************************************************************************
        #[Route('/annonce/supprimer/{id}', name: 'app_supprimer_annonce')]
        public function supprimerannonce(EntityManagerInterface $entityManager, int $id): Response
        {
            try {
                $annonce = $entityManager->getRepository(Annonce::class)->find($id);

            if (!$annonce) {
                throw $this->createNotFoundException(
                    'No annoncement found for id '.$id
                );
            }

            $entityManager->remove($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce');
            } 
            catch (IntegrityConstraintViolationException $e) {
                // Gérer l'exception 
                $errorMessage = "Erreur : Impossible de supprimer ou de mettre à jour une ligne parente en raison d'une contrainte de clé étrangère.";
                return $this->redirectToRoute('app_annonce');
            } catch (\PDOException $e) {
                // Gérer l'exception parente ici (PDOException)
                $errorMessage = $e->getMessage(); // Obtenez le message d'erreur PDO
                return $this->redirectToRoute('app_annonce');
            }
        }









    //la methode de modification d'une annonce**************************************************************************************************
    #[Route('/annonce/modifier/{id}', name: 'app_modifier_annonce')]
    public function modifierAnnonce(EntityManagerInterface $entityManager, int $id): Response
    {


        $etablissement = $this->getUser()->getEtablissement();

        $annonce = $entityManager->getRepository(Annonce::class)->findById($id)[0];

        $request = Request::createFromGlobals();

        $valider = $request->get("valider");
        $nom = $request->get("nom");
        $type = $request->get("type");
        $position = $request->get("position");
        $datedebut = $request->get("datedebut");
        $datefin = $request->get("datefin");
        $duree = $request->get("duree");
        $fr = $request->get("FR");
        $en = $request->get("EN");
        $es = $request->get("ES");
        $pt = $request->get("PT");
        $it = $request->get("IT");
        $ru = $request->get("RU");
        $de = $request->get("DE");
        $zh = $request->get("ZH");
        $ar = $request->get("AR");
        $police = $request->get("font");
        $taille = $request->get("fontSize");
        $style = $request->get("style");


        $taille = !empty($taille) ? (int) $taille : null;//conversion vert type int
        
        $themee = $request->get("themee"); //themee double e recupere les themse del a base de donnees 
        if ($themee == 'autre')
            $theme = $theme = $request->get("theme"); //theme avec single e est utilisee lorsque l'user ajoute un theme
        else
            $theme = $themee;


        if (isset($valider)) {


                // cette partie est pour la colonne URL 
            if ($type == 'Localtv') {
                $file = $request->get("ip");
            } else if ($type == 'Message'){
                $file = $request->get("text");
            } else {
                $fileName = ' ';
                $file1 = $request->files->get('file');
                if ($file1) {
                    $fileName = md5(uniqid()) . '.' . $file1->guessExtension();
                    $file1->move($this->getParameter('annonce_directory'), $fileName);
                    $file = 'annonce/' . $fileName;
                }
            }
            

            $annonce->setEtablissement($etablissement);

            $annonce->setNom($nom);
            $annonce->setType($type);
            $annonce->setDatedebut($datedebut);
            $annonce->setDatefin($datefin);
            $annonce->setDuree($duree);
            $annonce->setFr($fr);
            $annonce->setEn($en);
            $annonce->setEs($es);
            $annonce->setPt($pt);
            $annonce->setIt($it);
            $annonce->setRu($ru);
            $annonce->setDe($de);
            $annonce->setZh($zh);
            $annonce->setAr($ar);
            $annonce->setTheme($theme);
            $annonce->setPosition($position);
            $annonce->setUrl($file);
            $annonce->setPolice($police);
            $annonce->setTaille($taille);
            $annonce->setStyle($style);




            //la persistance de l'entite et l'enregistrement dans la base dee donnees     
            $entityManager->persist($annonce);
            $entityManager->flush();
            //si tous est valide on redirige vers index     
                return $this->redirectToRoute('app_annonce');
        }

            // Fetch existing themes for the etablissement
        $existingThemes = $entityManager->createQueryBuilder()
        ->select('DISTINCT a.theme')
        ->from(Annonce::class, 'a')
        ->where('a.etablissement = :etablissement')
        ->setParameter('etablissement', $etablissement)
        ->getQuery()
        ->getArrayResult();

        // Extract themes from the result
        $themes = array_column($existingThemes, 'theme'); //this theme refers to column name




        //sinon on reaffiche le formulaire de la creation d'une nouvelle annonce
        return $this->render('annonce/modifier.html.twig',[
            'themes' => $themes,
            'ann' => $annonce,
        ]);

}




        // la methode details**************************************************************************************************
    #[Route('/annonce/details/{id}', name: 'app_details_annonce')]
    public function detailsAnnonce(EntityManagerInterface $entityManager, int $id): Response
    {

        //recuperation de l'etablissement de l'utilisateur actuellement connecte 
        $etablissement = $this->getUser()->getEtablissement();
        //affichage des annonces liees a l'etablissement selon l'ordre croissant de leur champ nom
        $annonce = $entityManager->getRepository(Annonce::class)->findById($id)[0];
        
        
        return $this->render('annonce/details.html.twig', [
            'annonce' => $annonce,
        ]);
    }






}


