<?php

namespace App\Controller;

use App\Entity\CategorieLivreaudio;
use App\Entity\CategorieRadio;
use App\Entity\Categories;
use App\Entity\CategorieVod;
use App\Entity\Chambre;
use App\Entity\ConfigApp;
use App\Entity\Etablissement;
use App\Entity\Livreaudio;
use App\Entity\Questionnaire;
use App\Entity\Radio;
use App\Entity\ServiceEtablissement;
use App\Entity\Services;
use App\Entity\Television;
use App\Entity\User;
use App\Entity\Vod;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use ZipArchive;


class ExportImportController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/app_Confirme_export/{id}', name: 'app_Confirme_export', methods: ['POST'])]
    public function app_Confirme_export(string $id, Request $request, UserPasswordHasherInterface $passwordHasher,    ParameterBagInterface $params,   TokenGeneratorInterface $tokenGenerator
    ): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        $user1 = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user1) {
            return new JsonResponse(['passwordValid' => false], Response::HTTP_OK);
        }
    
        $content = $request->getContent();
        error_log('Request Content: ' . $content);
        $data = json_decode($content, true);
    
        if (!isset($data['password'])) {
            return new JsonResponse(['error' => 'Password is required.'], Response::HTTP_BAD_REQUEST);
        }
        $password = $data['password'];
            $lastDate = $user1->getDerniertempExport();
            if ($lastDate) {
                // $lastDate->modify('+2 minutes');
                $lastDate->modify('+1 hours');
            }
            $dateNow = new DateTime();
            if ($user1->getTentativeExport() < 3) {
                $user1->setTentativeExport($user1->getTentativeExport() + 1);
                $user1->setDerniertempExport($dateNow);
                $this->entityManager->flush();
                if ($password && $passwordHasher->isPasswordValid($user1, $password)) {
                    $user1->setTentativeExport(0);
                    $this->entityManager->persist($user1);
                    $this->entityManager->flush();
                    return new JsonResponse(['passwordValid' => true], Response::HTTP_OK);
                }
                return new JsonResponse([
                    'passwordValid' => false,
                    'temp' => $user1->getDerniertempExport(),
                    'tentative' => $user1->getTentativeExport()
                ], Response::HTTP_OK);
            }
            else{
                if($dateNow >= $lastDate)
                {
                    $user1->setTentativeExport(0);
                    $dateNow = new DateTime();
                    $user1->getDerniertempExport($dateNow);
                    $this->entityManager->flush();
                    if($dateNow < $lastDate)
                    {        
                        return new JsonResponse([
                            'passwordValid' => false,
                            'temp' => $user1->getDerniertempExport(),
                            'tentative' => $user1->getTentativeExport(),
                        ], Response::HTTP_OK);     
                    }
                }
            
                $token = $tokenGenerator->generateToken();
                $user1->setResetToken($token);
                $this->entityManager->flush();    
                $Admin = $this->entityManager->getRepository(User::class)->findOneBy(['email' =>$user1->getEmailAdmin()]);
                // Création et envoi de l'e-mail à l'administrateur pour l'informer que le mot de passe a été changé.
                // On utilise le Protocole SMTP: protocole de communication utilisé pour transférer le courrier électronique (courriel) vers les serveurs de messagerie électronique.
                // j'ai install (composer require phpmailer/phpmailer)
                // Les parametres de Protocole SMTP 
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                // le serveur de SMTP                                 
                    $mail->Host = $params->get('smtp_host');                           
                    $mail->SMTPAuth = true;     
                //L'email                              
                    $mail->Username = $params->get('smtp_username');
                //le mot de passe d'email                 
                    $mail->Password = $params->get('smtp_password');                   
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        
                    $mail->Port = $params->get('smtp_port');                                        
            
                    //les informations d'Expéditeur
                    $mail->setFrom($params->get('smtp_username'), 'AlterIpso');
                
                    //les informations de Destinataires
                    //Addresse de super admin
                    $mail->addAddress($params->get('smtp_username'));
                    $mail->addAddress($user1->getEmailAdmin());
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Un utilisateur souhaite effectuer une exportation des données.';
                    $bodyContent = $this->renderView('email/confirmPassword.html.twig', [
                                'user' => $user1,
                                'admin' => $Admin,
                    ]);
                    $mail->isHTML(true); 
                    // Corps du message
                    $mail->Body= $bodyContent;
            
                    // Envoi de l'email
                    $mail->send();
            return new JsonResponse(['passwordValid' => false,                  
            'message' => 'Vous avez épuisé le nombre de tentatives autorisées. Essayez de nouveau dans 1h.'
        ], Response::HTTP_OK);
        }
    }
    
    

    private function addFolderToZip(string $folder, ZipArchive $zip, string $zipPath = ''): void
    {
    if (!is_dir($folder)) {
        return;
    }
    $files = scandir($folder);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $filePath = $folder . DIRECTORY_SEPARATOR . $file;
        if ($zipPath === '') {
            $zipPath = basename($folder);
        }
        $relativePath = $zipPath . '/' . $file;
        if (is_dir($filePath)) {
            $zip->addEmptyDir($relativePath);
            $this->addFolderToZip($filePath, $zip, $relativePath);
        } else {
            $zip->addFile($filePath, $relativePath);
        }
    }
}

