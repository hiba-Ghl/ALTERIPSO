<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etablissement;
use App\Entity\Categories;
use App\Entity\ConfigApp;
use App\Entity\Services;

class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        return $this->render('categories/ajouter.html.twig', [
            'controller_name' => 'CategoriesController',
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/categories/{id}', name: 'categories')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $categories = $entityManager->getRepository(categories::class)->find($id);
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        if ($appConfig) {
            $configArray = ['Status'=>$appConfig->getStatusServeur()];
        } 
        else{
            $configArray = ['Status'=>'online'];
        }
        $configJson = json_encode($configArray);
        if (!$categories) {
            throw $this->createNotFoundException(
                'Aucune categorie trouvé pour id ' . $id
            );
        }

        // return new Response('Check out this great product: '.$product->getName());

        // or render a template
        // in the template, print things with {{ product.name }}
        return $this->render('categories/modifier.html.twig',
         ['categorie' => $categories,
         'configApp'=>$configJson,
        'appConfig' =>$appConfig,
        'user' => $this->getUser()
    ]);
    }

    #[Route('/ajoutercategories', name: 'ajoutercategories')]
    public function ajoutercategories(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $request = Request::createFromGlobals();
        $nom = $request->get("nom");
        $titre = $request->get("titre");
        $active = $request->get("active");
        $position = $request->get("position");
        $html = $request->get("html");
        $typemenu = $request->get("typemenu");
        $categories = new Categories();

        $file1 = $request->files->get('logo');
        $file2 = $request->files->get('background');



        // Vérifiez si au moins un des fichiers a été téléchargé
        if ($file1 || $file2) {
            try {
                if ($file1) {
                    // Générer un nom unique pour le fichier logo
                    $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();

                    // Déplacer le fichier téléchargé vers le dossier de destination
                    try {
                        $file1->move($this->getParameter('categories_directory'), $fileName1);
                        $fileName = 'images/categories/' . $fileName1;

                        // Mettre à jour le logo uniquement si un fichier a été téléchargé
                        $categories->setLogo($fileName);
                    } catch (\Exception $e) {
                        $this->addFlash('warning','Erreur lors du déplacement du fichier logo : ' . $e->getMessage());
                        die();
                    }
                }

                if ($file2) {
                    // Générer un nom unique pour le fichier background
                    $fileName2 = md5(uniqid()) . '.' . $file2->guessExtension();

                    // Déplacer le fichier téléchargé vers le dossier de destination
                    try {
                        $file2->move($this->getParameter('categories_directory'), $fileName2);
                        $fileNamebackground = 'images/categories/' . $fileName2;

                        // Mettre à jour le background uniquement si un fichier a été téléchargé
                        $categories->setBackground($fileNamebackground);
                    } catch (\Exception $e) {
                        $this->addFlash('warning','Erreur lors du déplacement du fichier background : ' . $e->getMessage());
                        die();
                    }
                }
            } catch (\Exception $e) {
                // Gérer les erreurs de téléchargement de fichiers
                return new Response('Erreur lors du téléchargement des fichiers : ' . $e->getMessage());
            }
        }


        $package = $request->get("package");
        $FR = $nom;
        $EN = $request->get("EN");
        $ES = $request->get("ES");
        $PT = $request->get("PT");
        $IT = $request->get("IT");
        $RU = $request->get("RU");
        $DE = $request->get("DE");
        $ZH = $request->get("ZH");
        $AR = $request->get("AR");

        $categories->setEtablissement($etablissement);
        $categories->setNom($nom);
        $categories->setTitre($titre);
        $categories->setActive($active);
        $categories->setPosition($position);
        $categories->setHtml($html);
        $categories->setTypemenu($typemenu);
        $categories->setPackage($package);
        $categories->setFR($FR);
        $categories->setEN($EN);
        $categories->setES($ES);
        $categories->setPT($PT);
        $categories->setIT($IT);
        $categories->setRU($RU);
        $categories->setDE($DE);
        $categories->setZH($ZH);
        $categories->setAR($AR);
        $categories->setId(mt_rand(1, 99999));

        $entityManager->persist($categories);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }


    #[Route('/categories/modifier/{id}', name: 'modifiercategories')]
    public function modifiercategories(EntityManagerInterface $entityManager, int $id): Response
    {
        $categories = $entityManager->getRepository(Categories::class)->find($id);
    
        if (!$categories) {
            throw $this->createNotFoundException(
                'Aucune catégorie trouvée pour id ' . $id
            );
        }
    
        $request = Request::createFromGlobals();
    
        $nom = $request->get("nom");
        $titre = $request->get("titre");
        $active = $request->get("active");
        $position = $request->get("position");
        $html = $request->get("html");
        $typemenu = $request->get("typemenu");
    
        $file1 = $request->files->get('logo');
        $file2 = $request->files->get('background');
    
        // Vérifiez si au moins un des fichiers a été téléchargé
        if ($file1 || $file2) {
            try {
                if ($file1) {
                    // Générer un nom unique pour le fichier logo
                    $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
                    
                    // Déplacer le fichier téléchargé vers le dossier de destination
                    $file1->move($this->getParameter('categories_directory'), $fileName1);
                    $fileName = 'images/categories/' . $fileName1;
                    
                    // Mettre à jour le logo dans la base de données
                    $categories->setLogo($fileName);
                }
    
                if ($file2) {
                    // Générer un nom unique pour le fichier background
                    $fileName2 = md5(uniqid()) . '.' . $file2->guessExtension();
                    
                    // Déplacer le fichier téléchargé vers le dossier de destination
                    $file2->move($this->getParameter('categories_directory'), $fileName2);
                    $fileNamebackground = 'images/categories/' . $fileName2;
                    
                    // Mettre à jour le background dans la base de données
                    $categories->setBackground($fileNamebackground);
                }
            } catch (\Exception $e) {
                return new Response('Erreur lors du téléchargement des fichiers : ' . $e->getMessage());
            }
        }
    
        // Mise à jour des autres champs
        $package = $request->get("package");
        $FR = $nom;
        $EN = $request->get("EN");
        $ES = $request->get("ES");
        $PT = $request->get("PT");
        $IT = $request->get("IT");
        $RU = $request->get("RU");
        $DE = $request->get("DE");
        $ZH = $request->get("ZH");
        $AR = $request->get("AR");
    
        $categories->setNom($nom);
        $categories->setTitre($titre);
        $categories->setActive($active);
        $categories->setPosition($position);
        $categories->setHtml($html);
        $categories->setTypemenu($typemenu);
        $categories->setPackage($package);
        $categories->setFR($FR);
        $categories->setEN($EN);
        $categories->setES($ES);
        $categories->setPT($PT);
        $categories->setIT($IT);
        $categories->setRU($RU);
        $categories->setDE($DE);
        $categories->setZH($ZH);
        $categories->setAR($AR);
    
        // Persistance et mise à jour en base de données
        $entityManager->persist($categories);
        $entityManager->flush();
    
        // Redirection après la mise à jour
        return $this->redirectToRoute('home');

        // return $this->redirectToRoute('categories', [
        //     'id' => $categories->getId(),
        //     'user' => $this->getUser(),
        // ]);
    }
    


    #[Route('/categories/supprimer/{id}', name: 'app_supprimer_categories')]
    public function supprimercategories(EntityManagerInterface $entityManager, int $id): Response
    {        
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $categorie = $entityManager->getRepository(Categories::class)->find($id);
        if (!$categorie) {
            throw $this->createNotFoundException(
                'No room found for id ' . $id
            );
        }
        $service = $entityManager->getRepository(Services::class)->findBy(['categories' => $categorie]);
        
        if (count($service) > 0) {
            $this->addFlash('error', 'Suppression Non Autorisée: La suppression de cette catégorie n\'est pas possible car elle est actuellement associée à des services dans notre système.');
            return $this->redirectToRoute('home');
        }
        $entityManager->remove($categorie);
        $entityManager->flush();

            return $this->redirectToRoute('home');
    } 
    
}
