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
        $destDir = $destinationBase . $relativePath;

        // S'assurer que les séparateurs sont standard
        $destDir = str_replace(['\\', '//'], '/', $destDir);

        $destPath = $destDir . '/' . $file;
        // dd($destDir);

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


// fonction pour ajouter toute les donnees sur un fichier xml et apres je l'ajoute sur un dossier zip
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
    
    // route pour exporter les donnes d'etablissements
    #[Route('/export', name: 'app_export')]
    public function export(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $etablissement = $user->getEtablissement();
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        // Créer la racine du fichier XML avec la déclaration de version et d'encodage
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><database></database>');

        // Ajoute le premier élément enfant dans le fichier XML
        $newEtablissementNode = $xml->addChild('etablissement');
        // Ajoute le deuxième élément enfant dans le fichier XML
        $newAppConfigNode = $xml->addChild('AppConfig');
    
        $rowNode = $newEtablissementNode->addChild('row');
        $rowNode1 = $newAppConfigNode->addChild('row');
    
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
        $this->exportData($etablissement, $metaData, $rowNode, $entityManager, $zip);

        // Appel de la fonction pour exporter les données de la configuration de l'application
        $this->exportData($appConfig, $metaDataConfigApp, $rowNode1, $entityManager, $zip);

        // Ajoute le fichier XML dans l'archive ZIP
        $zip->addFromString('database_export.xml', $xml->asXML());

        // Ferme le fichier ZIP
        $zip->close();
        // Télécharge le fichier ZIP
        return new BinaryFileResponse($zipFilePath, Response::HTTP_OK, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="exportData.zip"',
            ]);
    }

    
