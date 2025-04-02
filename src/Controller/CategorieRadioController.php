<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\CategorieRadio;
use App\Entity\ConfigApp;
use Doctrine\DBAL\Exception\IntegrityConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException; // Importez également cette classe si nécessaire



class CategorieRadioController extends AbstractController
{
    #[Route('/categorieradio/supprimer/{id}', name: 'app_supprimer_categorieradio')]
    public function supprimercategorieradio(EntityManagerInterface $entityManager, int $id): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);  
        $Acce = $this->getUser()->isSuppCatRadio() && $this->getUser()->isCatRadio() && $configApp->getEnableRADIO()=="1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        try {
            $CategorieRadio = $entityManager->getRepository(CategorieRadio::class)->find($id);

        if (!$CategorieRadio) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $entityManager->remove($CategorieRadio);
        $entityManager->flush();

        return $this->redirectToRoute('app_categorie_radio');
        } 
        catch (IntegrityConstraintViolationException $e) {
            // Gérer l'exception ici
            $errorMessage = "Erreur : Impossible de supprimer ou de mettre à jour une ligne parente en raison d'une contrainte de clé étrangère.";
            return $this->redirectToRoute('app_categorie_radio');
        } catch (\PDOException $e) {
            // Gérer l'exception parente ici (PDOException)
            $errorMessage = $e->getMessage(); // Obtenez le message d'erreur PDO
            // Faites quelque chose avec l'erreur, par exemple, journalisez-la
            return $this->redirectToRoute('app_categorie_radio');
        }
    }
    



    
    #[Route('/categorieradio', name: 'app_categorie_radio')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);  
        $Acce = $this->getUser()->isCatRadio();
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        $catradios = $etablissement->getCategorieRadios();
        //var_dump($catradios);die();
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);
        return $this->render('categorie_radio/index.html.twig', [
            'catradios' => $catradios,
            'appConfig' =>$appConfig,
            'user' => $this->getUser(),
        ]);
    }

 

    #[Route('/categorieradio/ajouter', name: 'app_ajouter_categorieradio')]
    public function ajoutercategorieradio(EntityManagerInterface $entityManager): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);              $Acce = $this->getUser()->isAjoutCatRadio() && $configApp->getEnableRADIO()=="1";
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
            $file1->move($this->getParameter('categorie_radio_directory'), $fileName1);
            

            // Répondre avec un message de succès ou rediriger vers une autre page

            //  return new Response('Fichiers téléchargés avec succès !');
            $fileName = 'images/categorie_radio/' . $fileName1;
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
        
            $categories = new CategorieRadio();
            
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
            return $this->redirectToRoute('app_categorie_radio');
        }
      return $this->render('categorie_radio/ajouter.html.twig',     
      ['configApp' => $configJson,
      'appConfig' =>$appConfig,
      'user' => $this->getUser(),
         ]
    );
        
    }

    #[Route('/categorieradio/modifier/{id}', name: 'app_modifier_categorieradio')]
    public function modifiercategorieradio(EntityManagerInterface $entityManager, int $id): Response
        {

            if( !$this->getUser())
            return $this->redirectToRoute('app_login');
            $etablissement = $this->getUser()->getEtablissement();
            $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);  
            
            $Acce = $this->getUser()->isModifierCatRadio() && $configApp->getEnableRADIO()=="1";
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
            $categories = $entityManager->getRepository(CategorieRadio::class)->findById($id)[0];
            $request = Request::createFromGlobals();

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
            $file1->move($this->getParameter('categorie_radio_directory'), $fileName1);
            

            // Répondre avec un message de succès ou rediriger vers une autre page
            //  return new Response('Fichiers téléchargés avec succès !');
            $fileName = 'images/categorie_radio/' . $fileName1;
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
            return $this->redirectToRoute('app_categorie_radio');
        }
      
      return $this->render('categorie_radio/modifier.html.twig', [
        'categorieradio' => $categories,
        'appConfig' =>$appConfig,
        'configApp' => $configJson,
        'user' => $this->getUser(),
    ]);

        
    }


    #[Route('/categorieradio/updateactive', name: 'app_update_active_categorie_radio')]
    public function updateActiveStatus(EntityManagerInterface $entityManager): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
       $etablissement = $this->getUser()->getEtablissement();
       $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);  
        $repository = $entityManager->getRepository(CategorieRadio::class);
        $Acce = $this->getUser()->isSauvegarderCatRadio() && $this->getUser()->isCatRadio() && $configApp->getEnableradio()=="1";
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

        return $this->redirectToRoute('app_categorie_radio'); 
    }
   

}
