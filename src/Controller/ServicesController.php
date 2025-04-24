<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Chambre;
use App\Entity\ConfigApp;
use App\Entity\Etablissement;
use App\Entity\LancerAnnonce;
use App\Entity\LancerRadio;
use App\Entity\Lancerservice;
use App\Entity\LancerTV;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Services;
use App\Push\PushRabbit;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class ServicesController extends AbstractController
{
    private $entityManager;
    private HttpClientInterface $httpClient;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
    }
   
    ///////////////////////////  route pour afficher tous les services ////////////////////////////////////////////////////

    #[Route('/services', name: 'app_services')]
    public function index(Security $security): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');     
        if( !$this->getUser())
          return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
           $Acce = $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        $categorie = $this->entityManager->getRepository(Categories::class)
        ->findOneBy([
            'etablissement' => $etablissement,
            'package' => 'Service'
        ]);
        $id_slected = [];
        $id_slected = ['id'=>$categorie->getId()];
        $id_slectedJson = json_encode($id_slected);
        $services  = $this->entityManager->getRepository(Services::class)->findBy(['etablissement' => $etablissement,'categories' => $categorie->getId()],['position' => 'ASC']);
        $serviceArray = [];
        foreach ($services as $service1) {
            $serviceArray[] = $service1->getPosition();
        }
        $chembre = $this->entityManager->getRepository(Chambre::class)->findBy(['etablissement' =>$etablissement]);
        $chembreArray = [];
        foreach ($chembre as $chambre) {
            $chembreArray[] = ['id'=>$chambre->getId(),'nom' => $chambre->getNom(),
                        'ip' => $chambre->getIp(),
                        'Mac' => $chambre->getMac(),
        ];
        }
        $chembreJson = json_encode($chembreArray);
        $serviceJson = json_encode($serviceArray);
        $categories = $this->entityManager->getRepository(Categories::class)
        ->findBy([
            'etablissement' => $etablissement,
            'package' => 'Service'
        ]);
        $categorieArray = [];
        foreach ($categories as $categorie) {
            $categorieArray[] = ['nom'=>$categorie->getNom(),'id'=>$categorie->getId(),'allService'=>$categorie->getServices()];
            }
        $categorieJson = json_encode($categorieArray);
        return $this->render('services/index.html.twig', ['services' => $services,
        'service1' =>$serviceJson,
        'categorie' =>$categorieJson,
        'id' => $categorie->getId(),
        'id_slected' => $id_slectedJson,
        'chembre' => $chembreJson,
        'appConfig' => $configApp,
        'user' => $this->getUser(),
        
    ]);
    }

 
///////////////////////////  route pour ajouter un service sur base de données ////////////////////////////////////////////////////

#[Route('/services/ajouter', name: 'app_ajouter_services')]
public function ajouterService(Request $request): Response
{
    if( !$this->getUser())
    return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
    $Acce = $this->getUser()->getAjouteService() && $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
    if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');    }   
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED'); 
    $service = new Services();
    $directory = $this->getParameter('services_directory');

    $service->setEtablissement($etablissement);
    if($request)
    {
    try {
        $nom = $request->get('nom');
        $active = filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN);
        $position = (int) $request->get('service_position');
        $type = $request->get('service_type');
        $description = $request->get('Description');
        $url = $request->get('url');
        $file2 = $request->files->get('service_src');
        $file3= $request->files->get('service_src1');
        $serviceServiceId = $request->get('service_service');
        $tempDisplays = $request->get('timeInput', []); 
        $orderDisplays = $request->get('numberInput', []); 
        
        $file1 = $request->files->get('ajout_logo1');
        if ($file1) {
            $extension = $file1->guessExtension() ?: pathinfo($file1->getClientOriginalName(), PATHINFO_EXTENSION) ?: 'bin';
            $fileName = md5(uniqid()) . '.' . $extension;
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file1->getMimeType(), $allowedMimeTypes)) {
                throw new \RuntimeException('Invalid image format. Please upload JPEG, PNG, GIF, or WebP images.');
            }
        
            if (@getimagesize($file1->getPathname()) === false) {
                throw new \RuntimeException('The uploaded file is not a valid image.');
            }
           
                    $file1->move($directory, $fileName);
                    $service->setLogo('images/services/' . $fileName);

            } 
        if ($file2 || $url || $file3) {
            if ($url) {
                
                $service->setSrc($url);
            } else {
                if ($type === 'DIAPO' && $file3) {
                    $this->handleDiapoFiles($file3, $tempDisplays, $orderDisplays, $directory, $service);
                } else {
                    $fileName = md5(uniqid()) . '.' . $file2->guessExtension();
                    $file2->move($directory, $fileName);
                    $service->setSrc('images/services/' . $fileName);
                }
            }
        }
        $service->setFr($nom);
        $translations = ['EN', 'ES', 'PT', 'IT', 'RU', 'DE', 'ZH', 'AR'];
        foreach ($translations as $lang) {
            $setter = "set$lang";
            $service->$setter($request->get($lang));
        }

        $service->setNom($nom);
        $service->setActive($active);
        $service->setPosition($position);
        $service->setType($type);
        $service->setDescription($description);

        $categorie = $this->entityManager->getRepository(Categories::class)->find($serviceServiceId);
        if ($categorie) {
            $service->setCategories($categorie);
        }

        $this->entityManager->persist($service);
        $this->entityManager->flush();

    } catch (\Exception $e) {
        return new Response('Erreur lors de la soumission du formulaire : ' . $e->getMessage(), 500);
    }

    return $this->redirectToRoute('services_Categorie', ['id' => $serviceServiceId]);
}
return new JsonResponse(['error' => 'request is empty'], 500);
}
    

