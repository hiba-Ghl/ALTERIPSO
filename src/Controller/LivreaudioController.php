<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\CategorieLivreaudio;
use App\Entity\ConfigApp;
use App\Entity\Favoris;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


use App\Entity\Livreaudio;

class LivreaudioController extends AbstractController
{
    #[Route('/livreaudio', name: 'app_livreaudio')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
  
        $Acce = $this->getUser()->getLIVREAUDIO() && $configApp->getEnableLIVREAUDIO()=="1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page."); 
            return $this->redirectToRoute('home');
        }
        $repository = $entityManager->getRepository(Livreaudio::class);
        $livreaudio  = $repository->findBy(['etablissement' => $etablissement],['nom' => 'ASC']);
        $categorielivreaudio =  $entityManager->getRepository(CategorieLivreaudio::class)->findBy(['etablissement' => $etablissement],['nom' => 'ASC']);
        #huba favoris
        $categorieFavori = $entityManager->getRepository(Categories::class)->findOneBy([
            'etablissement' => $etablissement,
            'nom' => 'Livre audio'
        ]);
        if ($categorieFavori === null) {
            $categorieFavori = $entityManager->getRepository(Categories::class)->findOneBy([
                'etablissement' => $etablissement,
                'nom' => 'Livre audio'
            ]);
        }
        $favorisLivreaudio = $entityManager->getRepository(Favoris::class)->findBy([
            'Etablissement' => $etablissement,
        ]);

        $favorisLivreaudioIds = [];
        foreach ($favorisLivreaudio as $favori) {
            $isFavorite = false;
            if ($categorieFavori !== null && $favori->getCategorie() === $categorieFavori) {
                $isFavorite = true;
            }
            if ($favori->getNomCategorie() !== null && in_array(strtolower($favori->getNomCategorie()), ['livreaudio', 'livreaudio'], true)) {
                $isFavorite = true;
            }
            if ($isFavorite && $favori->getIdElement() !== null) {
                $favorisLivreaudioIds[] = $favori->getIdElement();
            }
        }
        // dd($livreaudio);
        return $this->render('livreaudio/index.html.twig', [
            'livreaudio' => $livreaudio , 'categorielivreaudio' => $categorielivreaudio,'appConfig' => $configApp,'user' => $this->getUser(),
            #huba favoris
            'favorisLivreaudioIds' => $favorisLivreaudioIds,
        ]);
    }

    #[Route('/ajouterlivreaudio', name: 'ajouter_livreaudio')]
    public function ajouterlivreaudio(EntityManagerInterface $entityManager): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $Acce = $this->getUser()->getAjoutLiveAudio() && $this->getUser()->getLIVREAUDIO() && $configApp->getEnableLIVREAUDIO()=="1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
        $request = Request::createFromGlobals();

        $nom = $request->get("nom");
        $ip = $request->get("ip");
        $active = $request->get("active");
        $categorieFavori = $entityManager->getRepository(Categories::class)->findOneBy([
            'etablissement' => $etablissement,
            'nom' => 'Livre audio'
        ]);
        if ($categorieFavori === null) {
            $categorieFavori = $entityManager->getRepository(Categories::class)->findOneBy([
                'etablissement' => $etablissement,
                'nom' => 'Livre audio'
            ]);
        }
        #hiba favoris
        $favoris = $request->get("favoris", '0');
        $catlivreaudio = $request->get("catlivreaudio");
        $categorielivreaudio =  $entityManager->getRepository(CategorieLivreaudio::class)->findById($catlivreaudio)[0];
    

       $file1 = $request->files->get('logo');
       

       // Vérifiez si les fichiers ont été téléchargés
       if ($file1) {
           // Traitez les fichiers comme vous le souhaitez
           $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
           

           // Déplacez les fichiers téléchargés vers le dossier de destination
           $file1->move($this->getParameter('livreaudio_directory'), $fileName1);
          

         $fileName = 'images/livreaudio/' . $fileName1;
       }
       else 
       $fileName = 'images/no_image.png';
       
       
       

       
        $livreaudio = new Livreaudio();
        
        $livreaudio->setEtablissement($etablissement);
        $livreaudio->setNom($nom);
        $livreaudio->setIp($ip);
       $livreaudio->setCategorie($categorielivreaudio);
       $livreaudio->setLogo($fileName);
        if (isset($active) and !empty($active)) 
           $livreaudio->setActive(1);
       else 
           $livreaudio->setActive(0);
       

        $entityManager->persist($livreaudio);
        $entityManager->flush();
        #hiba favorie

        if ((string) $favoris === '1') {
            if ($this->canPersistNewFavorite($entityManager, $etablissement)) {
                $favoriLivreaudio = new Favoris();
                $favoriLivreaudio->setEtablissement($etablissement);
                $favoriLivreaudio->setCategorie($categorieFavori);
                $favoriLivreaudio->setNomCategorie($categorieFavori?->getNom() ?? 'LivreAudio');
                $favoriLivreaudio->setIdElement($livreaudio->getId());
                $favoriLivreaudio->setNomElement($livreaudio->getNom());

                $entityManager->persist($favoriLivreaudio);
                $entityManager->flush();
            } else {
                $this->addFlash('success', 'Vous avez atteint la limite maximale de 6 favoris.');
            }
        }
        #fin hiba favorie
        return $this->redirectToRoute('app_livreaudio');
        
    }

    #[Route('/livreaudio/supprimer/{id}', name: 'supprimer_livreaudio')]
    public function supprimerlivreaudio(EntityManagerInterface $entityManager, int $id): Response
    {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        $categorieFavori = $entityManager->getRepository(Categories::class)->findOneBy([
            'etablissement' => $etablissement,
            'nom' => 'Livre audio'
        ]);
  
       $livreaudio = $entityManager->getRepository(Livreaudio::class)->find($id);
       $Acce = $this->getUser()->getSupprimerLiveAudio() && $this->getUser()->getLIVREAUDIO() && $configApp->getEnableLIVREAUDIO()=="1" ;
       if (!$Acce) {
        $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('home');       }
        if (!$livreaudio) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $favorisLivreaudio = $entityManager->getRepository(Favoris::class)->findBy([
            'Etablissement' => $etablissement,
            'idElement' => $id,
        ]);

        foreach ($favorisLivreaudio as $favoriLivreaudio) {
            $isLivreaudioFavorite = false;

            if ($categorieFavori !== null && $favoriLivreaudio->getCategorie() === $categorieFavori) {
                $isLivreaudioFavorite = true;
            }

            if ($favoriLivreaudio->getNomCategorie() !== null && in_array(strtolower($favoriLivreaudio->getNomCategorie()), ['livreaudio', 'livre audio'], true)) {
                $isLivreaudioFavorite = true;
            }

            if ($isLivreaudioFavorite) {
                $entityManager->remove($favoriLivreaudio);
            }
        }

        $entityManager->remove($livreaudio);
        $entityManager->flush();

        return $this->redirectToRoute('app_livreaudio');
    }

    #[Route('/modifierlivreaudio', name: 'modifier_livreaudio')]
    public function modifierlivreaudio(EntityManagerInterface $entityManager): Response
    {   
        $repository = $entityManager->getRepository(Livreaudio::class);
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
          $Acce = $this->getUser()->getSauvegarderLiveAudio() && $this->getUser()->getLIVREAUDIO() && $configApp->getEnableLIVREAUDIO()=="1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');      
        }
       $livreaudio  = $repository->findBy(['etablissement' => $etablissement]);
       #huba favoris
        $categorieFavori = $entityManager->getRepository(Categories::class)->findOneBy([
            'etablissement' => $etablissement,
            'nom' => 'Livre audio'
        ]);
        if ($categorieFavori === null) {
            $categorieFavori = $entityManager->getRepository(Categories::class)->findOneBy([
                'etablissement' => $etablissement,
                'nom' => 'Livre audio'
            ]);
        }
       #fin huba favoris
        foreach ($livreaudio as $tele) {
            $tele->setActive('0');
            $entityManager->persist($tele);
            $entityManager->flush();
          }
        $request = Request::createFromGlobals();

      
    
        $nom = $request->get("listenom");
                 if (isset($nom) and !empty($nom)) {
                    foreach ($nom as $key => $k) {
                        $nom_television  = $repository->findById($key);
                         $nom_television[0]->setNom($k);
                         $entityManager->persist($nom_television[0]);
                         $entityManager->flush();
                    }
                 }
        $ip = $request->get("listeip");
            if (isset($ip) and !empty($ip)) {
                foreach ($ip as $key => $k) {
                    $ip_television  = $repository->findById($key);
                    $ip_television[0]->setIp($k);
                    $entityManager->persist($ip_television[0]);
                    $entityManager->flush();
                }
            }
      
        $catlivreaudio = $request->get("listcatlivreaudio");
            if (isset($catlivreaudio) and !empty($catlivreaudio)) {
                foreach ($catlivreaudio as $key => $k) {
                    $catlivreaudio  = $repository->findById($key);
                    $kc =  $entityManager->getRepository(CategorieLivreaudio::class)->findById($k)[0];
                    $catlivreaudio[0]->setCategorie($kc);
                    $entityManager->persist($catlivreaudio[0]);
                    $entityManager->flush();
                }
            }
      
        $active = $request->get("listeactive");
            if (isset($active) and !empty($active)) {
                foreach ($active as $key => $k) {
                    $active_television  = $repository->findById($key);
                    $active_television[0]->setActive("1");
                    $entityManager->persist($active_television[0]);
                    $entityManager->flush();
                }
            }
        #hiba favoris

        $favoris = $request->get("listefavoris", []);
        $currentFavoriteCount = $entityManager->getRepository(Favoris::class)->count([
            'Etablissement' => $etablissement,
        ]);

            foreach ($livreaudio as $tele) {
                $favoriLivreAudio = $entityManager->getRepository(Favoris::class)->findOneBy([
                    'Etablissement' => $etablissement,
                    'idElement' => $tele->getId(),
                ]);

                $isFavorite = array_key_exists($tele->getId(), $favoris);

                if ($isFavorite) {
                    if ($favoriLivreAudio === null) {
                        if ($currentFavoriteCount >= 6) {
                            $this->addFlash('success', 'Vous avez atteint la limite maximale de 6 favoris.');
                            continue;
                        }

                        $favoriLivreAudio = new Favoris();
                        $favoriLivreAudio->setEtablissement($etablissement);
                        $favoriLivreAudio->setIdElement($tele->getId());
                        $currentFavoriteCount++;
                    }

                    $favoriLivreAudio->setCategorie($categorieFavori);
                    $favoriLivreAudio->setNomCategorie($categorieFavori?->getNom() ?? 'LivreAudio');
                    $favoriLivreAudio->setNomElement($tele->getNom());

                    $entityManager->persist($favoriLivreAudio);
                } elseif ($favoriLivreAudio !== null) {
                    $entityManager->remove($favoriLivreAudio);
                    $currentFavoriteCount--;
                }
            }
      #fin hiba favoris
        $file1 = $request->files->get('listelogo');
            if (isset($file1) and !empty($file1)) {
                foreach ($file1 as $key => $k) {
                $thatlistfile = $repository->findById($key);
                if (!empty($k)) {
                    $fileName = md5(uniqid()) . '.' . $k->guessExtension();
                    $k->move($this->getParameter('livreaudio_directory'), $fileName);
                    $fileName = 'images/livreaudio/' . $fileName;
                } else {
                    $fileName = $thatlistfile[0]->getLogo();
                }
                $thatlistfile[0]->setLogo($fileName);
                $entityManager->persist($thatlistfile[0]);
                $entityManager->flush();
                }
            }
        return $this->redirectToRoute('app_livreaudio');
        
    }

    private function canPersistNewFavorite(EntityManagerInterface $entityManager, $etablissement): bool
    {
        $totalFavoris = $entityManager->getRepository(Favoris::class)->count([
            'Etablissement' => $etablissement,
        ]);

        return $totalFavoris < 6;
    }

   


}


