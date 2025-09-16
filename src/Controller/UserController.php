<?php
// src/Controller/UserController.php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Etablissement;
use App\Entity\Questionnaire;
use App\Entity\ServiceEtablissement;
use App\Entity\CategorieRadio;
use App\Entity\Radio;
use App\Entity\CategorieLivreAudio;
use App\Entity\LivreAudio;
use App\Entity\Television;
use App\Entity\Categories;
use App\Entity\CategorieVod;
use App\Entity\Chambre;
use App\Entity\ConfigApp;
use App\Entity\Configmobile;
use App\Entity\HistoriqueAnnonce;
use App\Entity\Historiquegratuite;
use App\Entity\Jeux;
use App\Entity\ResultatQuestionnaire;
use App\Entity\ServiceEnChambre;
use App\Entity\Services;
use App\Entity\Support;
use App\Entity\TypeServiceEnChambre;
use App\Entity\Vod;
use DateTime;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserController extends AbstractController
{
    private $requestStack;
    private $client;

    public function __construct(RequestStack $requestStack,HttpClientInterface $client)
    {
        $this->requestStack = $requestStack;
        $this->client = $client;

    }


    #[Route('/register', name: 'app_register')]
    public function register(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $bugs = [
            'nom'=>'',
            'prenom' => '',
            'email' => '',
            'nom_etablissement' => '',
            'adresse' => '',
            'code' => '',
            'ville' => '',
            'username' => '',
            'password' => ''
        ];  

        $request = Request::createFromGlobals();
    
        // Enregistrement des informations de l'établissement
        $etablissement = new Etablissement();
    
        $idetablissement = mt_rand(10000, 99999);
        $genre = $request->get("genre");
        $nom = $request->get("nom");
        $prenom = $request->get("prenom");
        $nom_etablissement = $request->get("nom_etablissement");
        $adresse = $request->get("adresse");
        $code = $request->get("code");
        $ville = $request->get("ville");
        $pays = $request->get("pays");
        $token = $request->get("fos_user_registration_form");
        $supports = $request->get("support");
        if ($supports === null) {
            $bugs['support'] = 'Cocher un support c\'est obligatoire.';
        }
        else{
            foreach ($supports as $index => $support) {
                $NewSupport = new Support();
                $NewSupport->setEtablissement($etablissement);
                $NewSupport->setProtocole($support);
                $NewSupport->setNom($index);
                $entityManager->persist($NewSupport);
            }
            $etablissement->setGenre($genre);
            $etablissement->setNom($nom);
            $etablissement->setId($idetablissement);
            $etablissement->setPrenom($prenom);
            $etablissement->setNomEtablissement($nom_etablissement);
        }
        $etablissement->setAdresse($adresse);
        $etablissement->setCode($code);
        $etablissement->setDescription('Hôtel');
        $etablissement->setVille($ville);
        $etablissement->setPays($pays);
        $etablissement->setTypeText("Times New Roman, serif");
        $etablissement->setCouleurText("#ffffff");
        $etablissement->setTailleText(26);
        $etablissement->setVolumeDemarage(10);

        
        
        
        
        // Enregistrement de l'utilisateur
        $user = new User();
        
        $username = $request->get("username");
        $password = $request->get("password");
        $email = $request->get("email");
        // Pour vérifier si l'email existe ou non, j'ai créé une API avec AbstractAPI. 
        //J'envoie l'email en tant que requête et cela retourne une réponse indiquant si l'email existe ou non.(https://app.abstractapi.com/)
        // {"email":"rajae1@gmail.com","autocorrect":"","deliverability":"UNDELIVERABLE","quality_score":"0.00","is_valid_format":{"value":true,"text":"TRUE"},"is_free_ema
        // {"email":"zineb.ell.zee@gmail.com","autocorrect":"","deliverability":"DELIVERABLE","quality_score":"0.95","is_valid_format":{"value":true,"text":"TRUE"},"is_fre
        // $response = $this->client->request('GET', 'https://emailvalidation.abstractapi.com/v1/', [
        //     'query' => [
        //         'api_key' => '28550f2f43bd4e808bfa565b3bc2adab',
        //         'email' => $email,
        //     ]
        // ]);
        // $data = $response->toArray();
        // $deliverability = $data['deliverability'] ?? 'Unknown';
        // if($deliverability != 'DELIVERABLE')
        // {
        //     $bugs['email'] = 'L\'adresse e-mail est invalide ou inaccessible. Veuillez entrer une adresse e-mail existante. ';

        // }
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEmailAdmin($email);
        $user->setDerniertempExport(new DateTime());
        $user->setTentativeExport(0);
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setResetToken($token['_token']);

        $sourceUser = $entityManager->getRepository(User::class)->find(103);
        if ($sourceUser) {
            $user->setMjTv($sourceUser->getMjTv());
            $user->setChangeCat($sourceUser->getChangeCat());
            $user->setTELEVISION($sourceUser->getTELEVISION());
            $user->setSTATISTIQUECHAINETV($sourceUser->getSTATISTIQUECHAINETV());
            $user->setRADIO($sourceUser->getRADIO());
            $user->setSERVICE($sourceUser->getSERVICE());
            $user->setVOD($sourceUser->getVOD());
            $user->setMUSIQUE($sourceUser->getMUSIQUE());
            $user->setJEUX($sourceUser->getJEUX()) ;
            $user->setSERVICESPAYANTS($sourceUser->getSERVICESPAYANTS());
            $user->setQUESTIONNAIRE($sourceUser->getQUESTIONNAIRE()) ;
            $user->setAPPLICATION($sourceUser->getAPPLICATION()) ;
            $user->setANNONCES($sourceUser->getANNONCES());
            $user->setCHARTES($sourceUser->getCHARTES()) ;
            $user->setVIDEOS($sourceUser->getVIDEOS()) ;
            $user->setSupportConnect($sourceUser->getSupportConnect()) ;
            $user->setRMOBILE($sourceUser->getRMOBILE()) ;
            $user->setRREMOTE($sourceUser->getRREMOTE());
            $user->setLIVREAUDIO($sourceUser->getLIVREAUDIO());
            $user->setMessagePersonnel($sourceUser->getMessagePersonnel());
            $user->setSupportConnect($sourceUser->getSupportConnect());
            $user->setAjoutTV($sourceUser->getAjoutTV());
            $user->setModifierTv($sourceUser->getModifierTv());
            $user->setSupprimerTV($sourceUser->getSupprimerTV());
            $user->setSauvegarderTv($sourceUser->getSauvegarderTv());
            $user->setGratuiteTv($sourceUser->getGratuiteTv());
            $user->setAjoutRadio($sourceUser->getAjoutRadio());
            $user->setModifierRadio($sourceUser->getModifierRadio());
            $user->setSupprimerRadio($sourceUser->getSupprimerRadio());
            $user->setSauvegarderRadio($sourceUser->getSauvegarderRadio());
            $user->setAjouteService($sourceUser->getAjouteService());
            $user->setModifierService($sourceUser->getModifierService());
            $user->setSupprimerService($sourceUser->getSupprimerService());
            $user->setSauvegarderService($sourceUser->getSauvegarderService());
            $user->setLancerArretService($sourceUser->getLancerArretService());
            $user->setAjouteVod($sourceUser->getAjouteVod());
            $user->setModifierVod($sourceUser->getModifierVod());
            $user->setSupprimerVod($sourceUser->getSupprimerVod());
            $user->setSauvegarderVod($sourceUser->getSauvegarderVod());
            $user->setLancerVod($sourceUser->getLancerVod());
            $user->setAjouterJeux($sourceUser->getAjouterJeux());
            $user->setModifierJeux($sourceUser->getModifierJeux());
            $user->setSupprimerJeux($sourceUser->getSupprimerJeux());
            $user->setSauvegarderJeux($sourceUser->getSauvegarderJeux());
            $user->setAjoutApp($sourceUser->getAjoutApp());
            $user->setModifierApp($sourceUser->getModifierApp());
            $user->setSupprimerApp($sourceUser->getSupprimerApp());
            $user->setSauvegarderApp($sourceUser->getSauvegarderApp());
            $user->setAjouteqs($sourceUser->getAjouteqs());
            $user->setModifierqs($sourceUser->getModifierqs());
            $user->setSupprimerqs($sourceUser->getSupprimerqs());
            $user->setSauvgarderqs($sourceUser->getSauvgarderqs());
            $user->setAjoutLiveAudio($sourceUser->getAjoutLiveAudio());
            $user->setModifierLiveAudio($sourceUser->getModifierLiveAudio());
            $user->setSupprimerLiveAudio($sourceUser->getSupprimerLiveAudio());
            $user->setSauvegarderLiveAudio($sourceUser->getSauvegarderLiveAudio());
            $user->setAjouteSupport($sourceUser->getAjouteSupport());
            $user->setModifierSupport($sourceUser->getModifierSupport());
            $user->setSupprimerSupport($sourceUser->getSupprimerSupport());
            $user->setRedimarerSupport($sourceUser->getRedimarerSupport());
            $user->setEnvoyerMessage($sourceUser->getEnvoyerMessage());
            $user->setAjouterAnnonce($sourceUser->getAjouterAnnonce());
            $user->setModifierAnnonce($sourceUser->getModifierAnnonce());
            $user->setSuppAnnonce($sourceUser->getSuppAnnonce());
            $user->setSauvegarderAnnonce($sourceUser->getSauvegarderAnnonce());
            $user->setRREMOTE($sourceUser->getRREMOTE());
            $user->setRMOBILE($sourceUser->getRMOBILE());
            $user->setAjoutCategorie($sourceUser->getAjoutCategorie());
            $user->setSauvegarderAcceuil($sourceUser->isSauvegarderAcceuil());
            $user->setFondEcran($sourceUser->isFondEcran());
            $user->setlancerTV($sourceUser->islancerTV());
            $user->setajoutMusique($sourceUser->isajoutMusique());
            $user->setModifierMusique($sourceUser->isModifierMusique());
            $user->setSuppMusique($sourceUser->isSuppMusique());
            $user->setSauvegarderMusique($sourceUser->isSauvegarderMusique());
            $user->setlancerMusique($sourceUser->islancerMusique());
            $user->setlancerJeux($sourceUser->islancerJeux()); 
            $user->setlancerVideo($sourceUser->islancerVideo()); 
            $user->setlancerLivreAudio($sourceUser->islancerLivreAudio()); 
            $user->setajoutVideo($sourceUser->isajoutVideo());
            $user->setModifierVideo($sourceUser->isModifierVideo());
            $user->setSauvegarderVideo($sourceUser->isSauvegarderVideo());
            $user->setsuppVideo($sourceUser->issuppVideo());
            $user->setlancerRadio($sourceUser->islancerRadio());


            $user->setCatLivreAudio($sourceUser->isCatLivreAudio());
            $user->setCatRadio($sourceUser->isCatRadio());
            $user->setCatVod($sourceUser->isCatVod());
            $user->setServiceEnChambre($sourceUser->isServiceEnChambre());
            
            $user->setajoutCatLivreAudio($sourceUser->isajoutCatLivreAudio());
            $user->setModifierCatLivreAudio($sourceUser->isModifierCatLivreAudio());
            $user->setSauvegarderCatLivreAudio($sourceUser->isSauvegarderCatLivreAudio());
            $user->setSuppCatLivreAudio($sourceUser->isSuppCatLivreAudio());

            $user->setAjoutCatRadio($sourceUser->isAjoutCatRadio());
            $user->setModifierCatRadio($sourceUser->isModifierCatRadio());
            $user->setSauvegarderCatRadio($sourceUser->isSauvegarderCatRadio());
            $user->setSuppCatRadio($sourceUser->isSuppCatRadio());

            $user->setAjoutCatVod($sourceUser->isAjoutCatVod());
            $user->setModifierCatVod($sourceUser->isModifierCatVod());
            $user->setSauvegarderCatVod($sourceUser->isSauvegarderCatVod());
            $user->setSuppCatVod($sourceUser->isSuppCatVod());

            $user->setAjoutServiceEnChambre($sourceUser->isAjoutServiceEnChambre());
            $user->setModifierServiceEnChambre($sourceUser->isModifierServiceEnChambre());
            $user->setSauvegarderServiceEnChambre($sourceUser->isSauvegarderServiceEnChambre());
            $user->setSuppServiceEnChambre($sourceUser->isSuppServiceEnChambre());
            $user->setResultatQs($sourceUser->isResultatQs());

            $user->setSauvegarderChartPatient($sourceUser->isSauvegarderChartPatient());
            $user->setSauvgarderServicePayant($sourceUser->isSauvgarderServicePayant());
            $user->setCocherMeteo($sourceUser->isCocherMeteo());
            $user->setCocherLogo($sourceUser->isCocherLogo());

            $user->setModifierRmobile($sourceUser->isModifierRmobile());
            $user->setAjouteServiceEtablissement($sourceUser->isAjouteServiceEtablissement());
            $user->setCheckSupport($sourceUser->isCheckSupport());


            $user->setImportRadio($sourceUser->isImportRadio());
            $user->setExportRadio($sourceUser->isExportRadio());
            $user->setImportTV($sourceUser->isImportTV());
            $user->setExportTv($sourceUser->isExportTv());
           

        }
        $existingEmailUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $existingUsernameUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
    
    if ($existingEmailUser !== null) {
        $bugs['email'] = 'Cet email est déjà utilisé.';
    }
    if ($existingUsernameUser !== null) {
        $bugs['username'] = 'Cet identifiant est déjà utilisé.';
    }

    // Si des erreurs existent, afficher le formulaire avec les valeurs saisies et les messages d'erreur
    if (!empty(array_filter($bugs))) {
        return $this->render('security/login.html.twig', [
            'genre' => $genre,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'nom_etablissement' => $nom_etablissement,
            'adresse' => $adresse,
            'code' => $code,
            'ville' => $ville,
            'username' => $username,
            'password' => $password,
            'pays' => $pays,
            'bugs' => $bugs,
            'supports' =>$supports,
            'error' => "",
            'last_username' => "",
        ]);
    }

        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setEtablissement($etablissement);
        // Sauvegarder tous dans la base de données
        $this->importPreFilledData($entityManager, $etablissement);
        $entityManager->persist($user);
        $entityManager->persist($etablissement);
        $entityManager->flush();
    
        // Importation des données pré-remplies

         // Ajouter un message de succès
        $this->addFlash('success', 'L\'établissement a été créé avec succès.');
    
        return $this->redirectToRoute('app_login');
    }
    
    private function importPreFilledData(EntityManagerInterface $entityManager, Etablissement $etablissement)
    {
        // Trouver le service général pour la source
        $serviceGeneralSource = $entityManager->getRepository(ServiceEtablissement::class)
            ->findOneBy(['nom' => 'Géneral', 'etablissement' => 27268]);
    
        if ($serviceGeneralSource) {
            // Trouver ou créer le service général pour le nouvel établissement
            $serviceGeneralTarget = $entityManager->getRepository(ServiceEtablissement::class)
                ->findOneBy(['nom' => 'Géneral', 'etablissement' => $etablissement]);
    
            if (!$serviceGeneralTarget) {
                $serviceGeneralTarget = new ServiceEtablissement();
                $serviceGeneralTarget->setId(mt_rand(1, 9999));
                $serviceGeneralTarget->setNom('Géneral');
                $serviceGeneralTarget->setEtablissement($etablissement);
                $entityManager->persist($serviceGeneralTarget);
            }
    
            // Trouver les questionnaires pour le service général source
            $questionnaireRepository = $entityManager->getRepository(Questionnaire::class);
            $sourceQuestionnaires = $questionnaireRepository->findBy(['service' => $serviceGeneralSource]);
    
            foreach ($sourceQuestionnaires as $sourceQuestionnaire) {
                // Créer un nouveau questionnaire pour le nouvel établissement
                $newQuestionnaire = clone $sourceQuestionnaire;
                $newQuestionnaire->setService($serviceGeneralTarget);
                $newQuestionnaire->setEtablissement($etablissement); // Associer le questionnaire au nouvel établissement
                $entityManager->persist($newQuestionnaire);
            }
        }
        // Importer les données des autres tables

        $tables = [
            CategorieRadio::class,
            CategorieLivreAudio::class,
            CategorieVod::class,
            Television::class,
            Categories::class,
            Jeux::class,
            Application::class,
            ConfigApp::class,
            Configmobile::class,
            TypeServiceEnChambre::class,
        ];
    
        foreach ($tables as $entityClass) {
            $repository = $entityManager->getRepository($entityClass);
            $sourceItems = $repository->findBy(['etablissement' => 27268]);
            foreach ($sourceItems as $sourceItem) {
                $newItem = clone $sourceItem;
                $newItem->setEtablissement($etablissement);
                if($newItem instanceof CategorieRadio || $newItem instanceof CategorieLivreAudio || $newItem instanceof CategorieVod || $newItem instanceof Categories)
                    $newItem->setId(mt_rand(1, 9999));
                $entityManager->persist($newItem);
                if ($sourceItem instanceof CategorieRadio) {
                    $repositoryRadio = $entityManager->getRepository(Radio::class);
                    $sourceItemsRadio = $repositoryRadio->findBy(['etablissement' => 27268,'categorie' => $sourceItem]);
                    foreach ($sourceItemsRadio as $radio) {
                        $newRadio = clone $radio;
                        $newRadio->setEtablissement($etablissement);
                        $newRadio->setCategorie($newItem);
                        $entityManager->persist($newRadio);
                    }
                }
                if ($sourceItem instanceof CategorieLivreAudio) {
                    $repository = $entityManager->getRepository(LivreAudio::class);
                    $sourceItems = $repository->findBy(['etablissement' => 27268,'categorie' => $sourceItem]);
                    foreach($sourceItems as $item)
                    {
                        $newItem1 = clone $item;
                        $newItem1->setEtablissement($etablissement);
                        $newItem1->setCategorie($newItem);
                        $entityManager->persist($newItem1);
                    }
                }
                if ($sourceItem instanceof CategorieVod) {
                    $repository = $entityManager->getRepository(Vod::class);
                    $sourceItems = $repository->findBy(['etablissement' => 27268,'categorie' => $sourceItem]);
                    foreach($sourceItems as $item)
                    {
                        $newItem1 = clone $item;
                        $newItem1->setEtablissement($etablissement);
                        $newItem1->setCategorie($newItem);
                        $entityManager->persist($newItem1);
                    }

                }
                if ($sourceItem instanceof Categories) {
                    $repository = $entityManager->getRepository(Services::class);
                    $sourceItems = $repository->findBy(['etablissement' => 27268,'categories' => $sourceItem]);
                    foreach($sourceItems as $item)
                    {
                        $newItem1 = clone $item;
                        $newItem1->setEtablissement($etablissement);
                        $newItem1->setCategories($newItem);
                        $entityManager->persist($newItem1);
                    }

                }
        }
        }
        // Importer les champs spécifiques
        $sourceEtablissement = $entityManager->getRepository(Etablissement::class)->find(27268);
        if ($sourceEtablissement) {
            $etablissement->setLogo($sourceEtablissement->getLogo());
            $etablissement->setLogoactive($sourceEtablissement->getLogoactive());
            $etablissement->setMeteoactive($sourceEtablissement->getMeteoactive());
            $etablissement->setBackground($sourceEtablissement->getBackground());
            $etablissement->setMsgbienvenu($sourceEtablissement->getMsgbienvenu());
            $etablissement->setMsgap($sourceEtablissement->getMsgap());
            $etablissement->setType($sourceEtablissement->getType());
            $etablissement->setLicence($sourceEtablissement->getLicence());
            $etablissement->setAccessTvInCheckout($sourceEtablissement->getAccessTvInCheckout());

            $entityManager->persist($etablissement);
        }
        $entityManager->flush();
    }

    #[Route('/profil', name: 'app_profil')]
      public function index( EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $etablissement = $user->getEtablissement();
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'appConfig' => $appConfig
        ]);
    }

    #[Route('/delateEtablissement/{id}', name:'delateEtablissement')]
    public function DelateEtablissement(int $id, EntityManagerInterface $entityManager)
    {
        $etablissement = $entityManager->getRepository(Etablissement::class)->find($id);
    
        if (!$etablissement) {
            throw $this->createNotFoundException('Etablissement non trouvé');
        }
    
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
    
        foreach ($tables as $entityClass) {
            $repository = $entityManager->getRepository($entityClass);
            $sourceItems = $repository->findBy(['etablissement' => $etablissement]);
            foreach ($sourceItems as $sourceItem) {
                $entityManager->remove($sourceItem);
            }
        }
        // Suppression de l'établissement après que toutes les dépendances soient supprimées
        $entityManager->remove($etablissement);
        
        // Flush après suppression de toutes les entités
        $entityManager->flush();
    
        return $this->redirectToRoute('app_super_admin');
    }
    
    #[Route("/modifierProfil/{id}", name: "modifierProfil")]
    public function modifierProfil(int $id, 
    EntityManagerInterface $entityManager, 
    UserPasswordHasherInterface $passwordHasher,
    TokenGeneratorInterface $tokenGenerator,
    ParameterBagInterface $params,
    Request $request): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        
        // Récupération des données du formulaire
        $username = $request->get('username');
        $email = $request->get('email');
        $password = $request->get('password');
        $newPassword = $request->get('newPassword');
        $confirmPassword = $request->get('confirmPassword');
        // Modification des données de l'utilisateur
        $user->setUsername($username);
        $user->setEmail($email);

        // Vérification et gestion du changement de mot de passe

        if ($password && $newPassword && $confirmPassword) {
            if(!password_verify($newPassword, $user->getPassword()))
            {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->flush();    
                $Admin = $entityManager->getRepository(User::class)->findOneBy(['email' =>$user->getEmailAdmin()]);
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
                    $mail->addAddress($user->getEmailAdmin());
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'L\'utilisateur ' . $user->getUsername() . '  change son mot de passe';
                    $bodyContent = $this->renderView('email/change_password.html.twig', [
                                'user' => $user,
                                'admin' => $Admin,
                                'NewPassword' => $newPassword,
                    ]);
                    $mail->isHTML(true); 
                    // Corps du message
                    $mail->Body= $bodyContent;
            
                    // Envoi de l'email
                    $mail->send();

            }
            if ($passwordHasher->isPasswordValid($user, $password)) {
                if ($newPassword === $confirmPassword) {
                        $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
                        $user->setPassword($encodedPassword);
                } else {
                        $this-> addFlash('error', 'Le nouveau mot de passe et la confirmation du mot de passe ne correspondent pas.');
                        return $this->redirectToRoute('app_profil', ['id' => $user->getId()]);
                    }
             } else {
                       $this->addFlash('error', 'Mot de passe invalide.');
                       return $this->redirectToRoute('app_profil', ['id' => $user->getId()]);
            }
        }
            
        // Enregistrement des modifications dans la base de données
        $entityManager->flush();

        // Redirection vers la page de profil
        $this->addFlash('changerPassword', 'Votre profil a été modifier avec succes !');
        return $this->redirectToRoute('app_home');
}

