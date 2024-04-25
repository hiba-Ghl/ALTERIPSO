<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\CategorieLivreaudio;
use Doctrine\DBAL\Exception\IntegrityConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException; // Importez également cette classe si nécessaire



class CategorieLivreaudioController extends AbstractController
{
    #[Route('/categorielivreaudio/supprimer/{id}', name: 'app_supprimer_categorielivreaudio')]
    public function supprimercategorielivreaudio(EntityManagerInterface $entityManager, int $id): Response
    {
       
        try {
            $CategorieLivreaudio = $entityManager->getRepository(CategorieLivreaudio::class)->find($id);

        if (!$CategorieLivreaudio) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $entityManager->remove($CategorieLivreaudio);
        $entityManager->flush();

        return $this->redirectToRoute('app_categorie_livreaudio');
        } 
        catch (IntegrityConstraintViolationException $e) {
            // Gérer l'exception ici
            $errorMessage = "Erreur : Impossible de supprimer ou de mettre à jour une ligne parente en raison d'une contrainte de clé étrangère.";
            return $this->redirectToRoute('app_categorie_livreaudio');
        } catch (\PDOException $e) {
            // Gérer l'exception parente ici (PDOException)
            $errorMessage = $e->getMessage(); // Obtenez le message d'erreur PDO
            // Faites quelque chose avec l'erreur, par exemple, journalisez-la
            return $this->redirectToRoute('app_categorie_livreaudio');
        }
    }
    



    
    #[Route('/categorielivreaudio', name: 'app_categorie_livreaudio')]
    public function index(): Response
    {
        
    
        $catlivreaudios = $this->getUser()->getEtablissement()->getCategorieLivreaudios();
        //var_dump($catlivreaudios);die();

        return $this->render('categorie_livreaudio/index.html.twig', [
            'catlivreaudios' => $catlivreaudios,
        ]);
    }

 

    #[Route('/categorielivreaudio/ajouter', name: 'app_ajouter_categorielivreaudio')]
    public function ajoutercategorielivreaudio(EntityManagerInterface $entityManager): Response
    {
            $user = $this->getUser();
            $etablissement = $this->getUser()->getEtablissement();
        
            $request = Request::createFromGlobals();

            $valider = $request->get("valider");
            $nom = $request->get("nom");
            $active = $request->get("active");
            $position = $request->get("position");
        

        $file1 = $request->files->get('logo');
        
            if(isset($valider)){
                $fileName = 'images/no_image.png';
        // Vérifiez si les fichiers ont été téléchargés
        if ($file1 ) {
            // Traitez les fichiers comme vous le souhaitez
            $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
            

            // Déplacez les fichiers téléchargés vers le dossier de destination
            $file1->move($this->getParameter('categorie_livreaudio_directory'), $fileName1);
            

            // Répondre avec un message de succès ou rediriger vers une autre page

            //  return new Response('Fichiers téléchargés avec succès !');
            $fileName = 'images/categorie_livreaudio/' . $fileName1;
        }
        
        

            $FR = $nom;
            $EN = $request->get("EN");
            $ES = $request->get("ES");
            $PT = $request->get("PT");
            $IT = $request->get("IT");
            $RU = $request->get("RU");
            $DE = $request->get("DE");
            $ZH = $request->get("ZH");
            $AR = $request->get("AR");
        
            $categories = new CategorieLivreaudio();
            
            $categories->setEtablissement($etablissement);
            $categories->setNom($nom);
            $categories->setActive($active);
            $categories->setPosition($position);
            $categories->setLogo($fileName);
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
            return $this->redirectToRoute('app_categorie_livreaudio');
        }
      return $this->render('categorie_livreaudio/ajouter.html.twig');
        
    }

    #[Route('/categorielivreaudio/modifier/{id}', name: 'app_modifier_categorielivreaudio')]
    public function modifiercategorielivreaudio(EntityManagerInterface $entityManager, int $id): Response
        {
            $user = $this->getUser();
            $etablissement = $this->getUser()->getEtablissement();
            $categories = $entityManager->getRepository(CategorieLivreaudio::class)->findById($id)[0];
            $request = Request::createFromGlobals();

            $valider = $request->get("valider");
            $nom = $request->get("nom");
            $active = $request->get("active");
            $position = $request->get("position");
        

        $file1 = $request->files->get('logo');
        
            if(isset($valider)){
                $fileName = $categories->getLogo();
        // Vérifiez si les fichiers ont été téléchargés
        if ($file1 ) {
            // Traitez les fichiers comme vous le souhaitez
            $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
            

            // Déplacez les fichiers téléchargés vers le dossier de destination
            $file1->move($this->getParameter('categorie_livreaudio_directory'), $fileName1);
            

            // Répondre avec un message de succès ou rediriger vers une autre page
            //  return new Response('Fichiers téléchargés avec succès !');
            $fileName = 'images/categorie_livreaudio/' . $fileName1;
        }
        
        

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
            $categories->setActive($active);
            $categories->setPosition($position);
            $categories->setLogo($fileName);
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
            return $this->redirectToRoute('app_categorie_livreaudio');
        }
      
      return $this->render('categorie_livreaudio/modifier.html.twig', [
        'categorielivreaudio' => $categories]);

        
    }

   

}
