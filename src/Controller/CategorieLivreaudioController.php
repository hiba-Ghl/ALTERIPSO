<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\CategorieLivreaudio;
use App\Entity\ConfigApp;
use app\Entity\Livreaudio;
use Doctrine\DBAL\Exception\IntegrityConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException; // Importez également cette classe si nécessaire



class CategorieLivreaudioController extends AbstractController
{
    #[Route('/categorielivreaudio/supprimer/{id}', name: 'app_supprimer_categorielivreaudio')]
    public function supprimercategorielivreaudio(EntityManagerInterface $entityManager, int $id): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);  
        $Acce = $this->getUser()->isCatLivreAudio() && $configApp->getEnableLIVREAUDIO()=="1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        try {
            $CategorieLivreaudio = $entityManager->getRepository(CategorieLivreaudio::class)->find($id);

        if (!$CategorieLivreaudio) {
            throw $this->createNotFoundException(
                'La catégorie de livre audio avec l\'ID '.$id.' n\'a pas été trouvée.'
            );
        }


         // Check si la actegorie est une cle etrangere dans la table des livres audios
         $livresAudio = $entityManager->getRepository(LivreAudio::class)->findBy(['categorie' => $CategorieLivreaudio]);

         if (count($livresAudio) > 0) {
             $this->addFlash('error', 'Suppression Non Autorisée: La suppression de cette catégorie n\'est pas possible car elle est actuellement associée à des livres audio dans notre système.');
             return $this->redirectToRoute('app_categorie_livreaudio');
         }

        $entityManager->remove($CategorieLivreaudio);
        $entityManager->flush();

        return $this->redirectToRoute('app_categorie_livreaudio');
        } catch (\PDOException $e) {
            // Gérer toute autre exception
            $this->addFlash('error', 'Une erreur est survenue : '.$e->getMessage());
            return $this->redirectToRoute('app_categorie_livreaudio');
        }
    }
    



    
    #[Route('/categorielivreaudio', name: 'app_categorie_livreaudio')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);  
        $Acce = $this->getUser()->isCatLivreAudio() && $configApp->getEnableLIVREAUDIO()=="1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        $catlivreaudios = $etablissement->getCategorieLivreaudios();
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);
        //var_dump($catlivreaudios);die();

        return $this->render('categorie_livreaudio/index.html.twig', [
            'catlivreaudios' => $catlivreaudios,
            'appConfig' => $appConfig,
            'user' => $this->getUser(),

        ]);
    }

 

    #[Route('/categorielivreaudio/ajouter', name: 'app_ajouter_categorielivreaudio')]
    public function ajoutercategorielivreaudio(EntityManagerInterface $entityManager): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);  
        $Acce = $this->getUser()->isajoutCatLivreAudio() && $this->getUser()->isCatLivreAudio()  && $configApp->getEnableLIVREAUDIO()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
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
            ->from(CategorieLivreaudio::class, 'c')
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
                $fileName = 'images/no_image.png';
        // Vérifiez si les fichiers ont été téléchargés
        if ($file1 ) {
            // Traitez les fichiers comme vous le souhaitez
            $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
            

            // Déplacez les fichiers téléchargés vers le dossier de destination
            $file1->move($this->getParameter('categorie_livreaudio_directory'), $fileName1);
            

            // Répondre avec un message de succès ou rediriger vers une autre page

            //  return new Response('Fichiers téléchargés avec succès !');
            $fileName = 'images/categorie_livreaudio/' . $fileName1;
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
        
            $categories = new CategorieLivreaudio();
            
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

            $entityManager->persist($categories);
            $entityManager->flush();
            return $this->redirectToRoute('app_categorie_livreaudio');
        }
      return $this->render('categorie_livreaudio/ajouter.html.twig',[
        'availablePositions' => $availablePositions,
        'configApp' => $configJson,
        'appConfig' => $appConfig,
        'user' => $this->getUser(),
      ]);
        
    }

    #[Route('/categorielivreaudio/modifier/{id}', name: 'app_modifier_categorielivreaudio')]
    public function modifiercategorielivreaudio(EntityManagerInterface $entityManager, int $id): Response
        {
            if( !$this->getUser())
            return $this->redirectToRoute('app_login');
            $etablissement = $this->getUser()->getEtablissement();
            $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
      
            $Acce = $this->getUser()->isModifierCatLivreAudio() && $this->getUser()->isCatLivreAudio() && $configApp->getEnableLIVREAUDIO()=="1";
            if (!$Acce) {
                $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
                return $this->redirectToRoute('home');
            }
            $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
            if ($appConfig) {
                $configArray = ['Status'=>$appConfig->getStatusServeur()];
            } 
            else{
                $configArray = ['Status'=>'online'];
            }
            $configJson = json_encode($configArray);
            $categories = $entityManager->getRepository(CategorieLivreaudio::class)->findById($id)[0];
            $request = Request::createFromGlobals();




            //recuperation des positions valables
            $usedPositions = $entityManager->createQueryBuilder()->select('c.position')
            ->from(CategorieLivreaudio::class, 'c')
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
            // Traitez les fichiers comme vous le souhaitez
            $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
            

            // Déplacez les fichiers téléchargés vers le dossier de destination
            $file1->move($this->getParameter('categorie_livreaudio_directory'), $fileName1);
            

            // Répondre avec un message de succès ou rediriger vers une autre page
            //  return new Response('Fichiers téléchargés avec succès !');
            $fileName = 'images/categorie_livreaudio/' . $fileName1;
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
            return $this->redirectToRoute('app_categorie_livreaudio');
        }
      
      return $this->render('categorie_livreaudio/modifier.html.twig', [
        'categorielivreaudio' => $categories,
        'availablePositions' => $availablePositions,
        'configApp' => $configJson,
        'appConfig' => $appConfig,
        'user' => $this->getUser(),
    ]);

        
    }



     //methode du status active *********************************************************************************************************************************************

     #[Route('/categorielivreaudio/updateactive', name: 'app_update_active_categorie_livreaudio')]
     public function updateActiveStatus(EntityManagerInterface $entityManager): Response
     {
         if( !$this->getUser())
         return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);  
         $repository = $entityManager->getRepository(CategorieLivreaudio::class);
         $Acce = $this->getUser()->isSauvegarderCatLivreAudio() && $this->getUser()->isCatLivreAudio() && $configApp->getEnableLIVREAUDIO()=="1";
            if (!$Acce) {
                $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
                return $this->redirectToRoute('home');
            }
         $user = $this->getUser();
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
 
         return $this->redirectToRoute('app_categorie_livreaudio'); 
     }
 
   

}
