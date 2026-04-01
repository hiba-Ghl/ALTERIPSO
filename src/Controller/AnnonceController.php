<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Chambre;
use App\Entity\ConfigApp;
use App\Entity\HistoriqueAnnonce;
use App\Entity\LancerAnnonce;
use App\Entity\LancerRadio;
use App\Entity\Lancerservice;
use App\Entity\LancerTV;
use App\Entity\Services;
use App\Push\PushRabbit;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Exception\IntegrityConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class AnnonceController extends AbstractController
{
    // la methode index**************************************************************************************************
    #[Route('/annonce', name: 'app_annonce')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        //recuperation de l'etablissement de l'utilisateur actuellement connecte 
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getANNONCES() && $configApp->getEnableANNONCES() == '1';
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
        //affichage des annonces liees a l'etablissement selon l'ordre croissant de leur champ nom
        $annonce =  $entityManager->getRepository(Annonce::class)->findBy(['etablissement' => $etablissement],['nom' => 'ASC']);
        $chambre = $entityManager->getRepository(Chambre::class)->findBy(['etablissement' =>$etablissement]);
        $chambreArray = [];
        foreach ($chambre as $chambre) {
            $chambreArray[] = ['id'=>$chambre->getId(),'nom' => $chambre->getNom(),
                        'ip' => $chambre->getIp(),
                        'Mac' => $chambre->getMac(),
        ];
        }
        $chambreJson = json_encode($chambreArray);
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonce,
            'appConfig' => $configApp,
            'chambre' => $chambreJson,
        ]);
    }


    //la methode d'ajout d'une annonce**************************************************************************************************
    #[Route('/annonce/ajouter', name: 'app_ajouter_annonce')]
    public function ajouterAnnonce(EntityManagerInterface $entityManager, Request $request): Response
    {


        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

        $Acce = $this->getUser()->getAjouterAnnonce() && $this->getUser()->getANNONCES() && $configApp->getEnableANNONCES() == '1';
            if (!$Acce) {
                $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
                return $this->redirectToRoute('home');            }
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        if ($appConfig) {
            $configArray = ['Status'=>$appConfig->getStatusServeur()];
        } 
        else{
            $configArray = ['Status'=>'online'];
        }
        $configJson = json_encode($configArray);
        //recuperation des donnees du formulaire 
        $valider = $request->get("valider");
        $nom = $request->get("nom");
        $type = $request->get("type");
        $position = $request->get("position");
        $datedebut = $request->get("datedebut");
        $datefin = $request->get("datefin");
        $duree = $request->get("duree");
        $frMessage = $request->get("FRMessage");
        $enMessage = $request->get("ENMessage");
        $esMessage = $request->get("ESMessage");
        $ptMessage = $request->get("PTMessage");
        $itMessage = $request->get("ITMessage");
        $ruMessage = $request->get("RUMessage");
        $deMessage = $request->get("DEMessage");
        $zhMessage = $request->get("ZHMessage");
        $arMessage = $request->get("ARMessage");
        $police = $request->get("font");
        $taille = $request->get("fontSize");
        $style = $request->get("style");
        $active = $request->get("active");

        $animation = $request->get("animation");
        $vitesseDefilement = $request->get("vitesseDefilement");


        
        $taille = !empty($taille) ? (int) $taille : null; //convert the string to int
        $vitesseDefilement = !empty($vitesseDefilement) ? (int) $vitesseDefilement : null;
        
        $themee = $request->get("themee"); //themee double e recupere les themse del a base de donnees 
        if ($themee == 'autre')
            $theme = $theme = $request->get("theme"); //theme avec single e est utilisee lorsque l'user ajoute un theme
        else
            $theme = $themee;

        $file = null; //initialisation du file

        if ($request->isMethod('POST')) {

            $annonce = new Annonce(); // creation d'une instance de l'entite Annonce

            
                // cette partie est pour la colonne URL 
            if ($type == 'Localtv') {
                $file = $request->get("ip");
            } else if ($type == 'Message' || $type == 'Message défilant'){
                $file = $request->get("text");
            } else {
                $fileName = ' ';
                $file1 = $request->files->get('file');
                if ($file1) {
                    $fileName = md5(uniqid()) . '.' . $file1->guessExtension();
                    $file1->move($this->getParameter('annonce_directory'), $fileName);
                    $file = 'images/annonce/' . $fileName;
                }
            }            $annonce->setEtablissement($etablissement);

            $annonce->setNom($nom);
            $annonce->setType($type);
            $annonce->setDatedebut($datedebut);
            $annonce->setDatefin($datefin);
            $annonce->setDuree($duree);
           
            $annonce->setFrMessage($frMessage);
            $annonce->setEnMessage($enMessage);
            $annonce->setEsMessage($esMessage);
            $annonce->setPtMessage($ptMessage);
            $annonce->setItMessage($itMessage);
            $annonce->setRuMessage($ruMessage);
            $annonce->setDeMessage($deMessage);
            $annonce->setZhMessage($zhMessage);
            $annonce->setArMessage($arMessage);
            $annonce->setTheme($theme);
            $annonce->setPosition($position);
            $annonce->setUrl($file);
            $annonce->setPolice($police);
            $annonce->setTaille($taille);
            $annonce->setStyle($style);
            $annonce->setActive($active);

            $annonce->setAnimation($animation);
            $annonce->setVitesseDefilement($type === 'Message défilant' ? $vitesseDefilement : null);


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
            'configApp'=>$configJson,
            'appConfig' => $appConfig,

        ]);

}







