<?php

namespace App\Controller;

use App\Entity\CategorieVod;
use App\Entity\ConfigApp;
use App\Entity\Vod;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class VodController extends AbstractController
{

    //la methode index*************************************************************************************************************************************************************8
    
    #[Route('/vod', name: 'app_vod')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Vod::class);
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getVOD() && $configApp->getEnableVOD() == "1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }

        $vod  = $repository->findBy(['etablissement' => $etablissement],['nom' => 'ASC']); 
        return $this->render('vod/index.html.twig', [
            'videos' => $vod,'appConfig' => $configApp,
            'user' => $this->getUser(),
        ]);
    }


    //la methode d'ajout d'une VOD *************************************************************************************************************************************************************
    
    #[Route('/vod/ajouter', name: 'app_ajouter_vod')]
    public function ajoutervod(EntityManagerInterface $entityManager): Response
    {
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getAjouteVod() && $this->getUser()->getVOD() && $configApp->getEnableVOD() == "1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
        $request = Request::createFromGlobals();

        //recuperation des categories deja existantes
        $categories = $entityManager->getRepository(CategorieVod::class)->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);



        //recuperation des donnees du formulaire
        $valider = $request->get("valider");
        $nom = $request->get("nom");
        $file1 = $request->files->get("logo");
        $url = $request->get("url");
        $categorieId = $request->get("categorie");
        $description = $request->get("description");
        
        



        if(isset($valider)){
            //traitement de l'image
            $fileName = 'images/no_image.png';
            // Vérifiez si les fichiers ont été téléchargés
             if ($file1) {

                    
                    // Traitez les fichiers comme vous le souhaitez
                    $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();

                    // Déplacez les fichiers téléchargés vers le dossier de destination
                    $file1->move($this->getParameter('vod_directory'), $fileName1);
           

                    //  return new Response('Fichiers téléchargés avec succès !');
                    $fileName = 'images/vod/' . $fileName1;
                }
                
                //la creation d'une nouvelle instance de l'entite VOD
                $vod = new Vod();
                //le remplissage ses attributs 
                $vod->setEtablissement($etablissement);
                $vod->setNom($nom);
                $vod->setDescription($description);
                $vod->setLogo($fileName);
                $categorie = $entityManager->getRepository(CategorieVod::class)->find($categorieId);
                $vod->setCategorie($categorie);
                $vod->setUrl($url);
                

                //la persistance de l'entite et l'enregistrement dans la base dee donnees     
                $entityManager->persist($vod);
                $entityManager->flush();
                //si tous est valide on redirige vers index     
                return $this->redirectToRoute('app_vod');
        }

        //sinon on reaffiche le formulaire de la creation d'une nouvelle categorie VOD
        return $this->render('vod/ajouter.html.twig',[
            'categories' => $categories,
            'appConfig' => $configApp,
            'user' => $this->getUser(),

        ]);
    }



    //la methode de suppression *************************************************************************************************************************************************************
                
    #[Route('/vod/supprimer/{id}',name: 'app_supprimer_vod')]
    public function supprimervod(EntityManagerInterface $entityManager, int $id): Response
    {
        $etablissement = $this->getUser()->getEtablissement();
          if( !$this->getUser())
     return $this->redirectToRoute('app_login');
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getSupprimerVod() && $this->getUser()->getVOD() && $configApp->getEnableVOD() == "1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
       $vod = $entityManager->getRepository(vod::class)->find($id);

        if (!$vod) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $entityManager->remove($vod);
        $entityManager->flush();

        return $this->redirectToRoute('app_vod');
    }





    //la methode de modification *************************************************************************************************************************************************************
                
    #[Route('/vod/modifier/{id}', name: 'app_modifier_vod')]
    public function modifiervod(EntityManagerInterface $entityManager, int $id): Response{

        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getModifierVod() && $this->getUser()->getVOD() && $configApp->getEnableVOD() == "1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');   
        }
        $vod = $entityManager->getRepository(Vod::class)->findById($id)[0];
        $request = Request::createFromGlobals();

        //recuperation des categories deja existantes
        $categories = $entityManager->getRepository(CategorieVod::class)->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);


        //recuperation des donnees du formulaire
        $valider = $request->get("valider");
        $nom = $request->get("nom");
        $description = $request->get("description"); 
        //var_dump($description);die();
        $file1 = $request->files->get("logo");
        $url = $request->get("url");
        $categorieId = $request->get("categorie");

        if(isset($valider)){
            //traitement de l'image
            $fileName = $vod->getLogo();
            // Vérifiez si les fichiers ont été téléchargés
             if ($file1) {

                     
                    // Traitez les fichiers comme vous le souhaitez
                    $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();

                    // Déplacez les fichiers téléchargés vers le dossier de destination
                    $file1->move($this->getParameter('vod_directory'), $fileName1);
           

                    //  return new Response('Fichiers téléchargés avec succès !');
                    $fileName = 'images/vod/' . $fileName1;
                }


                $vod->setEtablissement($etablissement);
                $vod->setNom($nom);
                $vod->setDescription($description);
                $vod->setLogo($fileName);
                $categorie = $entityManager->getRepository(CategorieVod::class)->find($categorieId);
                $vod->setCategorie($categorie);
                $vod->setUrl($url);

                $entityManager->persist($vod);
                $entityManager->flush();

                return $this->redirectToRoute('app_vod');
            }

            //variable twig a definir  en raison de l'utiliser dans le template modifier
             $ancienLogo = $vod->getLogo() ?? 'valeur_par_defaut.jpg';
             $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

                return $this->render('vod/modifier.html.twig', [
                    'vods' => $vod,
                    'categories' => $categories,
                    'ancienLogo' => $ancienLogo,
                    'appConfig' => $appConfig,
                    'user' => $this->getUser(),
                    ]);

        }


        //la methode du statut d'activation*************************************************************************************************************************************************************
                
        #[Route('/vod/active', name: 'app_active_vod')]
        public function activevod(EntityManagerInterface $entityManager) :Response {

            if( !$this->getUser())
                return $this->redirectToRoute('app_login');
            $etablissement = $this->getUser()->getEtablissement();
            $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
    
            $Acce = $this->getUser()->getSauvegarderVod() && $this->getUser()->getVOD() && $configApp->getEnableVOD() == "1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
            $repository = $entityManager->getRepository(Vod::class);
            $request = Request::createFromGlobals();
            $vods = $repository->findBy(['etablissement' => $etablissement]);


            //mettre tous a 0
            foreach ($vods as $vod) {
                $vod->setActive('0');   
                $entityManager->persist($vod);
                $entityManager->flush();
            }
            //recuperation de la liste des vods ayant un statut actif
            $active = $request->get("listeactive");
            if (isset($active) and !empty($active)) {
                foreach ($active as $key => $k) {
                    $active_vods  = $repository->findById($key);
                    $active_vods[0]->setActive("1");
                    $entityManager->persist($active_vods[0]);
                    $entityManager->flush();
                }

            return $this->redirectToRoute('app_vod'); 
            }
            return $this->redirectToRoute('app_vod');
        }

}