// Fonction permettant d'envoyer un e-mail contenant le lien de réinitialisation du mot de passe.
#[Route('/app_forgot_password_request/{username}', name:'app_forgot_password_request')]
    public function forgottenPassword(EntityManagerInterface $entityManager,string $username)
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $user->getEtablissement()]);
        if (!$user) {
            $this->addFlash('danger', 'Aucun utilisateur n\'a été identifié avec ce nom.');
            return $this->redirectToRoute('app_login');
        }        
    //  Avant de procéder à l'envoi d'un e-mail de réinitialisation,
    //  nous allons contrôler le nombre de courriels que l'utilisateur a envoyés ainsi que le moment du dernier envoi.
        $lastDate = $user->getdernierTemp();
        if($lastDate)
            $lastDate->modify('+1 day');
        $dateNow = new DateTime();
          
        if($user->gettentativeOblierMdp() < 3)
        {     
            $parts = explode("@", $user->getEmailAdmin()); 
            $name = $parts[0];
            $domain = $parts[1];
            if (strlen($name) > 2) {
                $maskedName = substr($name, 0, 2) . str_repeat('*', strlen($name) - 4) . substr($name, -2)."@".$domain;
            }
            else {
                $maskedName = substr($name, 0, 1) . '*'.$domain;
            }
            return $this->render('/security/forgetPassword.html.twig',["username" => $username,"emailAdmin"=>$maskedName,"email"=>$user->getEmail(),'appConfig'=>$appConfig]);
        }   
        else{
            if($dateNow >= $lastDate)
            {
                $user->settentativeOblierMdp(0);
                $dateNow = new DateTime();
                $user->setdernierTemp($dateNow);
                $entityManager->flush();
                $parts = explode("@", $user->getEmailAdmin()); 
                $name = $parts[0];
                $domain = $parts[1];
                if (strlen($name) > 2) {
                    $maskedName = substr($name, 0, 2) . str_repeat('*', strlen($name) - 4) . substr($name, -2)."@".$domain;
                }
                else {
                    $maskedName = substr($name, 0, 1) . '*'.$domain;
                }
                return $this->render('/security/forgetPassword.html.twig',["username" => $username,"emailAdmin"=>$maskedName,"email"=>$user->getEmail(),'appConfig'=>$appConfig]);
            }
            if($dateNow < $lastDate)
            {        
            $this->addFlash('danger','Vous avez épuisé le nombre de tentatives autorisées. Essayez de nouveau dans 24 heures.');
            return $this->redirectToRoute('app_login');      
            }
        }
}


