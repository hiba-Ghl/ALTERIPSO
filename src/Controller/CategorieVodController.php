<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CategorieVod;
use App\Entity\ConfigApp;
use App\Entity\Vod;
use Doctrine\DBAL\Exception\IntegrityConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class CategorieVodController extends AbstractController
{
    //la methode index*******************************************************************************************************************************************************
    
    
    #[Route('/categorie/vod', name: 'app_categorie_vod')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);  
        $Acce = $this->getUser()->isCatVod() && $configApp->getEnableVOD()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        //recuperation de l'etablissement de l'utilisateur actuellement connecte 
        $etablissement = $this->getUser()->getEtablissement();
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        //affichage des categories liees a l'etablissement selon l'ordre croissant de leur champ position
        $catvod =  $entityManager->getRepository(CategorieVOD::class)->findBy(['etablissement' => $etablissement],['position' => 'ASC']);
        return $this->render('categorie_vod/index.html.twig', [
            'categorie_v_o_ds' => $catvod,
            'appConfig' => $appConfig,
            'user' => $this->getUser(),
        ]);
    }


    //la methode d'ajout d'une categorie vod*******************************************************************************************************************************************************
    
    #[Route('/categorievod/ajouter', name: 'app_ajouter_categorievod')]
    public function ajoutercategorievod(EntityManagerInterface $entityManager): Response
    {
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
  
        $Acce = $this->getUser()->isAjoutCatVod() && $this->getUser()->isCatVod() && $configApp->getEnableVOD()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
            $request = Request::createFromGlobals(); 
            if ($configApp) {
                $configArray = ['Status'=>$configApp->getStatusServeur()];
            } 
            else{
                $configArray = ['Status'=>'online'];
            }
            $configJson = json_encode($configArray);
       // Récupération des positions utilisées sera utile dans le template
       $usedPositions = $entityManager->createQueryBuilder()
       ->select('c.position')
       ->from(CategorieVOD::class, 'c')
       ->getQuery()
       ->getArrayResult();

        // Extraire les positions
        $usedPositions = array_column($usedPositions, 'position');

        // Générer les positions disponibles on choisit 20 positionsq
        $availablePositions = [];
        for ($i = 1; $i <= 20; $i++) {
        if (!in_array($i, $usedPositions)) {
           $availablePositions[] = $i;
           }
        }       

        //recuperation des donnees du formulaire    
            $valider = $request->get("valider");
            $nom = $request->get("nom");
            $active = $request->get("active");
            $position = $request->get("position");
            $file1 = $request->files->get('logo');
            $FR = $nom;
            $EN = $request->get("EN");
            $ES = $request->get("ES");
            $PT = $request->get("PT");
            $IT = $request->get("IT");
            $RU = $request->get("RU");
            $DE = $request->get("DE");
            $ZH = $request->get("ZH");
            $AR = $request->get("AR");
        
        
            if(isset($valider)){
                //traitement de l'image
                $fileName = 'images/no_image.png';
                // Vérifiez si les fichiers ont été téléchargés
                 if ($file1) {

                        //la verification de la taille 
                         if ($file1->getSize() > 400000) {
                             // Gérer l'erreur de taille de fichier
                             $this->addFlash('error', 'La taille maximale autorisée pour le fichier est de 400 Ko.');
                            return $this->redirectToRoute('app_categorie_vod'); 
                         }

            
                        // Traitement du fichier
                        $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();

                        // Déplacez les fichiers téléchargés vers le dossier de destination
                        $file1->move($this->getParameter('categorie_vod_directory'), $fileName1);
               

                        $fileName = 'images/categorie_vod/' . $fileName1;
                 }
        
        
        //la creation d'une nouvelle instance de l'entite CategorieVOD    
            $categories = new CategorieVOD();
            
            $categories->setEtablissement($etablissement);
            $categories->setNom($nom);
            $categories->setActive($active);
            $categories->setPosition($position);
            $categories->setLogo($fileName);
            $categories->setFR($FR);
            $categories->setEN($EN);
            $categories->setES($ES);
            $categories->setPT($PT);
            $categories->setIT($IT);
            $categories->setRU($RU);
            $categories->setDE($DE);
            $categories->setZH($ZH);
            $categories->setAR($AR);
            $categories->setId(mt_rand(1, 99999));

        //la persistance de l'entite et l'enregistrement dans la base dee donnees     
            $entityManager->persist($categories);
            $entityManager->flush();
        //si tous est valide on redirige vers index     
            return $this->redirectToRoute('app_categorie_vod');
        }
        //sinon on reaffiche le formulaire de la creation d'une nouvelle categorie VOD
        return $this->render('categorie_vod/ajouter.html.twig', [
            'availablePositions' => $availablePositions,
            'configApp'=>$configJson,
            'appConfig' => $configApp,
            'user' => $this->getUser(),
        ]);
        
    }


    //la methode de suppression d'une categorie VOD*******************************************************************************************************************************************************
    
    
    #[Route('/categorievod/supprimer/{id}', name: 'app_supprimer_categorievod')]
    public function supprimercategorievod(EntityManagerInterface $entityManager, int $id): Response
    {
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->isSuppCatVod() && $this->getUser()->isCatVod() && $configApp->getEnableVOD()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        try {
            $CategorieVod = $entityManager->getRepository(CategorieVod::class)->find($id);

        if (!$CategorieVod) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
         // verifier si la categorie est une cle etrangere dans la table des vod
         $vod = $entityManager->getRepository(Vod::class)->findBy(['categorie' => $CategorieVod]);

         if (count($vod) > 0) {
             $this->addFlash('error', 'Suppression Non Autorisée: La suppression de cette catégorie n\'est pas possible car elle est actuellement associée à des VODs dans notre système.');
             return $this->redirectToRoute('app_categorie_vod');
         }

        $entityManager->remove($CategorieVod);
        $entityManager->flush();

        return $this->redirectToRoute('app_categorie_vod');
        } 
        catch (\PDOException $e) {
            // Gérer l'exception parente ici (PDOException)
            $errorMessage = $e->getMessage(); // Obtenez le message d'erreur PDO
            return $this->redirectToRoute('app_categorie_vod');
        }
    }




    //la methode de modification dune categorie vod *******************************************************************************************************************************************************
    
    
    #[Route('/categorievod/modifier/{id}', name: 'app_modifier_categorievod')]
    public function modifiercategorievod(EntityManagerInterface $entityManager, int $id): Response
        {
            $etablissement = $this->getUser()->getEtablissement();
            $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);    
        $Acce = $this->getUser()->isModifierCatVod() && $this->getUser()->isCatVod() && $configApp->getEnableVOD()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
            $etablissement = $this->getUser()->getEtablissement();
            //$repository = $entityManager->getRepository(CategorieVod::class); //added for some reason
            $categories = $entityManager->getRepository(CategorieVod::class)->findById($id)[0];
            $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
            if ($appConfig) {
                $configArray = ['Status'=>$appConfig->getStatusServeur()];
            } 
            else{
                $configArray = ['Status'=>'online'];
            }
            $configJson = json_encode($configArray);
            $request = Request::createFromGlobals();

            //recuperation des positions valables
            $usedPositions = $entityManager->createQueryBuilder()->select('c.position')
            ->from(CategorieVOD::class, 'c')
            ->getQuery()
            ->getArrayResult();

            // Extraire les positions
            $usedPositions = array_column($usedPositions, 'position');

            // Générer les positions disponibles
             $availablePositions = [];
            for ($i = 1; $i <= 20; $i++) {
                if (!in_array($i, $usedPositions)) {
                    $availablePositions[] = $i;
                }
            }       

            
            $valider = $request->get("valider");
            $nom = $request->get("nom");
            $active = $request->get("active");
            $position = $request->get("position");
        

        $file1 = $request->files->get('logo');
        
            if(isset($valider)){
                $fileName = $categories->getLogo();
        // Vérifiez si les fichiers ont été téléchargés
        if ($file1 ) {
            //la verification de la taille 
            if ($file1->getSize() > 400000) {
                // Gérer l'erreur de taille de fichier
                $this->addFlash('error', 'La taille maximale autorisée pour le fichier est de 400 Ko.');
            }
            // Traitement du fichier 
            $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
            

            // Déplacez les fichiers téléchargés vers le dossier de destination
            $file1->move($this->getParameter('categorie_vod_directory'), $fileName1);
            

            $fileName = 'images/categorie_vod/' . $fileName1;
        }
        
                
            
            $FR = $nom;
            $EN = $request->get("EN");
            $ES = $request->get("ES");
            $PT = $request->get("PT");
            $IT = $request->get("IT");
            $RU = $request->get("RU");
            $DE = $request->get("DE");
            $ZH = $request->get("ZH");
            $AR = $request->get("AR");
        
            
            
            
            $categories->setEtablissement($etablissement);
            $categories->setNom($nom);
            $categories->setActive($active);
            $categories->setPosition($position);
            $categories->setLogo($fileName);
            $categories->setFR($FR);
            $categories->setEN($EN);
            $categories->setES($ES);
            $categories->setPT($PT);
            $categories->setIT($IT);
            $categories->setRU($RU);
            $categories->setDE($DE);
            $categories->setZH($ZH);
            $categories->setAR($AR);

            $entityManager->persist($categories);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_vod');
    }

        


      return $this->render('categorie_vod/modifier.html.twig', [
        'categorie_v_o_ds' => $categories,
        'availablePositions' => $availablePositions,
        'configApp'=>$configJson,
        'appConfig' => $appConfig,
        'user' => $this->getUser(),
        ]);

        
    }


    //la methode d'update du champ active *******************************************************************************************************************************************************
    #[Route('/categorievod/updateactive', name: 'app_update_active_categorie_vod')]
    public function updateActiveStatus(EntityManagerInterface $entityManager): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->isSauvegarderCatVod() && $this->getUser()->isCatVod() && $configApp->getEnableVOD()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        $repository = $entityManager->getRepository(CategorieVod::class);
        $etablissement = $this->getUser()->getEtablissement();
        $categories = $repository->findBy(['etablissement' => $etablissement]);

       //setting all active status of categories to 0
        foreach ($categories as $cat) {
            $cat->setActive('0');   
            $entityManager->persist($cat);
            $entityManager->flush();
          }

        $request = Request::createFromGlobals();
        $active = $request->get("listeactive");
        if (isset($active) and !empty($active)) {
            foreach ($active as $key => $k) {
                $active_categories  = $repository->findById($key);
                $active_categories[0]->setActive("1");
                $entityManager->persist($active_categories[0]);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('app_categorie_vod'); 
    }
}








  