// Cette route permet d'importer les données depuis un fichier ZIP dans un autre serveur
#[Route('/import', name: 'app_import')]
public function import(Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response
{
    // Récupération du fichier téléchargé depuis la requête
    $uploadedFile = $request->files->get('fileData');

    // Vérification si un fichier a été fourni
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
    $xmlFilePath = $extractDir . 'database_export.xml';
    if (!file_exists($xmlFilePath)) {
        return new Response('Fichier XML non trouvé', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Chargement du fichier XML
    $xml = simplexml_load_file($xmlFilePath);
    // try {
        // Importation des données dans la base de données
        $this->importEtablissement($xml, $tokenGenerator, $entityManager);
    // } catch (\Exception $e) {
    //     // En cas d'erreur, affichage d'un message d'erreur et redirection
    //     $this->addFlash('danger', 'L’établissement que vous tentez d’importer existe déjà dans la base de données. Veuillez vérifier les informations ou utiliser un autre identifiant.');
    //     return $this->redirectToRoute('home');
    // }

    // Suppression des fichiers temporaires après l'importation
    $filesystem->remove($zipFilePath);
    $filesystem->remove($extractDir);

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

// Fonction pour importer les données de l'établissement à partir du fichier XML
private function importEtablissement(\SimpleXMLElement $xml, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager): void
{
    // Parcours des lignes pour importer les établissements
    foreach ($xml->etablissement->row as $row) {
        $etablissement = new Etablissement();
        // Importation de chaque champ du fichier XML vers l'entité
        foreach ($row->children() as $field => $value) {
            $this->setProperty($etablissement, $tokenGenerator, $field, (string) $value, $etablissement);
        }
        $entityManager->persist($etablissement);

        // Importation des relations (si elles existent) pour chaque ligne
        foreach ($row->children() as $field => $childNode) {
            if ($childNode->count() > 0) {
                $this->importRelation($childNode, $tokenGenerator, $etablissement, $field, $entityManager);
            }
        }
    }

    // Importation des configurations de l'application
    foreach ($xml->AppConfig->row as $row) {
        $AppConfig = new ConfigApp();
        foreach ($row->children() as $field => $value) {
            if (!$value) {
                $value = ""; // Valeur vide par défaut
            }
            $this->setProperty($AppConfig, $tokenGenerator, $field, (string) $value, $etablissement);
        }
        $AppConfig->setEtablissement($etablissement);
        $entityManager->persist($AppConfig);
    }

    $entityManager->flush(); // Enregistrement des données dans la base
}

// Fonction pour importer les relations de l'entité
private function importRelation(\SimpleXMLElement $node, TokenGeneratorInterface $tokenGenerator, Etablissement $etablissement, string $relationName, EntityManagerInterface $entityManager): void
{
    // Parcours des éléments enfants pour importer des relations
    foreach ($node->children() as $itemNode) {
        $relationClass = 'App\\Entity\\' . ucfirst($itemNode->getName());
        if (!class_exists($relationClass)) {
            continue; // Si la classe de relation n'existe pas, on passe à l'élément suivant
        }

        $relatedEntity = new $relationClass();
        // Importation des champs pour la relation
        foreach ($itemNode->children() as $field => $value) {
            $this->setProperty($relatedEntity, $tokenGenerator, $field, (string) $value, $etablissement);
        }
        $entityManager->persist($relatedEntity); // Persist de la relation
        $entityManager->flush(); // Enregistrement dans la base
    }
}

// Fonction pour définir une propriété de l'entité (générique pour différents types d'entités)
private function setProperty(object $entity, TokenGeneratorInterface $tokenGenerator, string $field, mixed $value, object $etablissement): void
{
    $setter = 'set' . ucfirst($field); // Recherche du setter de la propriété
    $firstTwo = substr($etablissement->getId(), 0, 2); // Récupération des deux premiers caractères de l'ID de l'établissement

    // Si la méthode setter n'existe pas, on arrête l'exécution
    if (!method_exists($entity, $setter)) {
        return;
    }

    // Traitement spécifique pour certains champs (par exemple, les IDs et catégories)
    if ($field === "id" && ($entity instanceof CategorieRadio || $entity instanceof CategorieLivreAudio || $entity instanceof CategorieVod ||  $entity instanceof ServiceEtablissement || $entity instanceof Categories || $entity instanceof Television)) {
        $value = (int)($firstTwo . $value); // Préfixe l'ID avec les deux premiers caractères de l'établissement
        $exEntity = $this->entityManager->getRepository($entity::class)->findOneBy(['etablissement' => $etablissement, 'id' => $value]);
        if ($exEntity) {
            return; // Si l'entité existe déjà, on ne l'ajoute pas
        }
    }
    if ($field === 'categorie' && $entity instanceof \App\Entity\Radio) {
        $value = (int)($firstTwo . $value); 
        $value = $this->entityManager->getRepository(\App\Entity\CategorieRadio::class)->find($value);
        if (!$value) {
            return; // si la catégorie n'existe pas, on arrête
        }

    }
    if ($field === 'chaine' && $entity instanceof \App\Entity\Chambre) {
        $value = (int)($firstTwo . $value); 
        $value = $this->entityManager->getRepository(\App\Entity\Television::class)->find($value);
        if (!$value) {
            return; // si la catégorie n'existe pas, on arrête
        }

    }
    if ($field === 'categorie' && $entity instanceof \App\Entity\Vod) {
        $value = (int)($firstTwo . $value); 
        $value = $this->entityManager->getRepository(\App\Entity\CategorieVod::class)->find($value);
        if (!$value) {
            return; // si la catégorie n'existe pas, on arrête
        }

    }
    if ($field === 'categorie' && $entity instanceof \App\Entity\Livreaudio) {
        $value = (int)($firstTwo . $value); 
        $value = $this->entityManager->getRepository(\App\Entity\CategorieLivreaudio::class)->find($value);
        if (!$value) {
            return; // si la catégorie n'existe pas, on arrête
        }

    }
    if ($field === 'categories' && $entity instanceof \App\Entity\Services) {
        $value = (int)($firstTwo . $value); 
        $value = $this->entityManager->getRepository(\App\Entity\Categories::class)->find($value);
        if (!$value) {
            return; // si la catégorie n'existe pas, on arrête
        }

    }
    if ($field === 'service' && $entity instanceof \App\Entity\Chambre) {
        $value = (int)($firstTwo . $value); 
        $value = $this->entityManager->getRepository(\App\Entity\ServiceEtablissement::class)->find($value);
        if (!$value) {
            return; // si la catégorie n'existe pas, on arrête
        }

    }
    if ($field === 'service' && $entity instanceof \App\Entity\Questionnaire) {
        $value = (int)($firstTwo . $value); 
        $value = $this->entityManager->getRepository(\App\Entity\ServiceEtablissement::class)->find($value);
        if (!$value) {
            return; // si la catégorie n'existe pas, on arrête
        }

    }
    // Traitements pour différents champs spécifiques (comme les catégories, services, etc.)
    if ($field === 'etablissement') {
        $value = $etablissement; // Associe l'établissement à la propriété
    }

    // Gestion des rôles et des tokens
    if ($field === 'roles') {
        $value = is_array($value) ? $value : json_decode($value, true) ?? [$value];
    }
    if ($field === 'resetToken') {
        $value = $tokenGenerator->generateToken(); // Génère un token pour le champ resetToken
    }

    // Conversion des dates si nécessaire
    if ($field === 'dernierTemp' || $field === 'derniertempExport') {
        try {
            $value = new \DateTime($value); // Conversion de la valeur en objet DateTime
        } catch (\Exception $e) {
            $value = new \DateTime('2025-02-24 12:30:00'); // Valeur par défaut en cas d'erreur
        }
    }

    // Appel du setter pour définir la propriété
    $entity->$setter($value);
}


// ////////////////////////////////////////////// Route pour exporter la télévision ////////////////////////////////

// Route pour l'exportation des données relatives à la télévision
#[Route('/export_tv', name: 'app_export_tv')]
public function exportDataCh(EntityManagerInterface $entityManager): Response
{
    // Vérifie si un utilisateur est connecté
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    }

    // Récupère l'établissement de l'utilisateur
    $etablissement = $user->getEtablissement();

    // Récupère toutes les entrées de la télévision pour l'établissement spécifié
    $televisions = $entityManager->getRepository(Television::class)->findBy(['etablissement' => $etablissement]);

    // Crée un fichier XML
    $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><database></database>');
    $newTele = $xml->addChild('television');

    // Parcourt chaque télévision et ajoute les données dans le fichier XML
    foreach($televisions as $television)
    {
        $rowNode = $newTele->addChild('row');
        $metaDataTele = $entityManager->getClassMetadata(Television::class);

        // Crée un fichier ZIP pour l'exportation
        $zip = new ZipArchive();
        $zipFilePath = $this->getParameter('kernel.project_dir') . '/var/exportDataTv.zip';
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return new Response('Erreur lors de la création du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Exporte les données dans le ZIP
        $this->exportData($television, $metaDataTele, $rowNode, $entityManager, $zip);
    }

    // Ajoute le fichier XML dans l'archive ZIP
    $zip->addFromString('database_export.xml', $xml->asXML());
    $zip->close();

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
        $zipFilePath = $this->getParameter('kernel.project_dir') . '/var/exportDataTv.zip';
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return new Response('Erreur lors de la création du fichier ZIP', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Exporte les données dans le ZIP
        $this->exportData($categorie, $metaDatacategorie, $rowNode, $entityManager, $zip);
    }

    // Ajoute le fichier XML dans l'archive ZIP
    $zip->addFromString('database_export.xml', $xml->asXML());
    $zip->close();

    // Retourne le fichier ZIP en réponse pour le téléchargement
    return new BinaryFileResponse($zipFilePath, Response::HTTP_OK, [
        'Content-Type' => 'application/zip',
        'Content-Disposition' => 'attachment; filename="exportDataRadio.zip"',
    ]);
}

// Fonction pour importer des données d'une télévision ou d'une radio
private function importchamp(int $id, Bool $object, \SimpleXMLElement $xml, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager): void
{
    // Si l'objet est une télévision
    if ($object)
    {
        // Parcourt chaque ligne de télévision dans le fichier XML et importe les données
        foreach ($xml->television->row as $row) {
            $television = new Television();
            $etablissement = $entityManager->getRepository(Etablissement::class)->findOneBy(['id'=>$id]);

            // Attribue les valeurs aux propriétés de l'objet télévision
            foreach ($row->children() as $field => $value) {
                $this->setProperty($television, $tokenGenerator, $field, (string) $value, $etablissement);
            }
            $entityManager->persist($television);

            // Vérifie s'il y a des relations à importer
            foreach ($row->children() as $field => $childNode) {
                if ($childNode->count() > 0) {
                    $this->importRelation($childNode, $tokenGenerator, $etablissement, $field, $entityManager);
                }
            }
        }
    }
    // Si l'objet est une station radio
    else {
        // Parcourt chaque ligne de radio dans le fichier XML et importe les données
        foreach ($xml->CategorieRadio->row as $row) {
            $CategorieRadio = new CategorieRadio();
            $etablissement = $entityManager->getRepository(Etablissement::class)->findOneBy(['id'=>$id]);

            // Attribue les valeurs aux propriétés de l'objet catégorie de radio
            foreach ($row->children() as $field => $value) {
                $this->setProperty($CategorieRadio, $tokenGenerator, $field, (string) $value, $etablissement);
            }

            // Si un identifiant est trouvé, persiste l'entité
            if ($CategorieRadio->getId())
            {
                $entityManager->persist($CategorieRadio);        
                $entityManager->flush();
            }

            // Vérifie s'il y a des relations à importer
            foreach ($row->children() as $field => $childNode) {
                if ($childNode->count() > 0) {
                    $this->importRelation($childNode, $tokenGenerator, $etablissement, $field, $entityManager);
                }
            }
        }
    }

    // Effectue la sauvegarde des modifications dans la base de données
    $entityManager->flush();
}

// Route pour importer des données de télévision
#[Route('/importTv/{id}', name: 'app_import_tv')]
public function importDataTv(int $id, Request $request, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator): Response
{
    // Récupère le fichier téléchargé
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
    $xmlFilePath = $extractDir . 'database_export.xml';
    if (!file_exists($xmlFilePath)) {
        return new Response('Fichier XML non trouvé', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    $xml = simplexml_load_file($xmlFilePath);

    // Tente d'importer les données et capture les exceptions
    try{
        $this->importchamp($id, true, $xml, $tokenGenerator, $entityManager);
    } catch(\Exception $e) {
        // Affiche un message d'erreur si l'importation échoue
        $this->addFlash('danger', 'Le fichier TV que vous tentez d’importer contient des données déjà existantes. Veuillez vérifier les informations avant de continuer.');
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
    $xmlFilePath = $extractDir . 'database_export.xml';
    if (!file_exists($xmlFilePath)) {
        return new Response('Fichier XML non trouvé', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    $xml = simplexml_load_file($xmlFilePath);

    // Tente d'importer les données et capture les exceptions
    try{
        $this->importchamp($id, false, $xml, $tokenGenerator, $entityManager);
    } catch(\Exception $e) {
        // Affiche un message d'erreur si l'importation échoue
        $this->addFlash('danger', 'La station radio que vous tentez d’importer existe déjà ou un problème a été détecté dans la base de données. Veuillez utiliser un autre identifiant ou vérifier les informations.');
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