///////////////////////////  La fonction pour ajouter les images de diapo sur un dossier interne. ////////////////////////////////////////////////////

private function handleDiapoFiles($files, $tempDisplays, $orderDisplays, $directory, $service): void
{
    $filesystem = new Filesystem();
    $counter = 0;
    $aliatoire = '_diapo' . uniqid();
    $diapoDirectory = $directory . '/diapo/' . $aliatoire;
    if (!$filesystem->exists($diapoDirectory)) {
        $filesystem->mkdir($diapoDirectory, 0755);
    }

    foreach ($files as $file) {
        if (!isset($orderDisplays[$counter], $tempDisplays[$counter])) {
            continue;
        }
        $extension = $file->guessExtension();
        $newFilename = $orderDisplays[$counter] . '_' . $tempDisplays[$counter] . '.' . $extension;
        try {
            $file->move($diapoDirectory, $newFilename);
        } catch (FileException $e) {
            throw new \Exception('An error occurred while uploading the file: ' . $e->getMessage());
        }

        $counter++;
    }

    $service->setSrc('images/services/diapo/' . $aliatoire);
}

///////////////////////////  route pour afficher le formulaire d'ajout de service ////////////////////////////////////////////////////

#[Route('/services/create_Service/{id_categorie}', name: 'create_Service')]
    public function Create_service(int $id_categorie): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED'); 
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getAjouteService() && $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1' ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }        
        $categories = $this->entityManager->getRepository(Categories::class)
        ->findBy([
            'etablissement' => $etablissement,
            'package' => 'Service'
        ]);
        $categorieArray = [];
        foreach ($categories as $categorie) {
            $categorieArray[] = ['nom'=>$categorie->getNom(),'id'=>$categorie->getId()];
            }
        $categorieJson = json_encode($categorieArray);
        if ($configApp) {
            $configArray = ['Status'=>$configApp->getStatusServeur()];
        } 
        else{
            $configArray = ['Status'=>'online'];
        }
        $configJson = json_encode($configArray);     
        $directory = $this->getParameter('project_dir') . '/public/images/imageIcone';
        if (!is_dir($directory)) {
            return new JsonResponse(['error' => 'Image directory not found'], 500);
        }
        $files = array_diff(scandir($directory), ['.', '..']);
        $directory = 'images/imageIcone/';
        $files = scandir($directory);
        $images = array_map(
            fn($file) => $directory . $file, 
            array_filter($files, fn($file) => is_file($directory . $file))
        );
        return $this->render('services/ajouter.html.twig', [
            'images' => $images,
            'categorie' =>$categorieJson,
            'id_categorie' => $id_categorie,
            'configApp' => $configJson,
            'appConfig' => $configApp,
            'user' => $this->getUser(),

        ]);
    }

///////////////////////////  route pour afficher tout les services de chaque categorie a partir l'Id de categorie ////////////////////////////////////////////////////

#[Route('/services/categorie/{id}', name: 'services_Categorie')]
public function display_service(string $id){
    if( !$this->getUser())
   return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    $Acce = $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1' ;
    if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');    }
    // $this->denyAccessUnlessGranted($Acce, $this->getUser());
    $id = (int) $id; 
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED'); 
    $services  = $this->entityManager->getRepository(Services::class)->findBy(['etablissement' => $etablissement,'categories' => $id],['position' => 'ASC']);
    $chembre = $this->entityManager->getRepository(Chambre::class)->findBy(['etablissement' =>$etablissement]);
    $chembreArray = [];
    $serviceArray = [];
    foreach ($chembre as $chambre) {;  
        $chembreArray[] = ['id'=>$chambre->getId(),'nom' => $chambre->getNom(),
        'ip' => $chambre->getIp(),
        'Mac' => $chambre->getMac(),
    ];
}
    $chembreJson = json_encode($chembreArray) ;
    $serviceJson = json_encode($serviceArray);
    $categories = $this->entityManager->getRepository(Categories::class)
    ->findBy([
        'etablissement' => $etablissement,
        'package' => 'Service'
    ]);
    $categorieArray = [];
    foreach ($categories as $categorie) {
        $categorieArray[] = ['nom'=>$categorie->getNom(),'id'=>$categorie->getId(),'allService'=>$categorie->getServices()];
        }
        $categorieJson = json_encode($categorieArray);
    $id_slected = [];
    $id_slected = ['id'=>$id];
    $id_slectedJson = json_encode($id_slected);

    return $this->render('services/index.html.twig', ['services' => $services,
    'service1' =>$serviceJson,
    'categorie' =>$categorieJson,
    'id' =>$id,
    'id_slected'=>$id_slectedJson,
    'chembre' => $chembreJson,
    'appConfig' => $configApp,
    'user' => $this->getUser(),


]);       
}


