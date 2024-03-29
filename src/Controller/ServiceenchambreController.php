<?php

namespace App\Controller;


use App\Entity\ServiceEnChambre;
use App\Entity\TypeServiceEnChambre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;


class ServiceenchambreController extends AbstractController
{
    // Déclaration de la propriété privée $entityManager
    private $entityManager;

    // Constructeur de la classe, injecte l'EntityManagerInterface
    public function __construct(EntityManagerInterface $entityManager)
    {
        // Initialise la propriété $entityManager avec l'injection de dépendance
        $this->entityManager = $entityManager;
    }

    // Méthode qui récupère les services en chambre et affichage de la pagede service
    #[Route('/serviceenchambre', name: 'service_en_chambre')]
    public function index(): Response
    {
        // Récupération de l'utilisateur actuel
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
        
         // Récupération du repository pour l'entité ServiceEnChambre
        $serviceEnChambreRepository = $this->entityManager->getRepository(ServiceEnChambre::class);
        // Récupération de tous les services en chambre
        $serviceEnChambres = $serviceEnChambreRepository->findBy(['etablissement'=>$etablissement], ['position' => 'ASC']);
        
        // Affichage de la page avec les services en chambre
        return $this->render('service_en_chambre/index.html.twig', [
            'user' => $user,
            'serviceEnChambres' => $serviceEnChambres,
        ]);
    }

    // Méthode qui ajoute une catégorie de service
    #[Route('/ajoutercategoriesservice', name: 'ajoutercategoriesservice')]
    public function ajouterCategories(Request $request): Response
    {
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
        // Récupérer les données du formulaire
        $nom = $request->request->get('nom');
        $description = $request->request->get('Description');
        // Créer une nouvelle entité
        $typeService = new TypeServiceEnChambre();
        $typeService->setNom($nom);
        $typeService->setEtablissement($etablissement);
        $typeService->setDescription($description);
        // Enregistrer l'entité dans la base de données
        $this->entityManager->persist($typeService);
        $this->entityManager->flush();
        // Récupérer à nouveau la liste des services après l'ajout
        $serviceEnChambreRepository = $this->entityManager->getRepository(ServiceEnChambre::class);
        $serviceEnChambres = $serviceEnChambreRepository->findBy(['etablissement'=>$etablissement]);        
        // Rediriger ou afficher une réponse
        // (vous pouvez personnaliser cela en fonction de vos besoins)
        return $this->render('service_en_chambre/index.html.twig', [
            'user' => $user,
            'serviceEnChambres' => $serviceEnChambres,
        ]);
    }
   
    //methode pour afficher la page du ajout service en chambre
    #[Route('/service_formu', name: 'service_formu')]
    public function pageaddservice(): Response
    {
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
        $typeserviceEnChambreRepository = $this->entityManager->getRepository(TypeServiceEnChambre::class);
        $typeserviceEnChambres = $typeserviceEnChambreRepository->findBy(['etablissement'=>$etablissement],['nom' => 'ASC']);
        return $this->render('service_en_chambre/ajouter.html.twig', [
            'user' => $user,
            'typeserviceEnChambres' => $typeserviceEnChambres,
            'etablissement' => $etablissement,
        ]);
    }

