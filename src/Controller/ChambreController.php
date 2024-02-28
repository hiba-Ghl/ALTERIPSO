<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ServiceEtablissement;
use App\Entity\Chambre;


class ChambreController extends AbstractController
{
    #[Route('/chambre', name: 'app_chambre')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $repository = $entityManager->getRepository(Chambre::class);
        $repositorys = $entityManager->getRepository(ServiceEtablissement::class);
        $etablissement = $this->getUser()->getEtablissement();
        $idetablissement = $etablissement->getId();
        $chambres  = $repository->findBy(['etablissement' => $etablissement]);
        $serviceetablissement  = $repositorys->findBy(['etablissement' => $etablissement]);
        return $this->render('chambre/index.html.twig', [
            'chambres' => $chambres,'etablissement' =>$etablissement,'serviceetablissement'=>$serviceetablissement
        ]);
    }

    #[Route('/checkin', name: 'app_checkin')]
    public function checkinAction(EntityManagerInterface $entityManager): Response
   {
        $request = Request::createFromGlobals();
        $repository = $entityManager->getRepository(Chambre::class);
        $etatcheckin = $request->get("etatcheckin");
        file_put_contents('checkin.txt',$etatcheckin);
        //var_dump($etatcheckin);//die();

        $etablissement = $this->getUser()->getEtablissement();
        $box = $request->get('box');
        $boxsx = array();
        $chambresx = array();

       // var_dump($box);//die();
       if (isset($box) and !empty($box)) {

           foreach ($box as $key => $k) {

            $boxs  = $repository->findById($key);
               array_push($boxsx, $boxs[0]->getId());
               array_push($chambresx, $boxs[0]->getNom());
               $supp = $boxs[0]->getSupport();


               if($etatcheckin=="checkin"){
               $boxs[0]->setCheckval('1');
               $boxs[0]->setDrois('1/1/1/1/1/1/1/1/1/1');
               }
               elseif($etatcheckin=="checkout"){
                  $boxs[0]->setCheckval('0');
                  $boxs[0]->setDrois('0/0/0/0/0/0/0/0/0/0');
                  }
               $entityManager->persist($boxs[0]);
               $entityManager->flush();
           }
     
              /* $Manager = new JsonRPCPush();
               $Manager->update_categories($idetab, $chambresx);*/
           
       }

     





       return $this->redirectToRoute('app_chambre');
   }
}