///////////////////////////  route pour modifier les informations de plusieurs services en même temps ////////////////////////////////////////////////////

#[Route('/update/{id}', name:'update_services')]
public function update_services(int $id,Request $request){
    if( !$this->getUser())
   return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    $Acce = $this->getUser()->getSauvegarderService() && $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
    if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');    } 
    $repository = $this->entityManager->getRepository(Services::class);
    $Services  = $repository->findBy(['etablissement' => $etablissement,'categories'=> $id]);
    $request = Request::createFromGlobals();
    foreach ($Services as $tele) {
        $tele->setActive(0);
        $this->entityManager->persist($tele);
        $this->entityManager->flush();
      }
    $nom = $request->get("nom_service");
             if (isset($nom) and !empty($nom)) {
                foreach ($nom as $key => $k) {
                    $nom_service  = $repository->findById($key);
                     $nom_service[0]->setNom($k);
                     $this->entityManager->persist($nom_service[0]);
                     $this->entityManager->flush();
                }
             }
    $position = $request->get("position_service");
             if (isset($position) and !empty($position)) {
                foreach ($position as $key => $k) {
                    $position_service  = $repository->findById($key);
                     $position_service[0]->setPosition($k);
                     $this->entityManager->persist($position_service[0]);
                     $this->entityManager->flush();
                }
             }
    $active = $request->get("active");
             if (isset($active) and !empty($active)) {
                foreach ($active as $key => $k) {
                    $active_service  = $repository->findById($key);
                     $active_service[0]->setActive($k);
                     $this->entityManager->persist($active_service[0]);
                     $this->entityManager->flush();
                }
             }
        return $this->redirectToRoute('services_Categorie',['id' =>$id]);
    }

    ///////////////////////////   route pour afficher le formulaire avec les informations de service pour le modifier. ////////////////////////////////////////////////////
     #[Route('/services/find_service/{id}', name: 'find_service')]
    public function findService(int $id): Response
    {
        try{
            if( !$this->getUser())
   return $this->redirectToRoute('app_login');
            $etablissement = $this->getUser()->getEtablissement();
            $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $Acce = $this->getUser()->getModifierService() && $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
        $categories = $this->entityManager->getRepository(Categories::class)
        ->findBy([
            'etablissement' => $etablissement,
            'package' => 'Service'
        ]);
        $categorieArray = [];
        foreach ($categories as $categorie) {
            $categorieArray[] = ['nom'=>$categorie->getNom(),'id'=>$categorie->getId()];
            }
            $categorieJson = json_encode($categorieArray);
        $service = $this->entityManager->getRepository(Services::class)->findOneBy([
            'etablissement' => $etablissement,
            'id' => $id
        ]);
        if (!$service) {
                throw new \Exception( 'Service not found' . $service);
        }
        $categorie = $service->getCategories();
        if (!$categorie) {
            throw new \Exception( 'Categorie not found' . $categorie);
        }
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        if ($configApp) {
            $configArray = ['Status'=>$configApp->getStatusServeur()];
        } 
        else{
            $configArray = ['Status'=>'online'];
        }
        $configJson = json_encode($configArray);
        $directory = $this->getParameter('project_dir') . '/public/images/imageIcone';
        if (!is_dir($directory)) {
            throw new \Exception( 'Image directory not found');
        }
    
        $files = array_diff(scandir($directory), ['.', '..']);
        $directory = 'images/imageIcone/';
        $files = scandir($directory);
        $images = array_map(
            fn($file) => $directory . $file, 
            array_filter($files, fn($file) => is_file($directory . $file))
        );
        $ImagesArray = [];
        if($service->getType() === 'DIAPO')
        {
        $dir_nom = $this->getParameter('project_dir') . '/public/' . $service->getSrc();

        if (!file_exists($dir_nom)) {
            throw new \Exception( 'Path does not exist: ' . $dir_nom);
        }
        if (!is_dir($dir_nom)) {
            throw new \Exception( 'Path is not a directory: ' . $dir_nom);
        }
        $files1 = array_diff(scandir($dir_nom), ['.', '..']);
        $ImagesArray = array_values($files1);
        usort($ImagesArray, function ($a, $b) {
            $numA = (int) explode('_', $a)[0];
            $numB = (int) explode('_', $b)[0];
            return $numA <=> $numB;
        });    }
    $src = [];
    $src = ['src'=>$service->getSrc()];
    $srcJson = json_encode($src);
    $id_service = [];
    $id_service = ['id'=>$id];
    $id_serviceJson = json_encode($id_service);
    }
    catch(\Exception $e) {
        $src = [];
        $src = ['src'=>$service->getSrc()];
        $srcJson = json_encode($src);
        $id_service = [];
        $id_service = ['id'=>$id];
        $id_serviceJson = json_encode($id_service);       
        return $this->render('services/modifier.html.twig', parameters: [
            'service' => $service,
            'images' => $images,
            'categorie' =>$categorieJson,
            'images1' =>$ImagesArray,
            'src' =>$srcJson,
            'id_service' => $id_serviceJson,
            'id_categorie' => $categorie->getId(),
            'configApp' => $configJson,
            'user' => $this->getUser(),
        ]);
    } 
    return $this->render('services/modifier.html.twig', parameters: [
        'service' => $service,
        'categorie1'=>$categorie->getNom(),
        'images' => $images,
        'categorie' =>$categorieJson,
        'images1' =>$ImagesArray,
        'src' =>$srcJson,
        'id_service' => $id_serviceJson,
        'id_categorie' => $categorie->getId(),
        'configApp' => $configJson,
        'appConfig' => $configApp,
        'user' => $this->getUser(),
    ]);
}
    


     ///////////////////////////  route pour enregistrer la modification de service sur base de donnée ////////////////////////////////////////////////////
    #[Route('/serviceUpdate/{id}', name: 'update_service')]
    public function updateService(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
   return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getModifierService() && $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
            $categories = $this->entityManager->getRepository(Categories::class)->findBy([
            'etablissement' => $etablissement,
            'package' => 'Service',
        ]);
    
        $categorieArray = array_map(fn($categorie) => [
            'nom' => $categorie->getNom(),
            'id' => $categorie->getId(),
        ], $categories);
    
        $service = $this->entityManager->getRepository(Services::class)->findOneBy([
            'etablissement' => $etablissement,
            'id' => $id,
        ]);
    
        if (!$service) {
            throw $this->createNotFoundException('Service not found');
        }
    
        $directory = $this->getParameter('services_directory');
        $oldDirectory = '';
        if($service->getType() === 'DIAPO')
        $oldDirectory = $this->getParameter('project_dir') . '/public/' . $service->getSrc();
    
        if ($request->isMethod('POST')) {
            try{
            $nom = $request->get('nom');
            $active = $request->get('active');
            $position = $request->get('service_position');
            $type = $request->get('service_type');
            $description = $request->get('Description');
            $serviceService = $request->get('service_service');
            $url = $request->get('url');
            $file3 = $request->files->get('service_src1');
            $file2 = $request->files->get('service_src');

            $file1 = $request->files->get('ajout_logo1');
            $tempDisplays = $request->get('timeInput', []);
            $orderDisplays = $request->get('numberInput', []);
            
            // dump($tempDisplays,$orderDisplays).die();
            $dir_nom = $this->getParameter('project_dir') . '/public/' . $service->getSrc();
            // dump($dir_nom);
            if ($type === 'DIAPO'){
            $this->handleDiapoFiles1( $file3,$oldDirectory, $tempDisplays, $orderDisplays, $directory, $service);
            if($service->getType() !== 'DIAPO' && file_exists($dir_nom))
                unlink($dir_nom); 
        }
            if ($file1) {
                    $logo= $this->getParameter('project_dir') . '/public/' . $service->getLogo();
                    if(file_exists($logo)) 
                            unlink($logo); 
                    $extension = $file1->guessExtension() ?: pathinfo($file1->getClientOriginalName(), PATHINFO_EXTENSION) ?: 'bin';
                    $fileName = md5(uniqid()) . '.' . $extension;
                    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($file1->getMimeType(), $allowedMimeTypes)) {
                        throw new \RuntimeException('Invalid image format. Please upload JPEG, PNG, GIF, or WebP images.');
                    }
                
                    if (@getimagesize($file1->getPathname()) === false) {
                        throw new \Exception('The uploaded file is not a valid image.');
                    }
                   
                            $file1->move($directory, $fileName);
                            $service->setLogo('images/services/' . $fileName);
        
                    } 
                if ($file2 || $url) {
                    if ($url)
                    {
                        $service->setSrc($url);
                        if($service->getType() === 'DIAPO'){
                            if (!file_exists($dir_nom)) {
                                throw new \Exception( 'Path does not exist: ' . $dir_nom);
                            }
                            if (!is_dir($dir_nom)) {
                                throw new \Exception('Path is not a directory: ' . $dir_nom);
                            }
                            else{
                                $this->deleteDirectory($dir_nom);
                            }
                        }
                        else if(file_exists($dir_nom)) 
                            unlink($dir_nom);
                        
                    }
                    elseif($file2 && $type !== 'DIAPO') {
                        $fileName2 = md5(uniqid()) . '.' . $file2->guessExtension();
                        $file2->move($directory, $fileName2);
                        $service->setSrc('images/services/' . $fileName2);
                    }
                }
                if ($type !== 'DIAPO' && $type !== 'URL'){
                    if($service->getType() === 'DIAPO'){
                        if (!file_exists($dir_nom)) {
                            throw new \Exception( 'Path does not exist: ' . $dir_nom);
                        }
                        if (!is_dir($dir_nom)) {
                            throw new \Exception('Path is not a directory: ' . $dir_nom);
                        }
                        else{
                            $this->deleteDirectory($dir_nom);
                        }
                    }
                    else if(file_exists($dir_nom) && $file2) 
                        unlink($dir_nom);
                }
            $translations = [
                'FR' => $nom,
                'EN' => $request->get('EN'),
                'ES' => $request->get('ES'),
                'PT' => $request->get('PT'),
                'IT' => $request->get('IT'),
                'RU' => $request->get('RU'),
                'DE' => $request->get('DE'),
                'ZH' => $request->get('ZH'),
                'AR' => $request->get('AR'),
            ];
            $service->setNom($nom);
            $service->setActive($active);
            $service->setPosition($position);
            $service->setType($type);
            $service->setDescription($description);
    
            foreach ($translations as $lang => $value) {
                $setMethod = 'set' . $lang;
                if (method_exists($service, $setMethod)) {
                    $service->$setMethod($value);
                }
            }
            $categorie = $this->entityManager->getRepository(Categories::class)->find($serviceService);
            if ($categorie) {
                $service->setCategories($categorie);
            }  
        }catch(\Exception $e)
        {
            throw new \Exception('Error : ' . $e->getMessage());
        }
        }
    
        $this->entityManager->persist($service);
        $this->entityManager->flush();

        return $this->redirectToRoute('services_Categorie', ['id' => $serviceService]);    }
    