//la methode de suppression d'une annonce**************************************************************************************************
        #[Route('/annonce/supprimer/{id}', name: 'app_supprimer_annonce')]
        public function supprimerannonce(EntityManagerInterface $entityManager, int $id): Response
        {
            if( !$this->getUser())
            return $this->redirectToRoute('app_login');
            $etablissement = $this->getUser()->getEtablissement();
            $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
            $Acce = $this->getUser()->getSuppAnnonce() && $this->getUser()->getANNONCES() && $configApp->getEnableANNONCES() == '1';
            if (!$Acce) {
                $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
                return $this->redirectToRoute('home');            }
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
    public function modifierAnnonce(EntityManagerInterface $entityManager, Request $request, int $id): Response
    {

        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getModifierAnnonce() && $this->getUser()->getANNONCES() && $appConfig->getEnableANNONCES() == '1';
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
        $annonce = $entityManager->getRepository(Annonce::class)->findById($id)[0];
        if ($appConfig) {
            $configArray = ['Status'=>$appConfig->getStatusServeur()];
        } 
        else{
            $configArray = ['Status'=>'online'];
        }
        $configJson = json_encode($configArray);
        $valider = $request->get("valider");
        $nom = $request->get("nom");
        $type = $request->get("type");
        $position = $request->get("position");
        $datedebut = $request->get("datedebut");
        $datefin = $request->get("datefin");
        $duree = $request->get("duree");
  

        $frMessage = $request->get("FRMessage");
        $enMessage = $request->get("ENMessage");
        $esMessage = $request->get("ESMessage");
        $ptMessage = $request->get("PTMessage");
        $itMessage = $request->get("ITMessage");
        $ruMessage = $request->get("RUMessage");
        $deMessage = $request->get("DEMessage");
        $zhMessage = $request->get("ZHMessage");
        $arMessage = $request->get("ARMessage");
        $police = $request->get("font");
        $taille = $request->get("fontSize");
        $style = $request->get("style");
        $active = $request->get("active");
        $animation = $request->get("animation");
        $vitesseDefilement = $request->get("vitesseDefilement");



        $taille = !empty($taille) ? (int) $taille : null;//conversion vert type int
    $vitesseDefilement = !empty($vitesseDefilement) ? (int) $vitesseDefilement : null;
        
        $themee = $request->get("themee"); //themee double e recupere les themse del a base de donnees 
        if ($themee == 'autre')
            $theme = $theme = $request->get("theme"); //theme avec single e est utilisee lorsque l'user ajoute un theme
        else
            $theme = $themee;

        // $file = null;
        if ($request->isMethod('POST')) {


                // cette partie est pour la colonne URL 
            if ($type == 'Localtv') {
                $annonce->setUrl($request->get("ip"));
            }
            else if ($type == 'Message' || $type == 'Message défilant'){
                $annonce->setUrl($request->get("text"));
            } else {
                $fileName = ' ';
                $file1 = $request->files->get('file');
                if ($file1) {
                    $fileName = md5(uniqid()) . '.' . $file1->guessExtension();
                    $file1->move($this->getParameter('annonce_directory'), $fileName);
                    $annonce->setUrl('images/annonce/' . $fileName);
                }
            }
            

            $annonce->setEtablissement($etablissement);

            $annonce->setNom($nom);
            $annonce->setType($type);
            $annonce->setDatedebut($datedebut);
            $annonce->setDatefin($datefin);
            $annonce->setDuree($duree);
            $annonce->setFrMessage($frMessage);
            $annonce->setEnMessage($enMessage);
            $annonce->setEsMessage($esMessage);
            $annonce->setPtMessage($ptMessage);
            $annonce->setItMessage($itMessage);
            $annonce->setRuMessage($ruMessage);
            $annonce->setDeMessage($deMessage);
            $annonce->setZhMessage($zhMessage);
            $annonce->setArMessage($arMessage);
            $annonce->setTheme($theme);
            $annonce->setPosition($position);
            $annonce->setPolice($police);
            $annonce->setTaille($taille);
            $annonce->setStyle($style);
            $annonce->setActive($active);
            $annonce->setAnimation($animation);
            $annonce->setVitesseDefilement($type === 'Message défilant' ? $vitesseDefilement : null);





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
            'configApp'=>$configJson,
            'appConfig' => $appConfig,

        ]);

}




        // la methode details**************************************************************************************************
    #[Route('/annonce/details/{id}', name: 'app_details_annonce')]
    public function detailsAnnonce(EntityManagerInterface $entityManager, int $id): Response
    {

        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);
        $Acce = $this->getUser()->getANNONCES() && $configApp->getEnableANNONCES() == '1';
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
        //recuperation de l'etablissement de l'utilisateur actuellement connecte 
        //affichage des annonces liees a l'etablissement selon l'ordre croissant de leur champ nom
        $annonce = $entityManager->getRepository(Annonce::class)->findById($id)[0];
        
        
        return $this->render('annonce/details.html.twig', [
            'annonce' => $annonce,
            'appConfig' => $configApp,
        ]);
    }


    #[Route('/annonce/journal/{id}',name:'app_journal_annonce')]
    public function journalAnnonce(EntityManagerInterface $entityManager,int $id):Response
    {
    if( !$this->getUser())
    return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);
    $Acce = $this->getUser()->getANNONCES() && $configApp->getEnableANNONCES() == '1';
    if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');        }
    // //recuperation de l'etablissement de l'utilisateur actuellement connecte 
    //affichage des annonces liees a l'etablissement selon l'ordre croissant de leur champ nom
    $annonce = $entityManager->getRepository(Annonce::class)->findById($id)[0];
    $lancerAnnonce = $entityManager->getRepository(LancerAnnonce::class)->findBy(["idAnnonce" => $id]);
    $arrayLancerAnnonce= [];
    foreach ($lancerAnnonce as $l) {
        $annonce_name = $entityManager->getRepository(Annonce::class)->find($id);
        $chambre_name = $entityManager->getRepository(Chambre::class)->find($l->getIdchambre());
        $arrayLancerAnnonce[] = [
            'nom_chambre' => $chambre_name,
            'nom_annonce' => $annonce_name,
            'date' => $l->getDateEnvoie()
        ];
    }
    // dd($arrayLancerAnnonce);
    return $this->render('annonce/journal.html.twig', [
        'annonce' => $annonce,
        'appConfig' => $configApp,
        'annonceHistorique' => $arrayLancerAnnonce
    ]);
    }





    #[Route('/annonce/update/', name:'update_annonce')]
    public function update_annonce(EntityManagerInterface $entityManager,Request $request){
        if( !$this->getUser())
       return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $Acce = $this->getUser()->getSauvegarderService() && $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');    } 
        $repository = $entityManager->getRepository(Annonce::class);
        $Annonce  = $repository->findBy(['etablissement' => $etablissement]);
        $request = Request::createFromGlobals();
        foreach ($Annonce as $ann) {
            $ann->setActive(0);
            $entityManager->persist($ann);
            $entityManager->flush();
          }
        $active = $request->get("active");
                 if (isset($active) and !empty($active)) {
                    foreach ($active as $key => $k) {
                        $active_annonce  = $repository->findById($key);
                         $active_annonce[0]->setActive($k);
                         $entityManager->persist($active_annonce[0]);
                         $entityManager->flush();
                    }
                 }
            return $this->redirectToRoute('app_annonce');
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
    #[Route('/annonce/LancerAnnonce/{idAnnonce}', name: 'LancerAnnonce')]
    public function lancerAnnonce(EntityManagerInterface $entityManager, int $idAnnonce)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $repository = $entityManager->getRepository(Chambre::class);
        $queues = array();
        $annonce = $entityManager->getRepository(Annonce::class)
            ->findOneBy(['etablissement' => $etablissement, 'id' => $idAnnonce]);
        if (!$annonce) {
            return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }
        $request = Request::createFromGlobals();
        $check = $request->get("checked");
        $queues = array();

        if (isset($check) and !empty($check)) {
            foreach ($check as $key1 => $k1) {            
                        $lancer = $entityManager->getRepository(LancerAnnonce::class)->findOneBy(['idchambre' => $k1]);
                        $lancer1 = $entityManager->getRepository(Lancerservice::class)->findOneBy(['idchambre' => $k1]);
                        if($lancer)
                            $entityManager->remove($lancer);
                        if($lancer1)
                            $entityManager->remove($lancer1);
                        $dateNow = new DateTime();
                        $lancer = new LancerAnnonce();
                        $lancer->setIdchambre($k1);
                        $lancer->setIdAnnonce($idAnnonce);
                        $lancer->setDateEnvoie($dateNow);
                        $entityManager->persist($lancer);
                        $boxs  = $repository->findById($k1);
                        $chambre= $boxs[0]->getNom();
                        $boxs  = $repository->findById($k1);
                        $chambre= $boxs[0]->getNom();
                        $queue = $etablissement->getId() . '.' . $chambre . '.service';
                        array_push($queues,$queue); 
                    }
                    $entityManager->flush();
                    $arrayAnnonce = [];
                    $arrayAnnonce[] = $this->entityToArray($annonce);
                    $AnnonceJson = json_encode($arrayAnnonce);
                    $message = "lancer_annonce%%".$AnnonceJson;
                    $Manager = new PushRabbit();
                    $Manager->MakeRabbitCall($queues, $message); 
}
return $this->redirectToRoute('app_annonce');

}