    // Méthode qui ajoute un service en chambre
    #[Route('/ajouterservice', name: 'ajouterservice', methods: ['POST'])]
    public function ajouterservice(Request $request): Response
    {
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
        // Récupérer les données du formulaire
        $nom = $request->request->get('nom');
        $lastService = $this->entityManager->getRepository(ServiceEnChambre::class)->findOneBy(['etablissement'=>$etablissement], ['position' => 'DESC']);
        $position = $lastService ? $lastService->getPosition() + 1 : 1;
        $description = $request->request->get('Description');
        $contenu = $request->request->get('listrequte');      
        $EN = $request->request->get('EN');
        $ES = $request->request->get('ES');
        $PT = $request->request->get('PT');
        $IT = $request->request->get('IT');
        $RU = $request->request->get('RU');
        $DE = $request->request->get('DE');
        $ZH = $request->request->get('ZH');
        $AR = $request->request->get('AR');

        $typeServiceEnChambreId = $request->request->get('type_service_en_chambre');
        $typeServiceEnChambre = $this->entityManager->getRepository(TypeServiceEnChambre::class)->find($typeServiceEnChambreId);
        $type=$typeServiceEnChambre->getNom();
        if( $type == 'List') {
            $contenu = $request->request->get('listrequte');
        }else if($type == 'Achat'){ 
            $typePrix = $request->request->get('typePrix');
             if($typePrix=="prixnormal1"){
                $devise = $request->request->get('Devise');
                $prix=$request->get('prix');
                $qmin=$request->get('qmin');
                $qmax=$request->get('qmax');
                $time=$request->get('time');
                $timeArray = [];
                foreach ($time as $value) {
                    $timeArray[] = intval($value); 
                }
                $contenu=array("devise"=>$devise,"prix"=>$prix,"qmin"=>$qmin,"qmax"=>$qmax,"time" => $timeArray);
             }else{
                $inputqte = $request->get('inputqte');
                $inputprice = $request->get('inputprice');
                $time=$request->get('time');
                $timeArray = [];
                foreach ($time as $value) {
                    $timeArray[] = intval($value); 
                }
                $arrsize  = sizeof($inputqte);
                $contenu=array();   
                for($i=0;$i<$arrsize;$i++){
                $contenu[$i]=array("nom"=>$inputqte[$i],"quantite"=>$inputprice[$i],"time" => $timeArray);
                }
            }
        }if($type == 'Prise rendez vous'){ 
            $Descriptionprise=$request->get('Descriptionprise');
            $debutlundi=$request->get('debutlundi');
            $Finlundi=$request->get('Finlundi');
            $debutmardi=$request->get('debutmardi');
            $Finmardi=$request->get('Finmardi');
            $debutmerc =$request->get('debutmerc');
            $Finmerc=$request->get('Finmerc');
            $debutjeudi  =$request->get('debutjeudi');
            $Finjeudi=$request->get('Finjeudi');
            $debutven =$request->get('debutven');
            $Finven=$request->get('Finven');
            $debutsam  =$request->get('debutsam');
            $Finsam=$request->get('Finsam');
            $debutdim  =$request->get('debutdim');
            $Findim=$request->get('Findim');
            $contenu=array(
            "Description"=>$Descriptionprise,
            "Lundi"=>["debut"=>$debutlundi,"fin"=>$Finlundi],
            "Mardi"=>["debut"=>$debutmardi,"fin"=>$Finmardi],
            "Merecredi"=>["debut"=>$debutmerc ,"fin"=>$Finmerc],
            "Jeudi"=>["debut"=>$debutjeudi ,"fin"=>$Finjeudi],
            "Vendredi"=>["debut"=>$debutven ,"fin"=>$Finven],
            "Samedi"=>["debut"=>$debutsam,"fin"=>$Finsam],
            "Dimanche"=>["debut"=>$debutdim,"fin"=>$Findim],
            );
        }
       
        $service = new ServiceEnChambre();
        $service->setEtablissement($etablissement);
        $service->setNom($nom);
        $service->setPosition($position);
        $service->setActive('1');
        $service->setDescription($description);
        $service->setFr($nom);
        $service->setEn($EN);
        $service->setEs($ES);
        $service->setPt($PT);
        $service->setIt($IT);
        $service->setRu($RU);
        $service->setDe($DE);
        $service->setZh($ZH);
        $service->setAr($AR);
        // Récupérer le fichier du champ 'logo'
        $logoFile = $request->files->get('logo');
        // Vérifier si un fichier a été soumis
        if ($logoFile) {
            // Générer un nom de fichier unique
            $newFilename = uniqid() . '.' . $logoFile->guessExtension();
            // Déplacer le fichier vers le répertoire d'upload
            try {
                $logoFile->move(
                    $this->getParameter('service_in_room_directory'), 
                    $newFilename
                );
            } catch (FileException $e) {
                // Gérer les erreurs liées au déplacement du fichier
            }
            // Mettre à jour le chemin du logo dans l'entité ServiceEnChambre
            $service->setLogo($newFilename);
        }
        // Récupérer l'entité TypeServiceEnChambre
        $typeServiceEnChambre = $this->entityManager->getRepository(TypeServiceEnChambre::class)->find($typeServiceEnChambreId);
        $service->setTypeServiceEnChambre($typeServiceEnChambre);
        if($typeServiceEnChambre->getNom()=="Menu" || $typeServiceEnChambre->getNom()=="menu"){
            $service->setContenu('[]');
        }else if(($typeServiceEnChambre->getNom()=="Achat" || $typeServiceEnChambre->getNom()=="Achat")){
            $typePrix = $request->request->get('typePrix');
            if($typePrix=="prixnormal1"){
            $desc=json_encode([$contenu]);
            $service->setContenu($desc);
             }
            else{
                $desc=json_encode($contenu);
                $service->setContenu($desc);
            }
        }else if(($typeServiceEnChambre->getNom()=="List" || $typeServiceEnChambre->getNom()=="list")){
            $service->setContenu($contenu);
        }else if(($typeServiceEnChambre->getNom()=="Prise rendez vous" || $typeServiceEnChambre->getNom()=="prise rendez vous")){
            $desc=json_encode($contenu);
            $service->setContenu($desc);
            
        }
        // Persistir et flush l'entité
        $this->entityManager->persist($service);
        $this->entityManager->flush();
        $serviceEnChambreRepository = $this->entityManager->getRepository(ServiceEnChambre::class);
        $serviceEnChambres = $serviceEnChambreRepository->findAll();
        $serviceEnChambreRepository = $this->entityManager->getRepository(ServiceEnChambre::class);
        $serviceEnChambresdetails = $serviceEnChambreRepository->find($service->getId());
        $serviceId = $service->getId();
        if ($typeServiceEnChambre && mb_strtolower($typeServiceEnChambre->getNom()) === "menu") {
                $publicDirectory = __DIR__ . '/../../public/MenuServiceEnChambre';
                $filePath = $publicDirectory . '/' . $serviceId . '.txt';
            if (file_exists($filePath)) {
                $fileContent = file_get_contents($filePath);
                $categories = explode("\n", $fileContent);
                // Convertir chaque élément en chaîne de caractères
                $categories = array_map('strval', $categories);
                // Supprimer les éléments vides du tableau
                $categories = array_filter($categories);
                // Transformation du tableau de catégories en un tableau associatif avec la clé 'name'
                $categorieOptions = array_map(function ($category) {
                    return ['name' => trim($category)];
                }, $categories);
            } else {
                // Si le fichier n'existe pas, initialisez $categorieOptions en tant que tableau vide
                $categorieOptions = [];
            }
            $contenuJson = $serviceEnChambresdetails->getContenu();
            // Décoder le JSON en tableau associatif
            $data = json_decode($contenuJson, true);
            $typeserviceEnChambreRepository = $this->entityManager->getRepository(TypeServiceEnChambre::class);
            $typeserviceEnChambres = $typeserviceEnChambreRepository->findAll();
            // Rediriger vers la page listservice.html.twig
            $formatContenu = 'format1';
            return $this->render('service_en_chambre/modifier.html.twig', [
                'user' => $user,
                'serviceEnChambres' => $serviceEnChambres,
                ['id' => $serviceId],
                'serviceEnChambresdetails'=>$serviceEnChambresdetails,
                'categorieOptions'=>$categorieOptions,
                'data' => $data,
                'typeserviceEnChambres'=>$typeserviceEnChambres,
                'formatContenu' => $formatContenu,
            ]);
        }
        // Rediriger ou rendre une réponse
        return $this->render('service_en_chambre/index.html.twig', [
            'user' => $user,
            'serviceEnChambres' => $serviceEnChambres,
           
        ]); // Remplacez par la route de succès réelle
    }