///////////////////////////  La fonction pour supprimer un dossier non vide ////////////////////////////////////////////////////
    private function deleteDirectory(string $dirPath): bool
    {
        if (!is_dir($dirPath)) {
            return false;
        }
    
        $items = array_diff(scandir($dirPath), ['.', '..']);
        foreach ($items as $item) {
            $itemPath = $dirPath . DIRECTORY_SEPARATOR . $item;
            is_dir($itemPath) ? $this->deleteDirectory($itemPath) : unlink($itemPath);
        }
    
        return rmdir($dirPath);
    }
    
///////////////////////////  fonction pour verifier est ce que le dossier est vide  ////////////////////////////////////////////////////
    private function dir_is_empty($dir) {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
          if ($entry != "." && $entry != "..") {
            closedir($handle);
            return false;
          }
        }
        closedir($handle);
        return true;
      }

///////////////////////////  fonction pour genere un dossier contenant les images de diapo modifier ////////////////////////////////////////////////////

    private function handleDiapoFiles1($files, $Olddirectory, $tempDisplays, $orderDisplays, $directory, $service): void
    {
        $filesystem = new Filesystem();
        $aliatoire = '_diapo' . uniqid();

        $ImagesArray = [];
        $files1 = [];
        if($Olddirectory && file_exists($Olddirectory)) 
            $files1 = array_diff(scandir($Olddirectory), ['.', '..']);
        $diapoDirectory = $directory . '/diapo/' . $aliatoire;
        if($files1)
            $ImagesArray = array_values($files1);
        if($files){
            foreach ($files as $newFile) {
                if (is_file($newFile)) {
                    array_push($ImagesArray, $newFile);
                }
            }
        }
        if (!$filesystem->exists($diapoDirectory)) {
            $filesystem->mkdir($diapoDirectory, 0755);
        }
        foreach ($ImagesArray as $index => $newFile) {
            if ($newFile instanceof UploadedFile) {
                if (!isset($orderDisplays[$index], $tempDisplays[$index])) {
                    continue;
                }
                $extension = $newFile->getClientOriginalExtension();
                $newFilename = $orderDisplays[$index] . '_' . $tempDisplays[$index] . '.' . $extension;
                $newFilePath = $newFile->getRealPath();
                $newFileDestination = rtrim($diapoDirectory, '/') . '/' . $newFilename;
    
                try {
                    if (!$newFile->move($diapoDirectory, $newFilename)) {
                        throw new \Exception('Error moving uploaded file: ' . $newFilePath);
                    }
                    $filesystem->copy($newFileDestination, $diapoDirectory . '/' . $newFilename);
    
                } catch (\Exception $e) {
                    throw new \Exception('Error processing uploaded file: ' . $e->getMessage());
                }
            } else {
                $extension = pathinfo($newFile, PATHINFO_EXTENSION);
                if (!isset($orderDisplays[$index], $tempDisplays[$index])) {
                    continue;
                }
                $newFilename = $orderDisplays[$index] . '_' . $tempDisplays[$index] . '.' . $extension;
                $newFilePath = rtrim($Olddirectory, '/') . '/' . $newFile;
                $newFileDestination = rtrim($diapoDirectory, '/') . '/' . $newFilename;
            try {
                $newFilePath = $Olddirectory .'/'. $newFile;
                $newFileDestination = $diapoDirectory . '/' . $newFilename;
                if (!rename($newFilePath, $newFileDestination)) {
                    throw new \Exception('Error renaming or moving file: ' . $newFilePath);
                }
                $filesystem->copy($newFileDestination, $diapoDirectory . '/' . $newFilename);
                if (is_dir($Olddirectory) &&  $this->dir_is_empty($Olddirectory)) {
                   $this->deleteDirectory($Olddirectory);
                 }
    
            } catch (\Exception $e) {
                throw new \Exception('Error processing file: ' . $e->getMessage());
            }
        }
        $service->setSrc('images/services/diapo/' . $aliatoire);
    }
}

