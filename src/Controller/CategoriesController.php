<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etablissement;
use App\Entity\Categories;
class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        return $this->render('categories/ajouter.html.twig', [
            'controller_name' => 'CategoriesController',
        ]);
    }

    #[Route('/categories/{id}', name: 'categories')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $categories = $entityManager->getRepository(categories::class)->find($id);

        if (!$categories) {
            throw $this->createNotFoundException(
                'Aucune categorie trouvé pour id '.$id
            );
        }

       // return new Response('Check out this great product: '.$product->getName());

        // or render a template
        // in the template, print things with {{ product.name }}
         return $this->render('categories/modifier.html.twig', ['categorie' => $categories]);
    }

    #[Route('/ajoutercategories', name: 'ajoutercategories')]
    public function ajoutercategories(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
    
        $request = Request::createFromGlobals();
        $nom = $request->get("nom");
        $titre = $request->get("titre");
        $active = $request->get("active");
        $position = $request->get("position");
        $html = $request->get("html");
        $categories = new Categories();
    
        $file1 = $request->files->get('logo');
        $file2 = $request->files->get('background');
    
        // Vérifiez si les fichiers ont été téléchargés
        if ($file1 && $file2) {
            try {
                $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
                $fileName2 = md5(uniqid()) . '.' . $file2->guessExtension();
    
                // Déplacez les fichiers téléchargés vers le dossier de destination
                $file1->move($this->getParameter('categories_directory'), $fileName1);
                $file2->move($this->getParameter('categories_directory'), $fileName2);
    
                $fileName = 'images/categories/' . $fileName1;
                $fileNamebackground = 'images/categories/' . $fileName2;
    
                $categories->setLogo($fileName);
                $categories->setBackground($fileNamebackground);
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
    
        $entityManager->persist($categories);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_home');
    }
    

    #[Route('/categories/modifier/{id}', name: 'modifiercategories')]
    public function modifiercategories(EntityManagerInterface $entityManager, int $id): Response
    {
        $categories = $entityManager->getRepository(categories::class)->find($id);

        if (!$categories) {
            throw $this->createNotFoundException(
                'Aucune categorie trouvé pour id '.$id
            );
        }
         
        $request = Request::createFromGlobals();

        $nom = $request->get("nom");
        $titre = $request->get("titre");
        $active = $request->get("active");
        $position = $request->get("position");
        $html = $request->get("html");
    

       $file1 = $request->files->get('logo');
       $file2 = $request->files->get('background');

       // Vérifiez si les fichiers ont été téléchargés
       if ($file1 && $file2) {
           // Traitez les fichiers comme vous le souhaitez
           $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
           $fileName2 = md5(uniqid()) . '.' . $file2->guessExtension();

           // Déplacez les fichiers téléchargés vers le dossier de destination
           $file1->move($this->getParameter('categories_directory'), $fileName1);
           $file2->move($this->getParameter('categories_directory'), $fileName2);

           // Répondre avec un message de succès ou rediriger vers une autre page
         //  return new Response('Fichiers téléchargés avec succès !');
         $fileName = 'images/categories/' . $fileName1;
         $fileNamebackground = 'images/categories/' . $fileName2;

         $categories->setLogo($fileName);
        $categories->setBackground($fileNamebackground);
       }
      

     //  var_dump($fileName1.'    '.$fileName2);
       // var_dump($request);
       // die();
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

        $entityManager->persist($categories);
        $entityManager->flush();
        

        return $this->redirectToRoute('categories', [
            'id' => $categories->getId()
        ]);
    }


}
