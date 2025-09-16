<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Application;
use App\Entity\CategorieLivreaudio;
use App\Entity\CategorieRadio;
use App\Entity\Categories;
use App\Entity\CategorieVod;
use App\Entity\Chambre;
use App\Entity\ConfigApp;
use App\Entity\Configmobile;
use App\Entity\Etablissement;
use App\Entity\HistoriqueAnnonce;
use App\Entity\Historiquegratuite;
use App\Entity\Jeux;
use App\Entity\LancerAnnonce;
use App\Entity\LancerRadio;
use App\Entity\Lancerservice;
use App\Entity\LancerTV;
use App\Entity\Livreaudio;
use App\Entity\Questionnaire;
use App\Entity\Radio;
use App\Entity\ResultatQuestionnaire;
use App\Entity\ServiceEnChambre;
use App\Entity\ServiceEtablissement;
use App\Entity\Services;
use App\Entity\Support;
use App\Entity\Television;
use App\Entity\TypeServiceEnChambre;
use App\Entity\User;
use App\Entity\Vod;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PDO;
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
use Doctrine\DBAL\Connection;



class ExportImportController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

// cette fonction pour confirmer le mot de passe est correct 
    #[Route('/app_Confirme_export/{id}', name: 'app_Confirme_export', methods: ['POST'])]
    public function app_Confirme_export(string $id, Request $request, UserPasswordHasherInterface $passwordHasher, ParameterBagInterface $params,   TokenGeneratorInterface $tokenGenerator
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
            // Obtenir le dernier temps de la dernière tentative, puis ajouter 1 heure pour vérifier si une heure s'est écoulée après trois tentatives.
            if ($lastDate) {
                // $lastDate->modify('+2 minutes');
                $lastDate->modify('+1 hours');
            }
            $dateNow = new DateTime();
            // Vérifier que le nombre de tentatives ne dépasse pas 3.
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
    
    // Fonction pour ajouter les fichiers et les dossiers contenant les images ainsi que le contenu de chaque page.

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

        // Créer le chemin relatif à partir de la racine "images/services/diapo"
        $relativePath = str_replace(realpath("images/services/diapo"), "", realpath($source));
         $lastSlashPos1 = str_replace(['\\', '//'], '/', $relativePath);
        $lastSlashPos = strrpos($lastSlashPos1, "/");
        $extracted = substr($lastSlashPos1, $lastSlashPos);
        $destDir = $destinationBase . $extracted;
        $destDir = str_replace(['\\', '//'], '/', $destDir);
        $destPath = $destDir . '/' . $file;
        
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


public function exportToSql(EntityManagerInterface $entityManager, Etablissement $etablissement): string
{
    $tables = [
        Television::class,
        Chambre::class,
        ServiceEnChambre::class,
        ResultatQuestionnaire::class,
        Questionnaire::class,
        LivreAudio::class,
        Vod::class,
        Radio::class,
        Services::class,
        TypeServiceEnChambre::class,
        Categories::class,
        CategorieLivreAudio::class,
        CategorieVod::class,
        CategorieRadio::class,
        Support::class,
        Annonce::class,
        Application::class,
        Configmobile::class,
        Historiquegratuite::class,
        HistoriqueAnnonce::class,
        Jeux::class,
        ConfigApp::class,
        ServiceEtablissement::class,
        User::class,
    ];

    // Helper function to escape SQL strings manually
    $escapeSqlString = function(string $value): string {
        $search  = ["\\",   "\x00",  "\n",  "\r",  "'",  '"',  "\x1a"];
        $replace = ["\\\\", "\\0", "\\n", "\\r", "\\'", '\\"', "\\Z"];
        return str_replace($search, $replace, $value);
    };

    $sql = "SET FOREIGN_KEY_CHECKS=0;\n";
    $conn = $entityManager->getConnection();

    // Export 'etablissement' table first
    $tableName = 'etablissement';
    $stmt = $conn->prepare("SELECT * FROM `$tableName` WHERE id = :etabId");
    $result = $stmt->executeQuery(['etabId' => $etablissement->getId()]);
    $rows = $result->fetchAllAssociative();

    if (count($rows) > 0) {
        $columns = array_map(fn($col) => "`$col`", array_keys($rows[0]));
        $sql .= "INSERT INTO `$tableName` (" . implode(", ", $columns) . ") VALUES\n";
        $valuesLines = [];
        foreach ($rows as $row) {
            $values = array_map(function ($val) use ($escapeSqlString) {
                if ($val === null) {
                    return 'NULL';
                }
                return "'" . $escapeSqlString($val) . "'";
            }, array_values($row));
            $valuesLines[] = "(" . implode(", ", $values) . ")";
        }
        $sql .= implode(",\n", $valuesLines) . ";\n\n";
    }

    // Export related tables
    foreach ($tables as $entityClass) {
        $meta = $entityManager->getClassMetadata($entityClass);
        $tableName = $meta->getTableName();

        $stmt = $conn->prepare("SELECT * FROM `$tableName` WHERE etablissement_id = :etabId");
        $result = $stmt->executeQuery(['etabId' => $etablissement->getId()]);
        $rows = $result->fetchAllAssociative();

        if (count($rows) > 0) {
            $columns = array_map(fn($col) => "`$col`", array_keys($rows[0]));
            $sql .= "INSERT INTO `$tableName` (" . implode(", ", $columns) . ") VALUES\n";
            $valuesLines = [];
            foreach ($rows as $row) {
                $values = array_map(function ($val) use ($escapeSqlString) {
                    if ($val === null) {
                        return 'NULL';
                    }
                    return "'" . $escapeSqlString($val) . "'";
                }, array_values($row));
                $valuesLines[] = "(" . implode(", ", $values) . ")";
            }
            $sql .= implode(",\n", $valuesLines) . ";\n\n";
        }
    }

    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

    return $sql;
}

// fonction pour ajouter toute les donnees sur un fichier xml et apres je l'ajoute sur un dossier zip
    private function exportDataFiles(object $nameEntity, object $metaData, EntityManagerInterface $entityManager, ZipArchive $zip)
    {
        foreach ($metaData->getFieldNames() as $field) {
            $getter = 'get' . ucfirst($field);
            $boolGetter = 'is' . ucfirst($field);
            $value = null;
            if (method_exists($nameEntity, $getter)) {
                $value = $nameEntity->$getter();
            }
            if ($field === 'logo' || $field === 'background' || $field === "src") {
                $file = $this->getParameter('project_dir') . '/public/' . $value;
                if ($value && file_exists($file)) {
                    $directoryPath = dirname($value);
                    $newFolder = 'folderZip/' . $directoryPath;
                
                    if (!file_exists($newFolder)) {
                        mkdir($newFolder, 0777, true);
                    }
                
                    if (is_dir($file)) {
                        $this->copyDirectory($file, $newFolder);
                    } else {
                        $destinationPath = $newFolder . '/' . basename($value);
                
                        if (!file_exists($destinationPath)) {
                            copy($file, $destinationPath);
                        }
                    }
                    $this->addFolderToZip($newFolder, $zip);
                  

                }
            }
        }
        
        foreach ($metaData->getAssociationNames() as $field) {
            $getter = 'get' . ucfirst($field);
            if (method_exists($nameEntity, $getter)) {
                $value = $nameEntity->$getter();
                if ($value instanceof \Doctrine\Common\Collections\Collection) {
                    foreach ($value as $item) {
                        $metadata = $entityManager->getClassMetadata(get_class($item));
                        $this->exportDataFiles($item, $metadata,$entityManager, $zip);
                    }
                }
            }
        }
    }
    
    // route pour exporter les donnes d'etablissements
    #[Route('/export/{id}', name: 'app_export')]
    public function export(EntityManagerInterface $entityManager,int $id): Response
    {
        $filesystem = new Filesystem();
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $etablissement = $entityManager->getRepository(Etablissement::class)->findOneById(['id' => $id]);
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        // Créer la racine du fichier XML avec la déclaration de version et d'encodage
        $metaData = $entityManager->getClassMetadata(Etablissement::class);
        $metaDataConfigApp = $entityManager->getClassMetadata(ConfigApp::class);
        // Crée un fichier ZIP pour exporter les données sous forme de fichier
            $zip = new ZipArchive();
            $zipFilePath = $this->getParameter('project_dir') . 'exportData.zip';

        // Tente d'ouvrir ou de créer le fichier ZIP en mode écrasement
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        // Retourne une erreur si la création du fichier ZIP échoue
        return new Response('Erreur lors de la création du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Appel de la fonction pour exporter les données de l'établissement
        // dd($metaData);
        $this->exportDataFiles($etablissement, $metaData, $entityManager, $zip);

        // Appel de la fonction pour exporter les données de la configuration de l'application
        $this->exportDataFiles($appConfig, $metaDataConfigApp, $entityManager, $zip);
        // dd("newFolder");

        // Ajoute le fichier XML dans l'archive ZIP
        $sql = $this->exportToSql($entityManager,$etablissement);
        // file_put_contents("export_$table.sql", );

        $zip->addFromString('database_export.sql', $sql);

        // Ferme le fichier ZIP
        $zip->close();
        $old = $this->getParameter('kernel.project_dir') . '/public/folderZip';
        $filesystem->remove($old);
        // Télécharge le fichier ZIP
        return new BinaryFileResponse($zipFilePath, Response::HTTP_OK, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="exportData.zip"',
            ]);
    }

    
public function importSqlFile(EntityManagerInterface $entityManager, string $pathToSqlFile): void
{
    $conn = $entityManager->getConnection();

    $sql = file_get_contents($pathToSqlFile);

    $conn->beginTransaction(); //DÉBUT TRANSACTION
    try {
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS=0;');

        // ICI tu modifies dynamiquement les valeurs (ex: logo/src)
        $sql = preg_replace(
            "#'https://rsmarttv-app-storage\.s3\.eu-west-1\.amazonaws\.com/uploads/([^']+)'#",
            "'$1'",
            $sql
        );
        $sql = preg_replace(
            "#'https://rsmarttv.cloud/([^']+)'#",
            "'$1'",
            $sql
        );
        $conn->executeStatement($sql);

        $conn->executeStatement('SET FOREIGN_KEY_CHECKS=1;');

        $conn->commit(); // TOUT EST BON → COMMIT
    } catch (\Throwable $e) {
        $conn->rollBack(); // ERREUR → ROLLBACK
        throw $e; // relancer l'erreur si besoin
    }
}

// Cette route permet d'importer les données depuis un fichier ZIP dans un autre serveur
#[Route('/import', name: 'app_import')]
public function import(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response
{
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    }

    // Récupère l'établissement de l'utilisateur
    $etablissement = $user->getEtablissement();
    
    // Récupération du fichier téléchargé depuis la requête
  /** @var UploadedFile|null $uploadedFile */
  $uploadedFile = $request->files->get('fileData');

    
    if (!$uploadedFile instanceof UploadedFile) {
        return new Response('Aucun fichier fourni', Response::HTTP_BAD_REQUEST);
    }

    // Initialisation des répertoires nécessaires
    $filesystem = new Filesystem();
    $uploadDir = $this->getParameter('kernel.project_dir') . '/var/uploads/';
    $destinationDir = $this->getParameter('project_dir') . '/public/images/';
    $extractDir = $uploadDir . 'extracted/';

    // Création des répertoires s'ils n'existent pas déjà
    $filesystem->mkdir([$uploadDir, $extractDir, $destinationDir], 0777);

    // Déplacement du fichier téléchargé vers le répertoire d'upload
    $zipFilePath = $uploadDir . 'exportData.zip';
    $uploadedFile->move($uploadDir, 'exportData.zip');

    // Extraction du fichier ZIP
    if (!$this->extractZip($zipFilePath, $extractDir)) {
        return new Response('Erreur lors de l’extraction du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Fusionner les dossiers extraits dans le répertoire de destination
    $this->mergeFolders($extractDir, $destinationDir);

    // Vérification de l'existence du fichier XML
    $xmlFilePath = $extractDir . 'database_export.sql';
    if (!file_exists($xmlFilePath)) {
        return new Response('Fichier XML non trouvé', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    // $this->importSqlFile($entityManager,$xmlFilePath,$etablissement->getId());
    try {
        // Importation des données dans la base de données
        // dd("test");
        $this->importSqlFile($entityManager,$xmlFilePath,$etablissement->getId());
        $filesystem->remove($zipFilePath);
        $filesystem->remove($extractDir);
      
    } catch (\Exception $e) {
        // En cas d'erreur, affichage d'un message d'erreur et redirection
        $this->addFlash('danger', 'L’établissement que vous tentez d’importer existe déjà dans la base de données. Veuillez vérifier les informations ou utiliser un autre identifiant.');
        return $this->redirectToRoute('home');
    }

    // Suppression des fichiers temporaires après l'importation

    // Affichage d'un message de succès et redirection
    $this->addFlash('changerPassword', 'Les données ont été importées avec succès.');
    return $this->redirectToRoute('home');
}

// Fonction pour fusionner deux répertoires (source et destination)
private function mergeFolders(string $source, string $destination): void
{
    $filesystem = new Filesystem();
    $finder = new Finder();
    $finder->files()->in($source);

    // Copie des fichiers du répertoire source vers le répertoire de destination
    foreach ($finder as $file) {
        $relativePath = $file->getRelativePathname();
        $targetPath = $destination . DIRECTORY_SEPARATOR . $relativePath;
        if (!$filesystem->exists($targetPath)) {
            $filesystem->copy($file->getRealPath(), $targetPath);
        }
    }
}

// Fonction pour extraire le contenu d'un fichier ZIP
private function extractZip(string $zipFilePath, string $extractPath): bool
{
    if (!is_dir($extractPath)) {
        mkdir($extractPath, 0777, true);
    }

    $zip = new ZipArchive();
    if ($zip->open($zipFilePath) !== true) {
        return false; // Retourne false si le fichier ZIP ne peut pas être ouvert
    }

    $zip->extractTo($extractPath); // Extraction du contenu du ZIP
    $zip->close(); // Fermeture du fichier ZIP

    return true;
}

// ////////////////////////////////////////////// Route pour exporter la télévision ////////////////////////////////



public function exportSqlData(EntityManagerInterface $entityManager,string $table, Object $etablissement){
    $sql = "SET FOREIGN_KEY_CHECKS=0;\n";
    $conn = $entityManager->getConnection();
    $tableName = $table;
    $stmt = $conn->prepare("SELECT * FROM `$tableName` WHERE etablissement_id = :etabId");
    $result = $stmt->executeQuery(['etabId' => $etablissement->getId()]);
    $rows = $result->fetchAllAssociative();
    
    $sql = ''; // Initialise la variable une seule fois pour accumuler toutes les requêtes
    
    if (count($rows) > 0) {
        $columns = array_map(fn($col) => "`$col`", array_keys($rows[0]));
        $sql .= "INSERT INTO `$tableName` (" . implode(", ", $columns) . ") VALUES\n";
        $valuesLines = [];
        foreach ($rows as $row) {
            $values = array_map(function ($val) use ($conn) {
                return $val === null ? 'NULL' : $conn->quote($val);
            }, array_values($row));
            $valuesLines[] = "(" . implode(", ", $values) . ")";
        }
        $sql .= implode(",\n", $valuesLines) . ";\n\n";
    }
    return $sql;
}
// Route pour l'exportation des données relatives à la télévision
#[Route('/export_tv', name: 'app_export_tv')]
public function exportDataCh(EntityManagerInterface $entityManager): Response
{
    // Vérifie si un utilisateur est connecté
    $filesystem = new Filesystem();
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    }

    // Récupère l'établissement de l'utilisateur
    $etablissement = $user->getEtablissement();

    // Récupère toutes les entrées de la télévision pour l'établissement spécifié
    $televisions = $entityManager->getRepository(Television::class)->findBy(['etablissement' => $etablissement]);

    // Crée un fichier XML

    // Parcourt chaque télévision et ajoute les données dans le fichier XML
    foreach($televisions as $television)
    {
        $metaDataTele = $entityManager->getClassMetadata(Television::class);

        // Crée un fichier ZIP pour l'exportation
        $zip = new ZipArchive();
        $zipFilePath = $this->getParameter('project_dir') . 'exportDataTv.zip';
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return new Response('Erreur lors de la création du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Exporte les données dans le ZIP
        $this->exportDataFiles($television, $metaDataTele, $entityManager, $zip);
    }

    // Ajoute le fichier XML dans l'archive ZIP
    $sql = "";
    $sql .= $this->exportSqlData($entityManager,'television',$etablissement);
    $zip->addFromString('database_export.sql', $sql);
    $zip->close();
    $old = $this->getParameter('kernel.project_dir') . '/public/folderZip';
    $filesystem->remove($old);
    // Retourne le fichier ZIP en réponse pour le téléchargement
    return new BinaryFileResponse($zipFilePath, Response::HTTP_OK, [
        'Content-Type' => 'application/zip',
        'Content-Disposition' => 'attachment; filename="exportDataTv.zip"',
    ]);
}

// ////////////////////////////////////////////// Route pour exporter la radio ////////////////////////////////

// Route pour l'exportation des données relatives à la radio
#[Route('/export_Radio', name: 'app_export_Radio')]
public function exportDataRadio(EntityManagerInterface $entityManager): Response
{
    // Vérifie si un utilisateur est connecté
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    }

    // Récupère l'établissement de l'utilisateur
    $etablissement = $user->getEtablissement();

    // Récupère toutes les catégories de radio pour l'établissement spécifié
    $CategorieRadio = $entityManager->getRepository(CategorieRadio::class)->findBy(['etablissement' => $etablissement]);

    // Crée un fichier XML
    $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><database></database>');
    $newcategorie = $xml->addChild('CategorieRadio');

    // Parcourt chaque catégorie de radio et ajoute les données dans le fichier XML
    foreach($CategorieRadio as $categorie)
    {
        $rowNode = $newcategorie->addChild('row');
        $metaDatacategorie = $entityManager->getClassMetadata(CategorieRadio::class);

        // Crée un fichier ZIP pour l'exportation
        $zip = new ZipArchive();
        $zipFilePath = $this->getParameter('project_dir') . 'exportDataTv.zip';
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return new Response('Erreur lors de la création du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Exporte les données dans le ZIP
        $this->exportDataFiles($categorie, $metaDatacategorie, $entityManager, $zip);
    }
    $sql = "";
    $sql .= $this->exportSqlData($entityManager,'categorie_radio',$etablissement);
    $sql .= $this->exportSqlData($entityManager,'radio',$etablissement);

    // Ajoute le fichier XML dans l'archive ZIP
    $zip->addFromString('database_export.sql', $sql);
    $zip->close();
    $filesystem = new Filesystem();

    $old = $this->getParameter('kernel.project_dir') . '/public/folderZip';
    $filesystem->remove($old);
    // Retourne le fichier ZIP en réponse pour le téléchargement
    return new BinaryFileResponse($zipFilePath, Response::HTTP_OK, [
        'Content-Type' => 'application/zip',
        'Content-Disposition' => 'attachment; filename="exportDataRadio.zip"',
    ]);
}

public function importSqlFile1(EntityManagerInterface $entityManager, string $pathToSqlFile, int $newEtabId,string $tvOuRd): void
{
    $conn = $entityManager->getConnection();
    $sql = file_get_contents($pathToSqlFile);
    $conn->executeStatement('SET FOREIGN_KEY_CHECKS=0;');

     $sql = preg_replace(
            "#'https://rsmarttv-app-storage\.s3\.eu-west-1\.amazonaws\.com/uploads/([^']+)'#",
            "'$1'",
            $sql
        );
    $categorieIdMap = [];

    $existingRadios = $conn->fetchOne('SELECT COUNT(*) FROM radio WHERE etablissement_id = :etab', [
    'etab' => $newEtabId
    ]);
    if ($existingRadios > 0 && $tvOuRd === 'radio') {
        throw new \Exception();
    }
    $existingTV = $conn->fetchOne('SELECT COUNT(*) FROM television WHERE etablissement_id = :etab', [
    'etab' => $newEtabId
    ]);
    if ($existingTV > 0 && $tvOuRd === 'tv') {
        throw new \Exception();
    }
    preg_match_all('/INSERT INTO\s+[`"]?(\w+)[`"]?\s*\((.*?)\)\s*VALUES\s*(.*?);(?=\s*INSERT INTO|\s*$)/is', $sql, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $table = strtolower($match[1]);
        $columns = array_map('trim', explode(',', $match[2]));
        $valuesPart = trim($match[3]);

        try {
            // CATEGORIE RADIO
            if ($table === 'categorie_radio') {
                $valuesPart = preg_replace_callback('/\(([^)]+)\)/', function ($valueMatch) use ($newEtabId, &$categorieIdMap, $conn) {
                    $fields = array_map('trim', explode(',', $valueMatch[1]));

                    $oldId = trim($fields[0], "' ");
                    $nom = trim($fields[2], "' ");

                    $existing = $conn->fetchAssociative(
                        'SELECT id FROM categorie_radio WHERE etablissement_id = :etab AND nom = :nom LIMIT 1',
                        ['etab' => $newEtabId, 'nom' => $nom]
                    );

                    if ($existing) {
                        $newId = $existing['id'];
                    } else {
                        $newId = mt_rand(100000, 999999);
                        $fields[0] = $newId;
                        $fields[1] = "'" . $newEtabId . "'";

                        $sqlInsert = "INSERT INTO categorie_radio (" .
                            "id, etablissement_id, nom, position, logo, active, fr, en, es, pt, it, ru, de, zh, ar" .
                            ") VALUES (" . implode(', ', $fields) . ")";
                        $conn->executeStatement($sqlInsert);
                    }

                    $categorieIdMap[$oldId] = $newId;
                    return ''; // on a déjà inséré manuellement
                }, $valuesPart);

                continue;
            }

            // RADIO
            if ($table === 'radio') {
                $valueStrings = [];

                preg_match_all('/\(([^)]+)\)/', $valuesPart, $radioMatches);
                foreach ($radioMatches[1] as $radioValue) {
                    
                    $fields = array_map('trim', explode(',', $radioValue));
                    $fields[1] = "'" . $newEtabId . "'";
                    $oldCatId = trim($fields[2], "' ");
                    if (isset($categorieIdMap[$oldCatId])) {
                        $fields[2] = "'" . $categorieIdMap[$oldCatId] . "'";
                    }

                    $valueStrings[] = '(' . implode(', ', $fields) . ')';
                }

                if (!empty($valueStrings)) {
                $query = "INSERT INTO radio (" . implode(', ', $columns) . ") VALUES " . implode(', ', $valueStrings);
                $conn->executeStatement($query);
            }
                continue;
        }

     if ($table === 'television') {
    preg_match_all('/\(([^)]+)\)/', $valuesPart, $tvMatches);
    foreach ($tvMatches[1] as $tvValue) {
        $fields = array_map('trim', explode(',', $tvValue));
        $newId = mt_rand(100000, 999999);
        $fields[0] = $newId;
        $fields[1] = "'" . $newEtabId . "'";
        $valueStrings[] = '(' . implode(', ', $fields) . ')';
    }

    if (!empty($valueStrings)) {
        $query = "INSERT INTO television (" . implode(', ', $columns) . ") VALUES " . implode(', ', $valueStrings);
        $conn->executeStatement($query);
    }

    continue;
}
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }

    $conn->executeStatement('SET FOREIGN_KEY_CHECKS=1;');
}


// Route pour importer des données de télévision
#[Route('/importTv/{id}', name: 'app_import_tv')]
public function importDataTv(int $id, Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response
{
    // Récupère le fichier téléchargé
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    }

    // Récupère l'établissement de l'utilisateur
    $etablissement = $user->getEtablissement();
    $uploadedFile = $request->files->get('fileData');
    if (!$uploadedFile instanceof UploadedFile) {
        return new Response('Aucun fichier fourni', Response::HTTP_BAD_REQUEST);
    }

    // Crée les répertoires nécessaires pour le téléchargement et l'extraction des fichiers
    $filesystem = new Filesystem();
    $uploadDir = $this->getParameter('kernel.project_dir') . '/var/uploads/';
    $destinationDir = $this->getParameter('project_dir') . '/public/images/';
    $extractDir = $uploadDir . 'extracted/';
    $filesystem->mkdir([$uploadDir, $extractDir, $destinationDir], 0777);

    // Déplace le fichier téléchargé vers le répertoire de téléchargement
    $zipFilePath = $uploadDir . 'exportData.zip';
    $uploadedFile->move($uploadDir, 'exportData.zip');

    // Extrait le fichier ZIP téléchargé
    if (!$this->extractZip($zipFilePath, $extractDir)) {
        return new Response('Erreur lors de l’extraction du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Fusionne les fichiers extraits avec ceux du répertoire public
    $this->mergeFolders($extractDir, $destinationDir);

    // Charge et parse le fichier XML
    $xmlFilePath = $extractDir . 'database_export.sql';
    if (!file_exists($xmlFilePath)) {
        return new Response('Fichier XML non trouvé', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    
    // Tente d'importer les données et capture les exceptions
    try{
        $this->importSqlFile1($entityManager,$xmlFilePath,$etablissement->getId(),'tv');
    } catch(\Exception $e) {
        // Affiche un message d'erreur si l'importation échoue
        $this->addFlash('danger', 'Le fichier TV que vous tentez d’importer contient des données déjà existantes. Veuillez supprimer les televisions existantes.');
        return $this->redirectToRoute('app_television');
    }

    // Nettoie les fichiers temporaires
    $filesystem->remove($zipFilePath);
    $filesystem->remove($extractDir);

    // Affiche un message de succès
    $this->addFlash('changerPassword', 'Les données ont été importées avec succès.');
    return $this->redirectToRoute('app_television');
}

// Route pour importer des données de radio
#[Route('/importRadio/{id}', name: 'app_import_Radio')]
public function importDataRadio(int $id, Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response
{
    // Récupère le fichier téléchargé
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    }

    // Récupère l'établissement de l'utilisateur
    $etablissement = $user->getEtablissement();
    $uploadedFile = $request->files->get('fileData');
    if (!$uploadedFile instanceof UploadedFile) {
        return new Response('Aucun fichier fourni', Response::HTTP_BAD_REQUEST);
    }

    // Crée les répertoires nécessaires pour le téléchargement et l'extraction des fichiers
    $filesystem = new Filesystem();
    $uploadDir = $this->getParameter('kernel.project_dir') . '/var/uploads/';
    $destinationDir = $this->getParameter('kernel.project_dir') . '/public/images/';
    $extractDir = $uploadDir . 'extracted/';
    $filesystem->mkdir([$uploadDir, $extractDir, $destinationDir], 0777);

    // Déplace le fichier téléchargé vers le répertoire de téléchargement
    $zipFilePath = $uploadDir . 'exportData.zip';
    $uploadedFile->move($uploadDir, 'exportData.zip');

    // Extrait le fichier ZIP téléchargé
    if (!$this->extractZip($zipFilePath, $extractDir)) {
        return new Response('Erreur lors de l’extraction du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Fusionne les fichiers extraits avec ceux du répertoire public
    $this->mergeFolders($extractDir, $destinationDir);

    // Charge et parse le fichier XML
    $xmlFilePath = $extractDir . 'database_export.sql';
    if (!file_exists($xmlFilePath)) {
        return new Response('Fichier XML non trouvé', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    
    // Tente d'importer les données et capture les exceptions
    try{
        $this->importSqlFile1($entityManager,$xmlFilePath,$etablissement->getId(),'radio');
    } catch(\Exception $e) {
        // Affiche un message d'erreur si l'importation échoue
        $this->addFlash('danger', 'La station radio que vous tentez d’importer existe déjà ou un problème a été détecté dans la base de données. Veuillez supprimer les radios et les catégories existantes');
        return $this->redirectToRoute('app_radio');
    }

    // Nettoie les fichiers temporaires
    $filesystem->remove($zipFilePath);
    $filesystem->remove($extractDir);

    // Affiche un message de succès
    $this->addFlash('changerPassword', 'Les données ont été importées avec succès.');
    return $this->redirectToRoute('app_radio');
}

// ////////////////////////////////////////////// Fonction pour réinitialiser les tentatives de mot de passe ////////////////////////////////

// Route pour réinitialiser les tentatives de mot de passe
#[Route('/app_Reinisialiser_password/{id}', name: 'app_Reinisialiser_password')]
public function Reinisialiser_password(int $id, EntityManagerInterface $entityManager)
{
    // Récupère l'utilisateur
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