    //Affichage page Details service en chambre
    #[Route('/page_details_service_en_chambre/{id}', name: 'page_details_service_en_chambre')]
    public function pagedetailsservice($id)
    {    
        $user = $this->getUser();
        $serviceEnChambreRepository = $this->entityManager->getRepository(ServiceEnChambre::class);
        $serviceEnChambres = $serviceEnChambreRepository->find($id);
        $ancienLogo = $serviceEnChambres->getLogo() ?? 'valeur_par_defaut.jpg';
        $publicDirectory = __DIR__ . '/../../public/MenuServiceEnChambre';
        $filePath = $publicDirectory . '/' . $id . '.txt';
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            $categories = explode("\n", $fileContent);
            // Convertir chaque élément en chaîne de caractères
            $categories = array_map('strval', $categories);
            // Supprimer les éléments vides du tableau
            $categories = array_filter($categories);
            // Transformation du tableau de catégories en un tableau associatif avec la clé 'name'
            $categorieOptions = array_map(function ($category) {
                return ['name' => trim($category)];
            }, $categories);
        } else {
            // Si le fichier n'existe pas, initialisez $categorieOptions en tant que tableau vide
            $categorieOptions = [];
        }
        $contenuJson = $serviceEnChambres->getContenu();
        $typeserviceEnChambreRepository = $this->entityManager->getRepository(TypeServiceEnChambre::class);
        $typeserviceEnChambres = $typeserviceEnChambreRepository->findAll();
        // Décoder le JSON en tableau associatif
        $data = json_decode($contenuJson, true);
        $formatContenu = 'format1';
            $data2 = [];
            $data3 = [];
            $jsonData = json_decode($contenuJson, true);
            if ( isset($jsonData[0]['devise'])) {
                $formatContenu = 'format1';
                $data2 = $jsonData;
                $data3 = [['nom' => '', 'quantite' => '']];
            } elseif ( isset($jsonData[0]['nom'])) {
                $formatContenu = 'format2';
                $data3 = $jsonData;
                $data2 = [['devise' => '', 'prix' => '', 'qmin' => '', 'qmax' => '']];
            }
        $listrequete = $serviceEnChambres->getContenu();
        return $this->render('service_en_chambre/modifier.html.twig', [
            'user' => $user,
            'serviceEnChambresdetails'=>$serviceEnChambres,
            'typeserviceEnChambres'=>$typeserviceEnChambres,
            'ancienLogo' => $ancienLogo,
            'categorieOptions' => $categorieOptions,
            'data' => $data,
            'data2' => $data2,
            'data3' => $data3,
            'listrequete' => $listrequete,
            'formatContenu' => $formatContenu,
        ]);
    }

    //methode d'enregistrer categorie du menu dans fichier restaurant.txt
    #[Route('/ajouter_categorie_service_file/{id}', name: 'ajouter_categorie_service_file')]
    public function ajouterCategorieServicefile(Request $request, $id)
    {
        // Récupération de l'utilisateur actuel
        $user = $this->getUser();
        // Récupération du repository pour ServiceEnChambre
        $serviceEnChambreRepository = $this->entityManager->getRepository(ServiceEnChambre::class);
        // Recherche du service en chambre spécifié par l'ID
        $serviceEnChambres = $serviceEnChambreRepository->find($id);
        // Assurez-vous que le répertoire 'MenuServiceEnChambre' existe
        $publicDirectory = __DIR__ . '/../../public/MenuServiceEnChambre'; // direction du fichier MenuServiceEnChambre
        // Utilisez le nom obtenu à partir de getNom() comme nom de fichier
        $fileName = $serviceEnChambres->getId() . '.txt';
        $filePath = $publicDirectory . '/' . $fileName;
        // Récupération du nom de catégorie à partir de la requête
        $nom = $request->get("nomcat");
        $nomAEnregistrer = $nom . "\n";
        // Ajout du nom de catégorie au fichier avec FILE_APPEND pour ajouter à la fin du fichier existant
        file_put_contents($filePath, $nomAEnregistrer, FILE_APPEND | LOCK_EX);
                $publicDirectory = __DIR__ . '/../../public/MenuServiceEnChambre';
                $filePath = $publicDirectory . '/' . $id . '.txt';
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            $categories = explode("\n", $fileContent);
            // Convertir chaque élément en chaîne de caractères
            $categories = array_map('strval', $categories);
            // Supprimer les éléments vides du tableau
            $categories = array_filter($categories);
            // Transformation du tableau de catégories en un tableau associatif avec la clé 'name'
            $categorieOptions = array_map(function ($category) {
                return ['name' => trim($category)];
            }, $categories);
        } else {
            // Si le fichier n'existe pas, initialisez $categorieOptions en tant que tableau vide
            $categorieOptions = [];
        }
        $contenuJson = $serviceEnChambres->getContenu();
         // Décoder le JSON en tableau associatif
        $data = json_decode($contenuJson, true);
        $typeserviceEnChambreRepository = $this->entityManager->getRepository(TypeServiceEnChambre::class);
        $typeserviceEnChambres = $typeserviceEnChambreRepository->findAll();
        $formatContenu = 'format1';
        // Rendu d'une vue avec des détails supplémentaires
        return $this->render('service_en_chambre/modifier.html.twig', [
            'user' => $user,
            'serviceEnChambresdetails' => $serviceEnChambres,
            'categorieOptions' => $categorieOptions,
            'data' => $data,
            'typeserviceEnChambres' => $typeserviceEnChambres,
            'formatContenu' => $formatContenu,
        ]);
    }

    //Affichage  categorie du menu
    #[Route('/get_categorie_options/{id}', name: 'get_categorie_options')]
    public function getCategorieOptions($id)
    {
        // Définition du chemin vers le répertoire public contenant les fichiers de MenuServiceEnChambre
        $publicDirectory = __DIR__ . '/../../public/MenuServiceEnChambre'; // direction du fichier MenuServiceEnChambre
         // Construction du chemin complet vers le fichier en fonction du nom fourni en paramètre
        $filePath = $publicDirectory . '/' . $id . '.txt';
        // Vérification de l'existence du fichier
        if (file_exists($filePath)) {
            // Lecture du contenu du fichier
            $fileContent = file_get_contents($filePath);
            // Séparation du contenu du fichier en lignes pour obtenir un tableau de catégories
            $categories = explode("\n", $fileContent);
           // Suppression des éléments vides du tableau
            $categories = array_filter($categories);
            // Transformation du tableau de catégories en un tableau associatif avec la clé 'name'
            $categorieOptions = array_map(function ($category) {
                return ['name' => trim($category)];
            }, $categories);
        } else {
            // Si le fichier n'existe pas, initialisez $categorieOptions en tant que tableau vide
            $categorieOptions = [];
        }
        // Retourne les catégories au format JSON
        return new JsonResponse($categorieOptions);
    }

    #[Route('/service_en_chambre/supprimer/{id}', name: 'app_supprimer_service_en_chambre')]
    public function supprimerserviceenchambre(EntityManagerInterface $entityManager, int $id): Response
    {
        $chambre = $entityManager->getRepository(ServiceEnChambre::class)->find($id);
 
        if (!$chambre) {
            throw $this->createNotFoundException(
                'No room found for id '.$id
            );
        }
 
        $entityManager->remove($chambre);
        $entityManager->flush();
 
        return $this->redirectToRoute('service_en_chambre');
    }
    //methode modification service en chambre
    #[Route('/modifierservice/{id}', name: 'modifierservice')]
    public function modifierservice(EntityManagerInterface $entityManager, Request $request, $id): Response
    {
        // Récupérer les données du formulaire
        $nom = $request->request->get('nom');
        //$active = $request->request->get('active');
        $description = $request->request->get('Description');
        $EN = $request->request->get('EN');
        $ES = $request->request->get('ES');
        $PT = $request->request->get('PT');
        $IT = $request->request->get('IT');
        $RU = $request->request->get('RU');
        $DE = $request->request->get('DE');
        $ZH = $request->request->get('ZH');
        $AR = $request->request->get('AR');
        $typeServiceEnChambreId = $request->request->get('type_service_en_chambre');
        $typeServiceEnChambre = $this->entityManager->getRepository(TypeServiceEnChambre::class)->find($typeServiceEnChambreId);
        $type=$typeServiceEnChambre->getNom();
        if( $type == 'List') {
            $contenu = $request->request->get('listrequte');
        }else if($type == 'Achat'){ 
            $typePrix = $request->request->get('typePrix');
             if($typePrix=="prixnormal1"){
                $devise = $request->request->get('Devise');
                $prix=$request->get('prix');
                $qmin=$request->get('qmin');
                $qmax=$request->get('qmax');
                $time=$request->get('time');
                $timeArray = [];
                foreach ($time as $value) {
                    $timeArray[] = intval($value); // Convertir la valeur en entier
                }
                $contenu=array("devise"=>$devise,"prix"=>$prix,"qmin"=>$qmin,"qmax"=>$qmax,"time" => $timeArray);
             }else{
                $inputqte = $request->get('inputqte');
                $inputprice = $request->get('inputprice');
                $arrsize  = sizeof($inputqte);
                $contenu=array();
                $time=$request->get('time');
                $timeArray = [];
                foreach ($time as $value) {
                    $timeArray[] = intval($value); // Convertir la valeur en entier
                }
                for($i=0;$i<$arrsize;$i++){
                $contenu[$i]=array("nom"=>$inputqte[$i],"quantite"=>$inputprice[$i],"time" => $timeArray);
                }
            }
        }elseif($type == "Menu"){
            $inputname = $request->get('inputname');
            $inputdesc = $request->get('inputdesc');
            $inputprice = $request->get('inputprice');
            $inputcat = $request->get('inputcat');
            $arrsize  = sizeof($inputname);
            $merged_array=array();
            for($i=0;$i<$arrsize;$i++){
            $desc[$i]=array("nom"=>$inputname[$i],"description"=>$inputdesc[$i],"prix"=>$inputprice[$i],"cat"=>$inputcat[$i]);
            }
            $merged_array=json_encode($desc);
        }if($type == 'Prise rendez vous'){ 
            $Descriptionprise=$request->get('Descriptionprise');
            $debutlundi=$request->get('debutlundi');
            $Finlundi=$request->get('Finlundi');
            $debutmardi=$request->get('debutmardi');
            $Finmardi=$request->get('Finmardi');
            $debutmerc =$request->get('debutmerc');
            $Finmerc=$request->get('Finmerc');
            $debutjeudi  =$request->get('debutjeudi');
            $Finjeudi=$request->get('Finjeudi');
            $debutven =$request->get('debutven');
            $Finven=$request->get('Finven');
            $debutsam  =$request->get('debutsam');
            $Finsam=$request->get('Finsam');
            $debutdim  =$request->get('debutdim');
            $Findim=$request->get('Findim');
            $contenu=array(
            "Description"=>$Descriptionprise,
            "Lundi"=>["debut"=>$debutlundi,"fin"=>$Finlundi],
            "Mardi"=>["debut"=>$debutmardi,"fin"=>$Finmardi],
            "Merecredi"=>["debut"=>$debutmerc ,"fin"=>$Finmerc],
            "Jeudi"=>["debut"=>$debutjeudi ,"fin"=>$Finjeudi],
            "Vendredi"=>["debut"=>$debutven ,"fin"=>$Finven],
            "Samedi"=>["debut"=>$debutsam,"fin"=>$Finsam],
            "Dimanche"=>["debut"=>$debutdim,"fin"=>$Findim],
            );
        }
        // Récupérer l'entité ServiceEnChambre à modifier
        $serviceEnChambre = $entityManager->getRepository(ServiceEnChambre::class)->find($id);

        if (!$serviceEnChambre) {
            throw $this->createNotFoundException('Service en chambre non trouvé pour l\'ID '.$id);
        }
        // Mettre à jour les propriétés de l'entité ServiceEnChambre
        $serviceEnChambre->setNom($nom);
        //$serviceEnChambre->setActive($active);
        $serviceEnChambre->setDescription($description);
        $serviceEnChambre->setEn($EN);
        $serviceEnChambre->setEs($ES);
        $serviceEnChambre->setPt($PT);
        $serviceEnChambre->setIt($IT);
        $serviceEnChambre->setRu($RU);
        $serviceEnChambre->setDe($DE);
        $serviceEnChambre->setZh($ZH);
        $serviceEnChambre->setAr($AR);
       if($type=="Menu" || $type=="menu"){
        $serviceEnChambre->setContenu($merged_array);
        }else if(($typeServiceEnChambre->getNom()=="Achat" || $typeServiceEnChambre->getNom()=="Achat")){
            $typePrix = $request->request->get('typePrix');
            if($typePrix=="prixnormal1"){
            $desc=json_encode([$contenu]);
            $serviceEnChambre->setContenu($desc);
            }
            else{
                $desc=json_encode($contenu);
                $serviceEnChambre->setContenu($desc);
            }
        }else if(($typeServiceEnChambre->getNom()=="List" || $typeServiceEnChambre->getNom()=="list")){
            $serviceEnChambre->setContenu($contenu);
        }else if(($typeServiceEnChambre->getNom()=="Prise rendez vous" || $typeServiceEnChambre->getNom()=="prise rendez vous")){
            $desc=json_encode($contenu);
            $serviceEnChambre->setContenu($desc);
            
        }
        // Récupérer l'entité TypeServiceEnChambre
        // Gérer le fichier logo s'il est envoyé
        $logoFile = $request->files->get('logo');
        if ($logoFile) {
            // Générer un nom de fichier unique
            $newFilename = uniqid() . '.' . $logoFile->guessExtension();

            try {
                // Déplacer le fichier vers le répertoire d'upload
                $logoFile->move(
                    $this->getParameter('service_in_room_directory'), 
                    $newFilename
                );
            } catch (FileException $e) {
                // Gérer les erreurs liées au déplacement du fichier
                // ...
            }
            // Mettre à jour le chemin du logo dans l'entité ServiceEnChambre
            $serviceEnChambre->setLogo($newFilename);
        }
        // Persistir et flush l'entité
        $entityManager->flush();
        $user = $this->getUser();
            // Récupération du repository pour ServiceEnChambre
        $serviceEnChambreRepository = $this->entityManager->getRepository(ServiceEnChambre::class);
            // Recherche du service en chambre spécifié par l'ID
        $serviceEnChambres = $serviceEnChambreRepository->find($id);
        $publicDirectory = __DIR__ . '/../../public/MenuServiceEnChambre';
        $filePath = $publicDirectory . '/' . $id . '.txt';
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            $categories = explode("\n", $fileContent);
            // Convertir chaque élément en chaîne de caractères
            $categories = array_map('strval', $categories);
            // Supprimer les éléments vides du tableau
            $categories = array_filter($categories);
            // Transformation du tableau de catégories en un tableau associatif avec la clé 'name'
            $categorieOptions = array_map(function ($category) {
                return ['name' => trim($category)];
            }, $categories);
        } else {
            // Si le fichier n'existe pas, initialisez $categorieOptions en tant que tableau vide
            $categorieOptions = [];
        }
        $contenuJson = $serviceEnChambres->getContenu();
        // Décoder le JSON en tableau associatif
        $data = json_decode($contenuJson, true);
        $listrequete = $serviceEnChambres->getContenu();
        $typeserviceEnChambreRepository = $this->entityManager->getRepository(TypeServiceEnChambre::class);
        $typeserviceEnChambres = $typeserviceEnChambreRepository->findAll();    
        $formatContenu = 'format1';
         $data2 = [];
         $data3 = [];
         $jsonData = json_decode($contenuJson, true);
          // supposez que le format par défaut soit format1
         if ( isset($jsonData[0]['devise'])) {
             $formatContenu = 'format1';
             $data2 = $jsonData;
             $data3 = [['nom' => '', 'quantite' => '']];
         } elseif ( isset($jsonData[0]['nom'])) {
             $formatContenu = 'format2';
             $data3 = $jsonData;
             $data2 = [['devise' => '', 'prix' => '', 'qmin' => '', 'qmax' => '']];
         } 
        $listrequete = $serviceEnChambres->getContenu();
        // Rediriger vers une page de succès ou une autre page
        return $this->render('service_en_chambre/modifier.html.twig', [
            'user' => $user,
            'serviceEnChambresdetails'=>$serviceEnChambres,
            'typeserviceEnChambres'=>$typeserviceEnChambres,
            'categorieOptions' => $categorieOptions,
            'data' => $data,
            'listrequete' => $listrequete,
            'data2' => $data2,
            'data3' => $data3,
            'formatContenu' => $formatContenu,
        ]);
    }

   // Méthode pour mettre à jour la position du service en chambre
    #[Route('/update-position/{id}', name: 'update_position', methods:['POST'])]
    public function updatePosition(Request $request, ServiceEnChambre $serviceEnChambre)
    {
        $position = $request->request->get('position');
        $serviceEnChambre->setPosition($position);
        $this->entityManager->flush();
        return new JsonResponse(['success' => true]);
    }

    // Méthode pour mettre à jour Activation du service en chambre
    #[Route('/update-active-service', name: 'update_active_service')]
    public function updateActiveService(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $serviceId = $request->request->get('id');
        $isActive = $request->request->get('active');
        // Récupérer l'entité ServiceEnChambre correspondante depuis la base de données
        $service = $entityManager->getRepository(ServiceEnChambre::class)->find($serviceId);
        if (!$service) {
            return new JsonResponse(['error' => 'Service en chambre non trouvé'], Response::HTTP_NOT_FOUND);
        }
        // Mettre à jour l'état du service en chambre
        $service->setActive($isActive);
        $entityManager->flush();
        return new JsonResponse(['message' => 'État mis à jour avec succès'], Response::HTTP_OK);
    }


   
}