#[Route('/annonce/ArreteAnnonce/{idAnnonce}',name:'Arreteannonce')]
public function RemoveAnnonce(EntityManagerInterface $entityManager, int $idAnnonce)
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    if( !$this->getUser())
     return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $repository = $entityManager->getRepository(Chambre::class);
    $queues = array();
    $annonce = $entityManager->getRepository(Annonce::class)
        ->findOneBy(['etablissement' => $etablissement, 'id' => $idAnnonce]);
    if (!$annonce) {
        return new JsonResponse(['error' => 'annonce not found'], Response::HTTP_NOT_FOUND);
    }
    $request = Request::createFromGlobals();
    $check = $request->get("checked");
    if (isset($check) and !empty($check)) {
            foreach($check as $key1 => $k1)
            {
                $lancer = $entityManager->getRepository(LancerAnnonce::class)->findOneBy(['idAnnonce'=>$idAnnonce,'idchambre' => $k1]);
                if($lancer)
                {
                $entityManager->remove($lancer);
                $boxs  = $repository->findById($k1);
                $chambre= $boxs[0]->getNom();
                $queue = $etablissement->getId() . '.' . $chambre . '.service';
            }
            }
            $entityManager->flush();
            array_push($queues,$queue);  
            $message = "arreter_annonce%%".$idAnnonce;          
            $Manager = new PushRabbit();
            $Manager->MakeRabbitCall($queues, $message);
}
    return $this->redirectToRoute('app_annonce');
}



#[Route('/annonce/GetAllchambreLancerAnnance/{idAnnonce}',name:'GetAllchambreLancerAnnonce',methods:'GET')]

public function GetAllchambreLancerAnnonce(EntityManagerInterface $entityManager, int $idAnnonce){
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    if( !$this->getUser())
        return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
    $annonce = $entityManager->getRepository(Annonce::class)
        ->findOneBy(['etablissement' => $etablissement, 'id' => $idAnnonce]);
    if (!$annonce) {
        return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
    }
    $lancer = $entityManager->getRepository(LancerAnnonce::class)->findBy(['idAnnonce'=>$idAnnonce]);
    $arrayLancerAnnonce = [];
    foreach ($lancer as $l) {
        $ex = $entityManager->getRepository(Chambre::class)->findOneBy(["id"=>$l->getIdchambre()]);
        $arrayLancerAnnonce[] = ['id'=>$ex->getId(),'nom' => $ex->getNom(),
                        'ip' => $ex->getIp(),
                        'Mac' => $ex->getMac(),];
        }
        $LancerAnnonceJson = json_encode($arrayLancerAnnonce);

    return new JsonResponse(['AnnonceLancer' => $arrayLancerAnnonce]);
}

}

