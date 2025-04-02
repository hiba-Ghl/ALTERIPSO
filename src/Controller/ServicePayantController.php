<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categories;
use App\Entity\ConfigApp;
use Symfony\Component\HttpFoundation\Request;

class ServicePayantController extends AbstractController
{
    // Déclaration de la propriété privée $entityManager
    private $entityManager;
    private $request;

    // Constructeur de la classe, injecte l'EntityManagerInterface
    public function __construct(EntityManagerInterface $entityManager)
    {
        // Initialise la propriété $entityManager avec l'injection de dépendance
        $this->entityManager = $entityManager;
        $this->request = Request::createFromGlobals();
    }

    #[Route('/servicepayant', name: 'app_service_payant')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

        $Acce = $this->getUser()->getSERVICESPAYANTS() && $configApp->getEnableSERVICESPAYANTS()=="1" ;
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');
        }
        $repository = $this->entityManager->getRepository(Categories::class);

        $servicepayant = $repository->findBy(
            ['etablissement' => $etablissement, 'titre' => 'servicepayant']
        )[0];
        $package = $servicepayant->getPackage();



        $valider = $this->request->get("valider");
        if (isset($valider)) {

            $fileName = $servicepayant->getPackage();




            // $fileName=' ';
            $file = $this->request->files->get('file');
           // var_dump($file);die();
            if (!empty($file)) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('service_payant_directory'), $fileName);
                $fileName = 'pdf/service_payant/' . $fileName;
            }
            else {
                $fileName = $servicepayant->getPackage();
            }




            $servicepayant->setPackage($fileName);

            $this->entityManager->persist($servicepayant);
           $this->entityManager->flush();

            //sleep(6);
            //header("Refresh:0; url=http://192.168.1.101:1111/ipso/web/app.php/showpdf");
            header("Refresh:0");
        }
        //var_dump($servicepayant);die();
        return $this->render('service_payant/index.html.twig', [
            'servicepayant' => $servicepayant,'package'=>$package,'appConfig' => $configApp,        
            'user' => $this->getUser(),



        ]);
    }
}
