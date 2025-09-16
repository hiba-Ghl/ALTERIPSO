<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categories;
use App\Entity\ServiceEtablissement;
use App\Entity\Chambre;
use App\Entity\ConfigApp;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class HomeController extends AbstractController
{


// Route permettant d'afficher une image depuis un répertoire spécifique
#[Route('/images/{filename}/{directory}', name: 'afficher_image', requirements: ['filename' => '.+'])]
public function image(string $filename, string $directory)
{
    // Récupère le chemin de base du répertoire à partir des paramètres définis dans services.yaml
    $baseDir = $this->getParameter($directory);

    // Construit le chemin absolu de l'image
    $fullPath = realpath($baseDir . '/' . $filename);

    // Vérifie si le fichier existe et qu'il est bien situé dans le répertoire autorisé
    // if (!$fullPath || !str_starts_with($fullPath, $baseDir)) {
        if (!$fullPath) {
        // Si le fichier est introuvable ou en dehors du répertoire, une erreur 404 est lancée
        throw $this->createNotFoundException('Image not found.');
    }
    // Retourne la réponse contenant l'image en mode inline (affichée dans le navigateur)
    return new BinaryFileResponse($fullPath, 200, [
        'Content-Disposition' => ResponseHeaderBag::DISPOSITION_INLINE
    ]);
}

    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $repository = $entityManager->getRepository(Categories::class);
        $user = $this->getUser();
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $user->getEtablissement();
        $categories = $repository->findBy(
            ['etablissement' => $etablissement],
            ['position' => 'ASC']
        );
        $repositorys = $entityManager->getRepository(ServiceEtablissement::class);
        $serviceetablissement  = $repositorys->findBy(['etablissement' => $etablissement]);
        $chambres  =  $entityManager->getRepository(Chambre::class)->findBy(['etablissement' => $etablissement]);
        $serviceEtablissementRepo = $entityManager->getRepository(ServiceEtablissement::class);
        $services = $serviceEtablissementRepo->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);
        $appConfig = $entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);
        if ($appConfig) {
            $configArray = ['Status'=>$appConfig->getStatusServeur()];
        } 
        else{
            $configArray = ['Status'=>'online'];
        }
        $configJson = json_encode($configArray);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'etablissement' => $etablissement,
            'categories' => $categories,
            'servicebox' => $serviceetablissement,  
            'boxs' => $chambres,
            'services' => $services,
            'appConfig' => $appConfig,
            'configApp' => $configJson,
            'user' => $user
        ]);
    }

    #[Route('/modifierposition/{id}', name: 'modifierposition')]
    public function modifierposition(EntityManagerInterface $entityManager, int $id): Response
    {
        $request = Request::createFromGlobals();
        $a = $entityManager->getRepository(Categories::class)->find($id);
        
        if (!$a) {
            throw $this->createNotFoundException(
                'No Category found for id '.$id
            );
        }
        $selectedcat = $request->get("selectedcat");
        $b = $entityManager->getRepository(Categories::class)->find($selectedcat);
      
        $posa=$a->getPosition();
        $posb=$b->getPosition();
        $posc=$posa;
        $posa=$posb;
        $posb=$posc;
        $a->setPosition($posa);
        $b->setPosition($posb);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/modifierhome', name: 'modifierhome')]
    public function modifierhome(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Categories::class);
        $request = Request::createFromGlobals();
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $logoactive = $request->get('logoactive');
        $meteoactive = $request->get('meteoactive');
        $etablissement->setLogoactive(0);
        $etablissement->setMeteoactive(0);
        $categories = $repository->findBy(
            ['etablissement' => $etablissement],
            ['position' => 'ASC']
        );
        
        foreach ($categories as $cat) {
              $cat->setActive(0);
             
        }
        $entityManager->flush();

        $cat = $request->get('cat');
        
        if (isset($cat) and !empty($cat)) {
            foreach ($cat as $key => $lg) {
                $categorieschecked = $entityManager->getRepository(categories::class)->find($key);
                $categorieschecked->setActive(1);
  
            }}
            if (isset($logoactive) and !empty($logoactive)) {
                $etablissement->setLogoactive(1);
            }
            if (isset($meteoactive) and !empty($meteoactive)) {
                $etablissement->setMeteoactive(1);
            }
            $entityManager->flush();
        return $this->redirectToRoute('app_home');
    }
}
