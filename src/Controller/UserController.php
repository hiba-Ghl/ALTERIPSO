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

class UserController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

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
}


?>