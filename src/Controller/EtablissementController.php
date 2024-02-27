<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etablissement;

class EtablissementController extends AbstractController
{
    #[Route('/etablissement', name: 'app_etablissement')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $etablissement = $this->getUser()->getEtablissement();
        
        //var_dump($etablissement);die();
        
        return $this->render('etablissement/index.html.twig', [
            'etablissement' => $etablissement,
        ]);
    }


    #[Route('/modifierbackground', name: 'modifierbackground')]
    public function modifierbackground(EntityManagerInterface $entityManager): Response
    {
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $etablissement = $this->getUser()->getEtablissement();
        
        
       
       $file = $request->files->get('backgound');
       //var_dump($file);die();

       // Vérifiez si les fichiers ont été téléchargés
       if ($file) {
           // Traitez les fichiers comme vous le souhaitez
           $fileName = md5(uniqid()) . '.' . $file->guessExtension();

           // Déplacez les fichiers téléchargés vers le dossier de destination
           $file->move($this->getParameter('etablissement_directory'), $fileName);
           

           // Répondre avec un message de succès ou rediriger vers une autre page
         //  return new Response('Fichiers téléchargés avec succès !');
         $fileName = 'etablissement/' . $fileName;
         

         
        $etablissement->setBackground($fileName);
       }

       $entityManager->persist($etablissement);
       $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route("/modifieretablissement/{id}", name: "modifieretablissement")]
    public function updateEtablissement( int $id,EntityManagerInterface $entityManager, Request $request): Response
    {
      $user = $this->getUser();
      $etablissement = $this->getUser()->getEtablissement();

      $etablissement = $entityManager->getRepository(etablissement::class)->find($id);
  
      // Récupérez le formulaire Twig pour le modifier
          $nometablissement = $request->get('nom_etablissement');
          $prenom = $request->get('prenom');
          $nom = $request->get('nom');
          $ville = $request->get('ville');
          $pays = $request->get('pays');
          $description = $request->get('description');
          //var_dump($description);die();
          $licence = $request->get('licence');
          $adresse = $request->get('adresse');
          $genre = $request->get('genre');
          $msgap = $request->get('msgap');
          $accessTvInCheckout = $request->get('accessTvInCheckout');
          $logoactive = $request->get('logoactive');
          $msgbienvenu = $request->get('msgbienvenu');
          
         $etablissement->setNometablissement( $nometablissement) ;    
         $etablissement->setNom($nom) ;
         $etablissement->setPrenom( $prenom) ;
         $etablissement->setGenre( $genre) ;
         $etablissement->setDescription( $description) ;
         $etablissement->setVille( $ville) ;
         $etablissement->setPays( $pays) ;
         $etablissement->setAdresse( $adresse) ;
         $etablissement->setLicence( $licence) ;
         $etablissement->setMsgap( $msgap ) ;
         $etablissement->setLogoactive( $logoactive ) ;
         $etablissement->setAccessTvInCheckout( $accessTvInCheckout ) ;
         $etablissement->setMsgbienvenu( $msgbienvenu ) ;

        //logo

        $file = $request->files->get('logo');
            
            // Vérifiez si les fichiers ont été téléchargés
            if ($file) {
    
                // Vérifiez si l'extension du fichier 
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $extension = $file->guessExtension();
            
                if (in_array(strtolower($extension), $allowedExtensions)) {
                    // Traitement des fichiers
                    $logo = md5(uniqid()) . '.' . $extension;
            
                    // Déplacez les fichiers téléchargés vers le dossier "etablissement"
                    $file->move($this->getParameter('etablissement_directory'), $logo);
            
                    // Chemin du fichier pour enregistrer dans la base de données
                    $logoPath = 'etablissement/' . $logo;
            
                    $etablissement->setLogo($logoPath);
                } else {
                   
                 return new Response('Seuls les fichiers JPG et PNG sont autorisés.');
                    
                }
            }
          // Enregistrez les modifications dans la base de donnée

          $entityManager->persist($etablissement);
          $entityManager->flush();
         


        // Redirigez l'utilisateur vers une page de confirmation 
        return $this->redirectToRoute('app_etablissement', [
            'id' => $etablissement->getId()
        ]);
            }

}