///////////////////////////  route pour supprimer un service  ////////////////////////////////////////////////////

#[Route('/services/delete/{id_service}/{id}' ,name:'service_delete')]

public function delete_service(int $id,int $id_service)
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    if( !$this->getUser())
   return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getSupprimerService() && $this->getUser()->getSERVICE()&& $configApp->getEnableSERVICE() === '1' ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
    try{
        $service = $this->entityManager->getRepository(Services::class)->findOneBy(['etablissement' => $etablissement, 'id' => $id]);
        if(empty($service))
        {
            throw new Exception();
        }
    $dir_nom = $this->getParameter('project_dir') . '/public/' . $service->getSrc();
    $logo= $this->getParameter('project_dir') . '/public/' . $service->getLogo();
    if($dir_nom)
    {
        if($service->getType() === 'DIAPO' ){
            if (!file_exists($dir_nom)) {
                throw new \Exception('Path does not exist: '.$dir_nom);
            }
    
            if (!is_dir($dir_nom)) {
                throw new \Exception('Path is not a directory: ' . $dir_nom);
            }
            else{
                $this->deleteDirectory($dir_nom);
            }
            }
        else if(file_exists($dir_nom))
            unlink($dir_nom);
    }
    if($logo && file_exists($logo))
        unlink($logo);
}catch (\Exception $e) {
    $this->entityManager->remove($service);
    $this->entityManager->flush();
    return $this->redirectToRoute('services_Categorie',['id' =>$id_service]);
}
$this->entityManager->remove($service);
$this->entityManager->flush();
return $this->redirectToRoute('services_Categorie',['id' =>$id_service]);
    
    }