private function copyDirectory($source, $destinationBase) {
    if (!is_dir($source)) {
        return false;
    }

    if (!file_exists($destinationBase)) {
        mkdir($destinationBase, 0777, true);
    }
    $files = scandir($source);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $srcPath = $source . DIRECTORY_SEPARATOR . $file;
        $relativePath = str_replace("images/services/diapo", "", $source);
        $destPath = $destinationBase . $relativePath . DIRECTORY_SEPARATOR . $file;
        if (is_dir($srcPath)) {
            $this->copyDirectory($srcPath, $destinationBase);
        } else {
            if (!file_exists(dirname($destPath))) {
                mkdir(dirname($destPath), 0777, true);
            }
            copy($srcPath, $destPath);
        }
    }
    return true;
}

    private function exportData(object $nameEntity, object $metaData, \SimpleXMLElement $rowNode, EntityManagerInterface $entityManager, ZipArchive $zip)
    {
        foreach ($metaData->getFieldNames() as $field) {
            $getter = 'get' . ucfirst($field);
            $boolGetter = 'is' . ucfirst($field);
            $value = null;
            if (method_exists($nameEntity, $getter)) {
                $value = $nameEntity->$getter();
            } elseif (method_exists($nameEntity, $boolGetter)) {
                $value = $nameEntity->$boolGetter();
            }
            
            if ($value === null || $value === false) {
                $value = "0";
            } elseif ($value instanceof \DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (is_array($value)) {
                $value = implode(', ', $value);
            }
            
            if ($field === 'logo' || $field === 'background' || $field === "src") {
                if ($value && file_exists($value)) {
                    $directoryPath = dirname($value);
                    $newFolder = "folderZip/" . $directoryPath;
                
                    if (!file_exists($newFolder)) {
                        mkdir($newFolder, 0777, true);
                    }
                
                    if (is_dir($value)) {
                        $this->copyDirectory($value, $newFolder);
                    } else {
                        $destinationPath = $newFolder . '/' . basename($value);
                        if (!file_exists($destinationPath)) {
                            copy($value, $destinationPath);
                        }
                    }
                
                    $this->addFolderToZip($newFolder, $zip);
                }
                
            }
    
            $rowNode->addChild($field, htmlspecialchars((string)$value, ENT_XML1, 'UTF-8'));
        }
    
        foreach ($metaData->getAssociationNames() as $field) {
            $getter = 'get' . ucfirst($field);
            if (method_exists($nameEntity, $getter)) {
                $value = $nameEntity->$getter();
                if ($value instanceof \Doctrine\Common\Collections\Collection) {
                    $childNode = $rowNode->addChild($field);
                    foreach ($value as $item) {
                        $itemNode = $childNode->addChild(strtolower((new \ReflectionClass($item))->getShortName()));
                        $metadata = $entityManager->getClassMetadata(get_class($item));
                        $this->exportData($item, $metadata, $itemNode, $entityManager, $zip);
                    }
                }
                elseif (is_object($value)) {
                    $getterId = 'getId';
                    if (method_exists($value, $getterId)) {
                        $rowNode->addChild($field, (string) $value->$getterId());
                    }
                }
            }
        }
    }
    
    #[Route('/export', name: 'app_export')]
    public function export(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $etablissement = $user->getEtablissement();
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><database></database>');
    
        $newEtablissementNode = $xml->addChild('etablissement');
        $newAppConfigNode = $xml->addChild('AppConfig');
    
        $rowNode = $newEtablissementNode->addChild('row');
        $rowNode1 = $newAppConfigNode->addChild('row');
    
        $metaData = $entityManager->getClassMetadata(Etablissement::class);
        $metaDataConfigApp = $entityManager->getClassMetadata(ConfigApp::class);
        $zip = new ZipArchive();
        $zipFilePath = $this->getParameter('kernel.project_dir') . '/var/exportData.zip';
    
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return new Response('Erreur lors de la création du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        $this->exportData($etablissement, $metaData, $rowNode, $entityManager, $zip);
        $this->exportData($appConfig, $metaDataConfigApp, $rowNode1, $entityManager, $zip);
        // dd("test");
        $zip->addFromString('database_export.xml', $xml->asXML());
        $zip->close();
        return new BinaryFileResponse($zipFilePath, Response::HTTP_OK, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="exportData.zip"',
        ]);
    }

    

#[Route('/import', name: 'app_import')]
public function import(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response
{
    $uploadedFile = $request->files->get('fileData');

    if (!$uploadedFile instanceof UploadedFile) {
        return new Response('Aucun fichier fourni', Response::HTTP_BAD_REQUEST);
    }

    $filesystem = new Filesystem();
    $uploadDir = $this->getParameter('kernel.project_dir') . '/var/uploads/';
    $destinationDir = $this->getParameter('kernel.project_dir') . '/public/images/';
    $extractDir = $uploadDir . 'extracted/';

    $filesystem->mkdir([$uploadDir, $extractDir, $destinationDir], 0777);

    $zipFilePath = $uploadDir . 'exportData.zip';
    $uploadedFile->move($uploadDir, 'exportData.zip');

    if (!$this->extractZip($zipFilePath, $extractDir)) {
        return new Response('Erreur lors de l’extraction du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    $this->mergeFolders($extractDir, $destinationDir);

    $xmlFilePath = $extractDir . 'database_export.xml';
    
    if (!file_exists($xmlFilePath)) {
        return new Response('Fichier XML non trouvé', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    $xml = simplexml_load_file($xmlFilePath);
    try{
        $this->importEtablissement($xml, $tokenGenerator, $entityManager);
    }catch(\Exception $e){
        $this->addFlash('danger', 'L’établissement que vous tentez d’importer existe déjà dans la base de données. Veuillez vérifier les informations ou utiliser un autre identifiant.');
        return $this->redirectToRoute('home');
    }
    $filesystem->remove($zipFilePath);
    $filesystem->remove($extractDir);
    $this->addFlash('changerPassword', 'Les données ont été importées avec succès.');
    return $this->redirectToRoute('home');
}

private function mergeFolders(string $source, string $destination): void
{
    $filesystem = new Filesystem();
    $finder = new Finder();
    $finder->files()->in($source);

    foreach ($finder as $file) {
        $relativePath = $file->getRelativePathname();
        $targetPath = $destination . DIRECTORY_SEPARATOR . $relativePath;
        if (!$filesystem->exists($targetPath)) {
            $filesystem->copy($file->getRealPath(), $targetPath);
        }
    }
}

private function extractZip(string $zipFilePath, string $extractPath): bool
{
    if (!is_dir($extractPath)) {
        mkdir($extractPath, 0777, true);
    }

    $zip = new ZipArchive();
    if ($zip->open($zipFilePath) !== true) {
        return false;
    }

    $zip->extractTo($extractPath);
    $zip->close();

    return true;
}
private function importEtablissement(\SimpleXMLElement $xml,TokenGeneratorInterface $tokenGenerator,EntityManagerInterface $entityManager): void
{
    foreach ($xml->etablissement->row as $row) {
        $etablissement = new Etablissement();
        foreach ($row->children() as $field => $value) {
            $this->setProperty($etablissement,$tokenGenerator, $field, (string) $value,$etablissement);
        }
        $entityManager->persist($etablissement);        
        foreach ($row->children() as $field => $childNode) {
            if ($childNode->count() > 0) {
                $this->importRelation($childNode,$tokenGenerator, $etablissement, $field, $entityManager);
            }
        }
    }
    foreach ($xml->AppConfig->row as $row) {
        $AppConfig = new ConfigApp();
        foreach ($row->children() as $field => $value) {
            if(!$value)
                $value = "";
            $this->setProperty($AppConfig,$tokenGenerator, $field, (string) $value,$etablissement);
        }
        $AppConfig->setEtablissement($etablissement);
        $entityManager->persist($AppConfig);
    }
    $entityManager->flush();
}

private function importRelation(\SimpleXMLElement $node,TokenGeneratorInterface $tokenGenerator,Etablissement $etablissement, string $relationName, EntityManagerInterface $entityManager): void
{
    foreach ($node->children() as $itemNode) {
        $relationClass = 'App\\Entity\\' . ucfirst($itemNode->getName());
        if (!class_exists($relationClass)) {
            continue;
        }
        $relatedEntity = new $relationClass();
        foreach ($itemNode->children() as $field => $value) {
            $this->setProperty($relatedEntity, $tokenGenerator,$field, (string) $value,$etablissement);
        }
        $entityManager->persist($relatedEntity);
        $entityManager->flush();
    }
}

private function setProperty(object $entity,TokenGeneratorInterface $tokenGenerator, string $field, mixed $value,object $etablissement): void
{
    $setter = 'set' . ucfirst($field);
    $firstTwo = substr($etablissement->getId(), 0, 2);
    if (!method_exists($entity, $setter)) {
        return;
    }

    if($field === "id" && ($entity instanceof CategorieRadio || $entity instanceof CategorieLivreAudio || $entity instanceof CategorieVod ||  $entity instanceof ServiceEtablissement || $entity instanceof Categories) )
      {
          $value = (int)($firstTwo.$value);
          $exEntity = $this->entityManager->getRepository($entity::class)->findOneBy(['etablissement'=>$etablissement,'id'=>$value]);
          if($exEntity)
          {
              return;
          }
      }  
    if($field === 'etablissement')
    {
        $value = $etablissement;
    }
    if($field === 'categorie' && $entity instanceof Radio)
    {
        $value = (int)($firstTwo.$value);
        $repository = $this->entityManager->getRepository(CategorieRadio::class)->findOneBy(['etablissement'=>$etablissement,'id'=>$value]);
        $value = $repository;
        // $radioRepository = $this->entityManager->getRepository(Radio::class)->findOneBy(['etablissement'=>$etablissement,'categorie'=>$value]);
        // if ($radioRepository) {
        //     throw new \Exception("Une radio avec cette catégorie existe déjà.");
        // }
    }
    if($field === 'categorie' && $entity instanceof Livreaudio)
    {
        $value = (int)($firstTwo.$value);
        $repository = $this->entityManager->getRepository(CategorieLivreaudio::class)->findOneBy(['etablissement'=>$etablissement,'id'=>$value]);
        $value = $repository;
    }
    if($field === 'categorie' && $entity instanceof Vod)
    {
        $value = (int)($firstTwo.$value);
        $repository = $this->entityManager->getRepository(CategorieVod::class)->findOneBy(['etablissement'=>$etablissement,'id'=>$value]);
        $value = $repository;
    }
    if($field === 'categories' && $entity instanceof Services)
    {
        $value = (int)($firstTwo.$value);
        $repository = $this->entityManager->getRepository(Categories::class)->findOneBy(['etablissement'=>$etablissement,'id'=>$value]);
        $value = $repository;
    }
    if($field === "service" && $entity instanceof Questionnaire)
    {
        $value = (int)($firstTwo.$value);
        $repository = $this->entityManager->getRepository(ServiceEtablissement::class)->findOneBy(['etablissement'=>$etablissement,'id'=>$value]);
        $value = $repository;
    }
    if($field === "service" && $entity instanceof Chambre)
    {
        $value = (int)($firstTwo.$value);
        $repository = $this->entityManager->getRepository(ServiceEtablissement::class)->findOneBy(['etablissement'=>$etablissement,'id'=>$value]);
        $value = $repository;
    }
    if ($field === 'roles') {
        $value = is_array($value) ? $value : json_decode($value, true) ?? [$value];
    }
    if($field === 'resetToken')
    {
        $value = $tokenGenerator->generateToken();
    }
    if ($value === true) {
        $value = true;
    } elseif ($value === false) {
        $value = false;
    } elseif ($value === null || $value === '') {
        return;
    }
    if ($field === 'dernierTemp' || $field === 'derniertempExport') {
        try {
            $value = new \DateTime($value);
        } catch (\Exception $e) {
            $value = new \DateTime('2025-02-24 12:30:00');
        }
    }
    $entity->$setter($value);
}
   

// ////////////////////////////////////////////// route pour exporter television et radio ////////////////////////////////

#[Route('/export_tv', name: 'app_export_tv')]
public function exportDataCh(EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }
    $etablissement = $user->getEtablissement();
    $televisions = $entityManager->getRepository(Television::class)->findBy(['etablissement' => $etablissement]);
    $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><database></database>');
    $newTele = $xml->addChild('television');

    foreach($televisions as $television)
    {
        $rowNode = $newTele->addChild('row');
        $metaDataTele = $entityManager->getClassMetadata(Television::class);
        $zip = new ZipArchive();
        $zipFilePath = $this->getParameter('kernel.project_dir') . '/var/exportDataTv.zip';
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return new Response('Erreur lors de la création du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $this->exportData($television, $metaDataTele, $rowNode, $entityManager, $zip);
    }
    $zip->addFromString('database_export.xml', $xml->asXML());
    $zip->close();
    return new BinaryFileResponse($zipFilePath, Response::HTTP_OK, [
        'Content-Type' => 'application/zip',
        'Content-Disposition' => 'attachment; filename="exportDataTv.zip"',
    ]);
}



#[Route('/export_Radio', name: 'app_export_Radio')]
public function exportDataRadio(EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }
    $etablissement = $user->getEtablissement();
    $CategorieRadio = $entityManager->getRepository(CategorieRadio::class)->findBy(['etablissement' => $etablissement]);
    $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><database></database>');
    $newcategorie = $xml->addChild('CategorieRadio');
    foreach($CategorieRadio as $categorie)
    {
        $rowNode = $newcategorie->addChild('row');
        $metaDatacategorie = $entityManager->getClassMetadata(CategorieRadio::class);
        $zip = new ZipArchive();
        $zipFilePath = $this->getParameter('kernel.project_dir') . '/var/exportDataTv.zip';
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return new Response('Erreur lors de la création du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $this->exportData($categorie, $metaDatacategorie, $rowNode, $entityManager, $zip);
    }
    $zip->addFromString('database_export.xml', $xml->asXML());
    $zip->close();
    return new BinaryFileResponse($zipFilePath, Response::HTTP_OK, [
        'Content-Type' => 'application/zip',
        'Content-Disposition' => 'attachment; filename="exportDataRadio.zip"',
    ]);
}

private function importchamp(int $id,Bool $object ,\SimpleXMLElement $xml,TokenGeneratorInterface $tokenGenerator,EntityManagerInterface $entityManager): void
{
    if($object)
    {
        foreach ($xml->television->row as $row) {
            $television = new Television();
            $etablissement = $entityManager->getRepository(Etablissement::class)->findOneBy(['id'=>$id]);
            foreach ($row->children() as $field => $value) {
                $this->setProperty($television,$tokenGenerator, $field, (string) $value,$etablissement);
            }
            $entityManager->persist($television);        
            foreach ($row->children() as $field => $childNode) {
                if ($childNode->count() > 0) {
                    $this->importRelation($childNode,$tokenGenerator, $etablissement, $field, $entityManager);
                }
            }
        }
    }
    else{
        foreach ($xml->CategorieRadio->row as $row) {
            $CategorieRadio = new CategorieRadio();
            $etablissement = $entityManager->getRepository(Etablissement::class)->findOneBy(['id'=>$id]);
            foreach ($row->children() as $field => $value) {
                $this->setProperty($CategorieRadio,$tokenGenerator, $field, (string) $value,$etablissement);
            }
            if($CategorieRadio->getId())
            {
                $entityManager->persist($CategorieRadio);        
                $entityManager->flush();
            }
            foreach ($row->children() as $field => $childNode) {
                if ($childNode->count() > 0) {
                    $this->importRelation($childNode,$tokenGenerator, $etablissement, $field, $entityManager);
                }
            }
        }
    }
    $entityManager->flush();
}

#[Route('/importTv/{id}', name: 'app_import_tv')]
public function importDataTv(int $id,Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response
{
    $uploadedFile = $request->files->get('fileData');

    if (!$uploadedFile instanceof UploadedFile) {
        return new Response('Aucun fichier fourni', Response::HTTP_BAD_REQUEST);
    }
    $filesystem = new Filesystem();
    $uploadDir = $this->getParameter('kernel.project_dir') . '/var/uploads/';
    $destinationDir = $this->getParameter('kernel.project_dir') . '/public/images/';
    $extractDir = $uploadDir . 'extracted/';

    $filesystem->mkdir([$uploadDir, $extractDir, $destinationDir], 0777);

    $zipFilePath = $uploadDir . 'exportData.zip';
    $uploadedFile->move($uploadDir, 'exportData.zip');

    if (!$this->extractZip($zipFilePath, $extractDir)) {
        return new Response('Erreur lors de l’extraction du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    $this->mergeFolders($extractDir, $destinationDir);
    $xmlFilePath = $extractDir . 'database_export.xml';
    if (!file_exists($xmlFilePath)) {
        return new Response('Fichier XML non trouvé', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    $xml = simplexml_load_file($xmlFilePath);
    try{
        $this->importchamp($id,true, $xml, $tokenGenerator, $entityManager);
    }catch(\Exception $e){
        $this->addFlash('danger', 'Le fichier TV que vous tentez d’importer contient des données déjà existantes. Veuillez vérifier les informations avant de continuer.');
        return $this->redirectToRoute('app_television');
    }
    $filesystem->remove($zipFilePath);
    $filesystem->remove($extractDir);
    $this->addFlash('changerPassword', 'Les données ont été importées avec succès.');
    return $this->redirectToRoute('app_television');
}


#[Route('/importRadio/{id}', name: 'app_import_Radio')]
public function importDataRadio(int $id,Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response
{
    $uploadedFile = $request->files->get('fileData');
    if (!$uploadedFile instanceof UploadedFile) {
        return new Response('Aucun fichier fourni', Response::HTTP_BAD_REQUEST);
    }
    $filesystem = new Filesystem();
    $uploadDir = $this->getParameter('kernel.project_dir') . '/var/uploads/';
    $destinationDir = $this->getParameter('kernel.project_dir') . '/public/images/';
    $extractDir = $uploadDir . 'extracted/';

    $filesystem->mkdir([$uploadDir, $extractDir, $destinationDir], 0777);

    $zipFilePath = $uploadDir . 'exportData.zip';
    $uploadedFile->move($uploadDir, 'exportData.zip');

    if (!$this->extractZip($zipFilePath, $extractDir)) {
        return new Response('Erreur lors de l’extraction du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    $this->mergeFolders($extractDir, $destinationDir);
    $xmlFilePath = $extractDir . 'database_export.xml';
    if (!file_exists($xmlFilePath)) {
        return new Response('Fichier XML non trouvé', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    $xml = simplexml_load_file($xmlFilePath);
    try{
        $this->importchamp($id,false, $xml, $tokenGenerator, $entityManager);
    }catch(\Exception $e){
        $this->addFlash('danger', 'La station radio que vous tentez d’importer existe déjà dans la base de données. Veuillez utiliser un autre identifiant ou vérifier les informations.');
        return $this->redirectToRoute('app_radio');
    }
    $filesystem->remove($zipFilePath);
    $filesystem->remove($extractDir);
    $this->addFlash('changerPassword', 'Les données ont été importées avec succès.');
    return $this->redirectToRoute('app_radio');
}

// fonction pour reinitialser les tentative de mot de passe

#
    #[Route('/app_Reinisialiser_password/{id}', name:'app_Reinisialiser_password')]

    public function Reinisialiser_password(int $id,EntityManagerInterface $entityManager)
    {
        $user1 = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user1) {
            return new JsonResponse(['Resit' => false], Response::HTTP_OK);
        }
        $lastDate = $user1->getDerniertempExport();
        if ($lastDate) {
            $lastDate->modify('+5 minutes');
            // $lastDate->modify('+1 hours');
        }
        $dateNow = new DateTime();
        
        if($user1->getTentativeExport() >= 3 && $dateNow >= $lastDate)
        {
            $user1->setTentativeExport(0);
            $dateNow = new DateTime();
            $user1->getDerniertempExport($dateNow);
            $this->entityManager->flush();
            if($dateNow < $lastDate)
            {        
                return new JsonResponse([
                    'Resit' => true,
                    'temp' => $user1->getDerniertempExport(),
                    'tentative' => $user1->getTentativeExport(),
                ], Response::HTTP_OK);     
            }
        }
        else{
            return new JsonResponse(['Resit' => false,                  
        ], Response::HTTP_OK);
        }
    }

}