#[Route('/EnvoyerEmail', name: 'EnvoyerEmail')]
    public function EnvoyerEmail(
        Request $request,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        ParameterBagInterface $params

        // MailerInterface $mailer
    ): Response {
       
        // Récupération de l'utilisateur par email
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $request->get('email')]);
        if (!$user) {
            $this->addFlash('danger', 'Aucun utilisateur trouvé avec cet email.');
            return $this->redirectToRoute('app_forgot_password_request',['username' => $user->getUsername()]);
        }
            // Génération du token de réinitialisatio
            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);
            // Lorsqu'un utilisateur expédie un courrier électronique,
            // le nombre d'envois est enregistré dans la base de données avec l'heure correspondante.
            $user->settentativeOblierMdp($user->gettentativeOblierMdp() + 1);
            $dateNow = new DateTime();
            $user->setdernierTemp($dateNow);
            $entityManager->flush(); 

            // Génération du lien de réinitialisation
            $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            // Création et envoi  de l'e-mail à l'administrateur pour réinitialiser le mot de passe
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
                $mail->addAddress($user->getEmailAdmin());
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Demande de réinitialisation du mot de passe';
                $bodyContent = $this->renderView('email/reset_password.html.twig', [
                'user' => $user,
                'url' => $url,
            ]);
            $mail->isHTML(true); 
            // Corps du message
            $mail->Body    = $bodyContent;
    
            // Envoi de l'email
            $mail->send();
            $this->addFlash('success', 'L\'email a été envoyé avec succès à l\'administrateur(' . $user->getEmailAdmin() . ').');
            return $this->redirectToRoute('app_login');    
    }
    // Fonction pour réinitialiser le mot de passe et enregistrer la nouvelle valeur dans la base de données.

    #[Route('/reset_pass/{token}', name:'reset_pass')]
    public function reset_pass(string $token,Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher)
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);
        if(!$user)
        {
            $this->addFlash('danger', 'Le lien de réinitialisation du mot de passe a expiré ou a déjà été utilisé. Si vous avez oublié votre mot de passe, veuillez cliquer sur « oublier le mot de passe » ci-dessous.');
            return $this->redirectToRoute('app_login');
        }
        if($request->get('password') && $request->get('confirmerPassword') )
        {
            $hashedPassword = $passwordHasher->hashPassword($user, $request->get('password'));
            $user->setPassword($hashedPassword);
            $user->setResetToken("");
            $entityManager->flush();
            $this->addFlash('success', 'Le mot de passe a été créé avec succès.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('/security/ResitPassword.html.twig',['token'=>$token]);
    }  
}

?>