///////////////////////////  route pour prend les positions de chaque categorie  ////////////////////////////////////////////////////

    #[Route('/services/{id}', name: 'service_categorie', methods: ['GET'])]
    public function getServiceByCategory(int $id, EntityManagerInterface $entityManager): Response
    {
        if( !$this->getUser())
   return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
     if (!$Acce) {
         $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
         return $this->redirectToRoute('home');
     }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED'); 
        $services = $entityManager->getRepository(Services::class)
            ->findBy(['etablissement' => $etablissement, 'categories' => $id]);
    
        if (empty($services)) {
            return new JsonResponse([]);
        }
        $serviceArray = [];
        foreach ($services as $service) {
            $serviceArray[] = $service->getPosition();
        }
        return new JsonResponse($serviceArray);
    }


///////////////////////////  route pour prend liste des services de chaque categorie  ////////////////////////////////////////////////////

 #[Route('/services/getService/{id}', name: 'get_services_Categorie',methods: ['GET'])]
    public function get_service(string $id){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
     if (!$Acce) {
         $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
         return $this->redirectToRoute('home');
     }
        $id = (int) $id; 
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED'); 
        $services  = $this->entityManager->getRepository(Services::class)->findBy(['etablissement' => $etablissement,'categories' => $id],['position' => 'ASC']);
        $serviceArray = [];
        foreach ($services as $service1) {
            $serviceArray[] = ['nom' => $service1->getNom(),
            'logo'=>$service1->getLogo(),
            'position'=>$service1->getPosition(),
            'active'=>$service1->getActive(),
            'Id'=>$service1->getId(),
            'type' => $service1->getType(),
            'src' => $service1->getSrc(),
        ];
            }
    $PositionArray1 = [];
    foreach ($services as $service) {
        $PositionArray1[] = $service->getPosition();
    }
        return new JsonResponse(["services"=>$serviceArray,"positions" =>$PositionArray1]);
    }

    ///////////////////////////  route pour prend les images de diapo pour un service contient source de type Diapo  ////////////////////////////////////////////////////

    #[Route('/services/getImage/{id}', name: 'get_Image_diapo', methods: ['GET'])]
    public function get_images(string $id): JsonResponse
    {    
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
        return new JsonResponse(['error' => 'etablissement not found'], Response::HTTP_NOT_FOUND);
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
     if (!$Acce) {
         $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
         return new JsonResponse(['error' => 'Vous n\'avez pas le droit d\'accéder à cette page.'], Response::HTTP_NOT_FOUND);

     }
        $service = $this->entityManager->getRepository(Services::class)
            ->findOneBy(['etablissement' => $etablissement, 'id' => $id]);
    
        if (!$service) {
            return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }
    
        $dir_nom = $this->getParameter('project_dir') . '/public/' . $service->getSrc();

        if (!file_exists($dir_nom)) {
            return new JsonResponse(['error' => 'Path does not exist: ' . $dir_nom], Response::HTTP_NOT_FOUND);
        }

        if (!is_dir($dir_nom)) {
            return new JsonResponse(['error' => 'Path is not a directory: ' . $dir_nom], Response::HTTP_BAD_REQUEST);
        }
        $files = array_diff(scandir($dir_nom), ['.', '..']);
        $images = array_map(fn($file) =>  $file, $files);
        return new JsonResponse($images);
    }

    
    ///////////////////////////  route pour supprimer une image du dossier existe.  ////////////////////////////////////////////////////

    #[Route('/services/deleteImage/{id}/{index}', name: 'deleteImage')]
    public function delete_image(string $id, int $index): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
            return new JsonResponse(['error' => 'etablissement not found'], Response::HTTP_NOT_FOUND);
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
     if (!$Acce) {
         $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
         return new JsonResponse(['error' => 'Vous n\'avez pas le droit d\'accéder à cette page.'], Response::HTTP_NOT_FOUND);
     }
        $service = $this->entityManager->getRepository(Services::class)
            ->findOneBy(['etablissement' => $etablissement, 'id' => $id]);
    
        if (!$service) {
            return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }
    
        $directory = $this->getParameter('project_dir') . '/public/' . $service->getSrc();
    
        if (!is_dir($directory)) {
            return new JsonResponse(['error' => 'Invalid directory: ' . $directory], Response::HTTP_BAD_REQUEST);
        }
    
        $files = array_values(array_diff(scandir($directory), ['.', '..']));
        usort($files, function ($a, $b) {
            $numA = (int) explode('_', $a)[0];
            $numB = (int) explode('_', $b)[0];
            return $numA <=> $numB;
        });
    
        if (!isset($files[$index])) {
            return new JsonResponse(['error' => 'Image index not found', 'images' => $files[$index]], Response::HTTP_BAD_REQUEST);
        }
        else{
            $imageToDelete = $directory . '/' . $files[$index];
            if (file_exists($imageToDelete)) {
                unlink($imageToDelete);
            } else {
                return new JsonResponse(['error' => 'Image not found: ' . $files[$index]], Response::HTTP_NOT_FOUND);
            }
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Image deleted and remaining files renamed successfully',
            'deletedImage' => $files[$index],
            'files' => $files,
        ]);
    }
    
    #[Route('/services/renameFiles/{id}', name: 'renameFiles')]
    public function renameFiles( int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
        return new JsonResponse(['error' => 'Etablissement not found'], Response::HTTP_NOT_FOUND);
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
     if (!$Acce) {
         $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
         return new JsonResponse(['error' => 'Vous n\'avez pas le droit d\'accéder à cette page.'], Response::HTTP_NOT_FOUND);
        }
        $service = $this->entityManager->getRepository(Services::class)
            ->findOneBy(['etablissement' => $etablissement, 'id' => $id]);
    
        if (!$service) {
            return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }
    
        $directory = $this->getParameter('project_dir') . '/public/' . $service->getSrc();
    
        if (!is_dir($directory)) {
            return new JsonResponse(['error' => 'Invalid directory: ' . $directory], Response::HTTP_BAD_REQUEST);
        }
    
        $files = array_values(array_diff(scandir($directory), ['.', '..']));
        usort($files, function ($a, $b) {
            $numA = (int) explode('_', $a)[0];
            $numB = (int) explode('_', $b)[0];
            return $numA <=> $numB;
        });
        $counter = 1;
        foreach ($files as $file) {
            $oldFilePath = $directory . '/' . $file;
            $str = explode('_', $file);
            $str1 = explode('.', $str[1]);
            $newFileName = $counter . '_'.$str1[0] .'.'. $str1[1];
            $newFilePath = $directory . '/' . $newFileName;
    
            if (is_file($oldFilePath)) {
                rename($oldFilePath, $newFilePath);
            }
            $counter++;
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Image deleted and remaining files renamed successfully',
            'files' => $files,
        ]);
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
    #[Route('/services/lancerService/{idService}', name: 'lancerService')]
    public function lancerService(int $idService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getSERVICE() && $this->getUser()->getLancerArretService() && $configApp->getEnableSERVICE() === '1';
     if (!$Acce) {
         $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
         return $this->redirectToRoute('home');
     }
        $repository = $this->entityManager->getRepository(Chambre::class);
        $queues = array();
        $service = $this->entityManager->getRepository(Services::class)
            ->findOneBy(['etablissement' => $etablissement, 'id' => $idService]);
        if (!$service) {
            return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }
        $request = Request::createFromGlobals();
        $chambre = $request->get("chambre");
        $check = $request->get("checked");
        $chembreNonVide = [];
        foreach($check as $key1 => $k1)
        {
            $lancerService = $this->entityManager->getRepository(LancerAnnonce::class)->findOneBy(['idChembre' => $k1]);
            $lancerService1 = $this->entityManager->getRepository(LancerTV::class)->findOneBy(['idChembre' => $k1]);
            $lancerService2 = $this->entityManager->getRepository(LancerRadio::class)->findOneBy(['idChembre' => $k1]);
            if($lancerService)
                $chembreNonVide[$key1] = $lancerService;
            if($lancerService1)
                $chembreNonVide[$key1] = $lancerService1;
            if($lancerService2)
                $chembreNonVide[$key1] = $lancerService2;
        }
        if(!$chembreNonVide )
        {
        if (isset($chambre) and !empty($chambre) and isset($check) and !empty($check)) {
            foreach ($chambre as $key => $k) {
                foreach($check as $key1 => $k1)
                {
                    if($k == $k1)
                    {
                    $lancer = $this->entityManager->getRepository(Lancerservice::class)->findOneBy(['idChembre' => $k]);
                    if($lancer)
                    {
                        $this->entityManager->remove($lancer);
                        $this->entityManager->flush();
                    }
                    $lancer = new LancerService();
                    $lancer->setIdChembre($k);
                    $lancer->setIdService($idService);
                    $this->entityManager->persist($lancer);
                    $this->entityManager->flush();
                    $boxs  = $repository->findById($k);
                    $chambre= $boxs[0]->getNom();
                    $queue = $etablissement->getId() . '.' . $chambre . '.service';
                    array_push($queues,$queue);
                    $arrayService = [];
                    $arrayService[] = $this->entityToArray($service);
                    $ServiceJson = json_encode($arrayService);  
                    $message = "lancer_service%%".$ServiceJson."%%".$k;
;                   $Manager = new PushRabbit();
                    $Manager->MakeRabbitCall($queues, $message); 
            } 
        }
    }
}
        return $this->redirectToRoute('services_Categorie',['id' =>$service->getCategories()->getId()]);
}
else{
        $chembreIds = array_map(function($service) {
            $name_chambre = $this->entityManager->getRepository(Chambre::class)->findOneBy(['id'=>$service->getIdChembre()])->getNom();
            return $name_chambre ;
        }, $chembreNonVide);
        $this->addFlash('warning','Les chambres suivantes ne sont pas vides, elles ont d\'autres annonces à lancer. S\'il vous plaît, arrêtez les annonces en cours sur les chembres suivant : ' . implode(', ', $chembreIds));
        return $this->redirectToRoute('services_Categorie',['id' =>$service->getCategories()->getId()]);
    }
}


