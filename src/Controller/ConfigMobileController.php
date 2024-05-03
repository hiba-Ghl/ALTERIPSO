<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Configmobile;

class ConfigMobileController extends AbstractController
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
    #[Route('/config/mobile', name: 'app_config_mobile')]
    public function index(): Response
    {   $repository = $this->entityManager->getRepository(ConfigMobile::class);
        $etablissement = $this->getUser()->getEtablissement();
        $config = $repository->findBy(
            ['etablissement' => $etablissement]
        )[0];
        $msg = "Mise à jour de l'utilisateur ";
        $valider = $this->request->get('valider');
        if (isset($valider)) {

            $title = $this->request->get('title');
            $imgAffichage = $this->request->get('img_affichage');
            $imgLogo = $this->request->get('img_logo');
            $infoAdresse = $this->request->get('info_adresse');
            $infoCP = $this->request->get('info_cp');
            $infoVille = $this->request->get('info_ville');
            $infoPays = $this->request->get('info_pays');
            $infoTel = $this->request->get('info_tel');
            $infoSiret = $this->request->get('info_siret');
            $infoEmail = $this->request->get('info_email');
            $infochambre = $this->request->get('info_chambre');
            $infoId = $this->request->get('info_id');
            $prixCasque = $this->request->get('prix_casque');
            $dateVersion = $this->request->get('date_version');
            if ($dateVersion == '1')
            $dateValeurs = 0;
            else
            $dateValeurs = $this->request->get('date_valeurs');

            $factureObjet = $this->request->get('facture_objet');
            $factureTitle = $this->request->get('facture_title');
            $factureFooter = $this->request->get('facture_footer');
            $phase = $this->request->get('phase');
            $payementVersion = $this->request->get('payement_version');
            if ($payementVersion == 'payline'){
            $payementId = $this->request->get('payement_id');
            $payementKey = $this->request->get('payement_key');
            $payementVad = $this->request->get('payement_vad');
            $payementPk = 0;
            $payementSk =0;

            }
            else {
            $payementPk = $this->request->get('payement_pk');
            $payementSk = $this->request->get('payement_sk');
            $payementId = 0;
            $payementKey = 0;
            $payementVad =0;
            }
            

            $config->setEtablissement($etablissement);
            $config->setTitle($title);
            $config->setImgAffichage($imgAffichage);
            $config->setImgLogo($imgLogo);
            $config->setInfoAdresse($infoAdresse);
            $config->setInfoCP($infoCP);
            $config->setInfoVille($infoVille);
            $config->setInfoPays($infoPays);
            $config->setInfoTel($infoTel);
            $config->setInfoSiret($infoSiret);
            $config->setInfoEmail($infoEmail);
            $config->setInfochambre($infochambre);
            $config->setInfoId($infoId);
            $config->setPrixCasque($prixCasque);
            $config->setDateVersion($dateVersion);
            $config->setDateValeurs($dateValeurs);
            $config->setFactureObjet($factureObjet);
            $config->setFactureTitle($factureTitle);
            $config->setFactureFooter($factureFooter);
            $config->setPhase($phase);
            $config->setPayementVersion($payementVersion);
            $config->setPayementId($payementId);
            $config->setPayementKey($payementKey);
            $config->setPayementVad($payementVad);
            $config->setPayementPk($payementPk);
            $config->setPayementSk($payementSk);
            



            $this->entityManager->persist($config);
            $this->entityManager->flush();

            

        }

        return $this->render('config_mobile/index.html.twig', array(
            'msg' => $msg, 'config' => $config
        ));
    }

}
