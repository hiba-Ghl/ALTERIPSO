<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categories;
use App\Entity\ConfigApp;
use Symfony\Component\HttpFoundation\Request;

class ChartePatientController extends AbstractController
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

    #[Route('/chartepatient', name: 'app_charte_patient')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

        $Acce = $this->getUser()->getCHARTES() && $configApp->getEnableCHARTES() == "1";
        if (!$Acce) {
            $this->addFlash('success',"Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('home');        }
        $repository = $this->entityManager->getRepository(Categories::class);
        $configApp = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement' => $etablissement]);

        $charte = $repository->findBy(
            ['etablissement' => $etablissement, 'titre' => 'charte']
        )[0];
        $package = $charte->getPackage();



        $valider = $this->request->get("valider");
        if (isset($valider)) {

            $fileName = $charte->getPackage();




            // $fileName=' ';
            $file = $this->request->files->get('file');
            if (!empty($file)) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('charte_patient_directory'), $fileName);
                $fileName = 'pdf/charte_patient/' . $fileName;
            }
            else {
                $fileName = $charte->getPackage();
            }




            $charte->setPackage($fileName);

            $this->entityManager->persist($charte);
           $this->entityManager->flush();

            //sleep(6);
            //header("Refresh:0; url=http://192.168.1.101:1111/ipso/web/app.php/showpdf");
            header("Refresh:0");
        }
        return $this->render('charte_patient/index.html.twig', [
            'charte' => $charte,'package'=>$package,
            'appConfig' => $configApp,
        ]);
    }
}
