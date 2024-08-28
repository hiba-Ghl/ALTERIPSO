<?php
// src/Controller/UserController.php

namespace App\Controller;
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


class UserController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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
    
        $etablissement->setId($idetablissement);
        $etablissement->setGenre($genre);
        $etablissement->setNom($nom);
        $etablissement->setPrenom($prenom);
        $etablissement->setNomEtablissement($nom_etablissement);
        $etablissement->setAdresse($adresse);
        $etablissement->setCode($code);
        $etablissement->setVille($ville);
        $etablissement->setPays($pays);
    
   
        // Sauvegarder l'établissement dans la base de données
        $entityManager->persist($etablissement);
        $entityManager->flush();
    
        // Enregistrement de l'utilisateur
        $user = new User();
    
        $username = $request->get("username");
        $password = $request->get("password");
        $email = $request->get("email");
    
        $user->setUsername($username);
        $user->setEmail($email);
    
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
            'error' => "",
            'last_username' => "",
        ]);
    }
    
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setEtablissement($etablissement);
    
        $entityManager->persist($user);
        $entityManager->flush();
    
        // Importation des données pré-remplies
        $this->importPreFilledData($entityManager, $etablissement);

         // Ajouter un message de succès
        $this->addFlash('success', 'L\'établissement a été créé avec succès.');
    
        return $this->redirectToRoute('app_login');
    }
    
    private function importPreFilledData(EntityManagerInterface $entityManager, Etablissement $etablissement)
    {
        // Trouver le service général pour la source
        $serviceGeneralSource = $entityManager->getRepository(ServiceEtablissement::class)
            ->findOneBy(['nom' => 'Géneral', 'etablissement' => 87371]);
    
        if ($serviceGeneralSource) {
            // Trouver ou créer le service général pour le nouvel établissement
            $serviceGeneralTarget = $entityManager->getRepository(ServiceEtablissement::class)
                ->findOneBy(['nom' => 'Géneral', 'etablissement' => $etablissement]);
    
            if (!$serviceGeneralTarget) {
                $serviceGeneralTarget = new ServiceEtablissement();
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
            Radio::class,
            CategorieLivreAudio::class,
            LivreAudio::class,
            Television::class,
            Categories::class
        ];
    
        foreach ($tables as $entityClass) {
            $repository = $entityManager->getRepository($entityClass);
            $sourceItems = $repository->findBy(['etablissement' => 87371]);
    
            foreach ($sourceItems as $sourceItem) {
                // Créer un nouvel élément pour le nouvel établissement
                $newItem = clone $sourceItem;
                $newItem->setEtablissement($etablissement);
                $entityManager->persist($newItem);
            }
        }
    
        // Importer les champs spécifiques
        $sourceEtablissement = $entityManager->getRepository(Etablissement::class)->find(87371);
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
    

        

/*
    #[Route('/register', name: 'app_register')]
    public function register(EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    
    
    {
        

        $request = Request::createFromGlobals();

        $entityManager->flush();



        // enregistrement des infos etablissement : 
        $etablissement = new Etablissement();

        $idetablissement =  mt_rand(10000, 99999);
        $genre = $request->get("genre");
        $nom = $request->get("nom");
        $prenom = $request->get("prenom");
        $nom_etablissement = $request->get("nom_etablissement");
        $adresse = $request->get("adresse");
        $code = $request->get("code");
        $ville = $request->get("ville");
        $pays = $request->get("pays");
       
        $etablissement->setId($idetablissement);
        $etablissement->setGenre($genre);
        $etablissement->setNom($nom);
        $etablissement->setPrenom($prenom);
        $etablissement->setNomEtablissement($nom_etablissement);
        $etablissement->setAdresse($adresse);
        $etablissement->setCode($code);
        $etablissement->setVille($ville);
        $etablissement->setPays($pays);

        //var_dump($idetablissement);die();

         // Sauvegarder l'etablissement dans la base de données
         $entityManager->persist($etablissement);
         



        // Créer une instance de l'entité utilisateur
        $user = new User();

        $username = $request->get("username");
        $password = $request->get("password");
        $email = $request->get("email");


        // Vérifier si l'email est déjà utilisé
        $existingEmailUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Vérifier si l'username est déjà utilisé
        $existingUsernameUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        // Si un utilisateur avec cet email ou username existe déjà, afficher un message d'erreur
        if ($existingEmailUser !== null && $existingUsernameUser !== null) {
            $this->addFlash('error', 'Cet email et identifiant sont déjà utilisés.');
            return $this->redirectToRoute('app_login');
        } 
        elseif ($existingEmailUser !== null) {
            $this->addFlash('error', 'Cet email est déjà utilisé.');
            return $this->redirectToRoute('app_login');
        } 
        elseif ($existingUsernameUser !== null) {
            $this->addFlash('error', 'Cet identifiant est déjà utilisé.');
            return $this->redirectToRoute('app_login');
        }




        // Définir les propriétés de l'utilisateur
        $user->setUsername($username);
        $user->setEmail($email);
        $plaintextPassword=$password; // Vous devrez peut-être encoder le mot de passe
           // hash the password (based on the security.yaml config for the $user class)
           $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setEtablissement($etablissement);

        // Sauvegarder l'utilisateur dans la base de données
        $entityManager->persist($user);
        
        $entityManager->flush();
       // return new Response('Utilisateur créé avec succès!'.$user->getId());
        return $this->redirectToRoute('app_login'); 
        
    }
    */
    #[Route('/profil', name: 'app_profil')]
      public function index(): Response
    {
        $user = $this->getUser();
        
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route("/modifierProfil/{id}", name: "modifierProfil")]
    public function modifierProfil(int $id, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Request $request): Response
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
            if ($passwordHasher->isPasswordValid($user, $password)) {
                if ($newPassword === $confirmPassword) {
                        $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
                        $user->setPassword($encodedPassword);
                        $this->addFlash('success', 'Mot de passe valide, votre action a été réalisée avec succès !');
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
        $this->addFlash('success', 'Votre profil a été modifier avec succes !');
        return $this->redirectToRoute('app_profil');
    }
    
}


?>