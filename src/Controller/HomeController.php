<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Etablissement;
use App\Entity\Categories;
use App\Entity\ServiceEtablissement;
use App\Entity\Chambre;
use App\Entity\ConfigApp;
use App\Entity\User;

class HomeController extends AbstractController
{
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
        //var_dump($categories);die();
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
        
        //var_dump($a);die();
        if (!$a) {
            throw $this->createNotFoundException(
                'No Category found for id '.$id
            );
        }
        $selectedcat = $request->get("selectedcat");
        $b = $entityManager->getRepository(Categories::class)->find($selectedcat);
      
       // var_dump($categoriesa);die();
        $posa=$a->getPosition();
        $posb=$b->getPosition();
        $posc=$posa;
        $posa=$posb;
        $posb=$posc;
        //var_dump($posa,$posb);die();
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
        //var_dump($logoactive);die();
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