#[Route('/services/RemoveService/{idService}',name:'RemoveService')]
public function RemoveService(int $idService)
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    if( !$this->getUser())
     return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
    $Acce = $this->getUser()->getSERVICE() && $this->getUser()->getLancerArretService() && $configApp->getEnableSERVICE() === '1';
 if (!$Acce) {
     $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
     return $this->redirectToRoute('home');
 }
    $repository = $this->entityManager->getRepository(Chambre::class);
    $queues = array();
    $service = $this->entityManager->getRepository(Services::class)
        ->findOneBy(['etablissement' => $etablissement, 'id' => $idService]);
    if (!$service) {
        return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
    }
    $request = Request::createFromGlobals();
    $chambre = $request->get("chambre");
    $check = $request->get("checked");
    if (isset($chambre) and !empty($chambre) and isset($check) and !empty($check)) {
        foreach ($chambre as $key => $k) {
            foreach($check as $key1 => $k1)
            {
                if($k == $k1)
                {
                $lancer = $this->entityManager->getRepository(LancerService::class)->findOneBy(['idService'=>$idService,'idChembre' => $k]);
                if($lancer)
                {
                $this->entityManager->remove($lancer);
                $this->entityManager->flush();
                $boxs  = $repository->findById($k);
                $chambre= $boxs[0]->getNom();
                $queue = $etablissement->getId() . '.' . $chambre . '.service';
                array_push($queues,$queue);  
                $message = "arreter_service%%".$idService."%%".$k;
;               $Manager = new PushRabbit();
                $Manager->MakeRabbitCall($queues, $message);
                }

            }
    }
}
}
    return $this->redirectToRoute('services_Categorie',['id' =>$service->getCategories()->getId()]);
}

#[Route('/service/GetAllchambreLancerService/{idService}',name:'GetAllchambreLancerService',methods:'GET')]

public function GetAllchambreLancerService(int $idService){
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
    if( !$this->getUser())
        return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
    $Acce = $this->getUser()->getSERVICE() && $configApp->getEnableSERVICE() === '1';
 if (!$Acce) {
     $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
     return $this->redirectToRoute('home');
 }
    $service = $this->entityManager->getRepository(Services::class)
        ->findOneBy(['etablissement' => $etablissement, 'id' => $idService]);
    if (!$service) {
        return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
    }
    $lancer = $this->entityManager->getRepository(LancerService::class)->findBy(['idService'=>$idService]);
    $arrayLancerService = [];
    foreach ($lancer as $l) {
        $ex = $this->entityManager->getRepository(Chambre::class)->findOneBy(["id"=>$l->getIdChembre()]);
        $arrayLancerService[] = ['id'=>$ex->getId(),'nom' => $ex->getNom(),
                        'ip' => $ex->getIp(),
                        'Mac' => $ex->getMac(),];
        }
        $LancerServiceJson = json_encode($arrayLancerService);

    return new JsonResponse(['ServiceLancer' => $arrayLancerService]);
}

}