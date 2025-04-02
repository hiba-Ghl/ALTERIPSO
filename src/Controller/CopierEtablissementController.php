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

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CopierEtablissementController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }    




    #[Route('/app_Confirme_Copier/{id}', name: 'app_Confirme_Copier', methods: ['POST'])]
    public function app_Confirme_Copier(string $id, Request $request, UserPasswordHasherInterface $passwordHasher
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
         
                if ($password && $passwordHasher->isPasswordValid($user1, $password)) {
                    return new JsonResponse(['passwordValid' => true], Response::HTTP_OK);
                }
        
            else{    
                    return new JsonResponse([
                        'passwordValid' => false,
                        'temp' => $user1->getDerniertempExport(),
                        'tentative' => $user1->getTentativeExport(),
                    ], Response::HTTP_OK);     
                }
    }
    
    

#[Route('/copier/etablissement/{id}', name: 'app_copier_etablissement')]
public function import(int $id,Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
{
    $etablissement = $this->entityManager->getRepository(Etablissement::class)->findOneBy(["id" => $id ]);
    $this->importEtablissement($id,$request,$etablissement, $entityManager,$passwordHasher);
     try{
    }catch(\Exception $e){
        $this->addFlash('danger', 'L’établissement que vous tentez de copier existe déjà dans la base de données. Veuillez vérifier les informations ou utiliser un autre identifiant.');
        return $this->redirectToRoute('home');
    }
    $this->addFlash('changerPassword', 'Les données ont été copiées avec succès.');
    return $this->redirectToRoute('app_super_admin');
}

private function importEtablissement(int $id,Request $request,object $etablissement,EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): void
{


    $tables = [
        ServiceEnChambre::class,
        ResultatQuestionnaire::class,
        Questionnaire::class,
        LivreAudio::class,
        Vod::class,
        Radio::class,  
        CategorieRadio::class,
        CategorieLivreAudio::class,
        CategorieVod::class,
        Categories::class,
        Services::class,
        ServiceEtablissement::class,
        TypeServiceEnChambre::class,
        Support::class,
        Annonce::class,
        Application::class,
        Configmobile::class,
        Historiquegratuite::class,
        HistoriqueAnnonce::class,
        Jeux::class,
        ConfigApp::class,
        Chambre::class,
        Television::class,
        User::class,
    ];


    $newEtablissement = new Etablissement();
    $methods = get_class_methods($etablissement);
    
    foreach ($methods as $method) {
        if (strpos($method, 'get') === 0) { 
            $property = lcfirst(substr($method, 3));
            if($property == "id")
            {
                $newEtablissement->setId(mt_rand(10000, 99999));
            }
            else
            {
                $setter = 'set' . ucfirst($property);
                if (method_exists($newEtablissement, $setter)) {
                    $value = $etablissement->$method() ?? "";; 
                    $newEtablissement->$setter($value); 
                }
            }
        }
    }
    foreach ($tables as $entityClass) {
        $repository = $entityManager->getRepository($entityClass);
        $sourceItems = $repository->findBy(['etablissement' => $id]);
        foreach ($sourceItems as $sourceItem) {
            if ($sourceItem instanceof User && !in_array('ROLE_ADMIN', $sourceItem->getRoles(), true)) {
                continue;
            }
            $newItem = clone $sourceItem;
            $newItem->setEtablissement($newEtablissement);
            
            if($newItem instanceof ServiceEtablissement || $newItem instanceof CategorieRadio || $newItem instanceof CategorieLivreAudio || $newItem instanceof CategorieVod || $newItem instanceof Categories)
            
            {
                $newItem->setId(mt_rand(9999,99999));
                $entityManager->persist($newItem);
            }
            else if(!($newItem instanceof Radio) && !($newItem instanceof Questionnaire) && !($newItem instanceof Livreaudio) && !($newItem instanceof Vod) && !($newItem instanceof Services))
            {
                $entityManager->persist($newItem);
            }
            if($newItem instanceof User && in_array('ROLE_ADMIN', $newItem->getRoles(), true))
            {
                    $newItem->setUsername($request->request->get('nom'));
                    $newItem->setEmail($request->request->get('email'));
                    $password = $request->request->get('password1');
                    $hashedPassword = $passwordHasher->hashPassword($newItem, $password);
                    $newItem->setPassword($hashedPassword);
                    $entityManager->persist($newItem);
            }
            if ($sourceItem instanceof CategorieRadio) {
                $repositoryRadio = $entityManager->getRepository(Radio::class);
                $sourceItemsRadio = $repositoryRadio->findBy(['etablissement' => $id,'categorie' => $sourceItem]);
                foreach ($sourceItemsRadio as $radio) {
                    $newRadio = clone $radio;
                    $newRadio->setEtablissement($newEtablissement);
                    $newRadio->setCategorie($newItem);
                    $entityManager->persist($newRadio);
                }
            }
            if ($sourceItem instanceof CategorieLivreAudio) {
                $repository = $entityManager->getRepository(LivreAudio::class);
                $sourceItems = $repository->findBy(['etablissement' => $id,'categorie' => $sourceItem]);
                foreach($sourceItems as $item)
                {
                    $newItem1 = clone $item;
                    $newItem1->setEtablissement($newEtablissement);
                    $newItem1->setCategorie($newItem);
                    $entityManager->persist($newItem1);
                }
            }
            if ($sourceItem instanceof CategorieVod) {
                $repository = $entityManager->getRepository(Vod::class);
                $sourceItems = $repository->findBy(['etablissement' => $id,'categorie' => $sourceItem]);
                foreach($sourceItems as $item)
                {
                    $newItem1 = clone $item;
                    $newItem1->setEtablissement($newEtablissement);
                    $newItem1->setCategorie($newItem);
                    $entityManager->persist($newItem1);
                }

            }
            if ($sourceItem instanceof Categories) {
                $repository = $entityManager->getRepository(Services::class);
                $sourceItems = $repository->findBy(['etablissement' => $id,'categories' => $sourceItem]);
                foreach($sourceItems as $item)
                {
                    $newItem1 = clone $item;
                    $newItem1->setEtablissement($newEtablissement);
                    $newItem1->setCategories($newItem);
                    $entityManager->persist($newItem1);
                }

            }
            if ($sourceItem instanceof ServiceEtablissement) {
                $repository = $entityManager->getRepository(Questionnaire::class);
                $sourceItems = $repository->findBy(['etablissement' => $id,'service' => $sourceItem]);
                foreach($sourceItems as $item)
                {
                    $newItem1 = clone $item;
                    $newItem1->setEtablissement($newEtablissement);
                    $newItem1->setService($newItem);
                    $entityManager->persist($newItem1);
                }

            }
            if ($sourceItem instanceof ServiceEtablissement) {
                $repository = $entityManager->getRepository(Chambre::class);
                $sourceItems = $repository->findBy(['etablissement' => $id,'service' => $sourceItem]);
                foreach($sourceItems as $item)
                {
                    $newItem1 = clone $item;
                    $newItem1->setEtablissement($newEtablissement);
                    $newItem1->setService($newItem);
                    $entityManager->persist($newItem1);
                }

            }
    }
    
    $entityManager->flush();
}
}
  
}

