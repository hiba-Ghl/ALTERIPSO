<?php

namespace App\Controller;

use App\Entity\ConfigApp;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GestionProfileController extends AbstractController
{
    
    private $entityManager;
    private HttpClientInterface $httpClient;
    private Security $security;
    private $client;
    
    public function __construct(EntityManagerInterface $entityManager,
     HttpClientInterface $httpClient,
     Security $security,
    HttpClientInterface $client)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->security = $security;
        $this->client = $client;
    }

    // Fonction pour afficher tous les utilisateurs.
    #[Route('/gestion/profile', name: 'app_gestion_profile')]
    public function index(): Response
    {
    if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_SUPER_ADMIN')) {
       
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
    $etablissement = $this->getUser()->getEtablissement();
    $appConfig  = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);
    $users =  $this->entityManager->getRepository(User::class)->findBy(['etablissement' => $etablissement]);
    return $this->render('gestion_profile/index.html.twig', [
        'controller_name' => 'GestionProfileController',
        'appConfig' => $appConfig,
        'users' => $users,
        'user' => $this->getUser(),
    ]);
}
else{
    $this->addFlash('success', " Vous n'avez pas le droit d'accéder à cette page.");
    return $this->redirectToRoute('app_home');
}
    }

    // Fonction pour afficher le formulaire d'ajoute un utilisateur
    #[Route('/ajouteUtilisateur',name:'ajouteUtilisateur')]
    public function ajouteUtilisateur():Response
    {
        if ($this->security->isGranted('ROLE_ADMIN')  || $this->security->isGranted('ROLE_SUPER_ADMIN'))
        {
            if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $appConfig  = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);
        return $this->render('gestion_profile/ajouter.html.twig', [
            'appConfig' => $appConfig,
        ]);
    }
    else{
        $this->addFlash('success', " Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('app_home');
    }
    }
    //Fonction pour ajouter un utilisateur  sur la base de donnees
    #[Route('/ajouterU',name:'ajouterU')]
    public function ajoute(Request $request,UserPasswordHasherInterface $passwordHasher,TokenGeneratorInterface $tokenGenerator,)
    {
        if ($this->security->isGranted('ROLE_ADMIN')  || $this->security->isGranted('ROLE_SUPER_ADMIN')) {
        if( !$this->getUser())
        return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $user = new User();
        $contact =$request->query->all()['contact'];
        if($contact['plainPassword']['first']===$contact['plainPassword']['second'])
        {
            $hashedPassword = $passwordHasher->hashPassword($user, $contact['plainPassword']['second']);
            $user->setPassword($hashedPassword);
            $user->setUsername($contact['Identifiant']);
            $user->setEmail($contact['email']);
            // $response = $this->client->request('GET', 'https://emailvalidation.abstractapi.com/v1/', [
            //     'query' => [
            //         'api_key' => '28550f2f43bd4e808bfa565b3bc2adab',
            //         'email' => $contact['email'],
            //     ]
            // ]);
            // $data = $response->toArray();
            // $deliverability = $data['deliverability'] ?? 'Unknown';
            // if($deliverability != 'DELIVERABLE')
            // {
            //     $this->addFlash('danger','L\'adresse e-mail est invalide ou inaccessible. Veuillez entrer une adresse e-mail existante.');
            //     return $this->redirectToRoute('ajouteUtilisateur'); 
            // }
            // remplir tous les champs d'utilisateur
            $user->setEmailAdmin($this->getUser()->getEmail());
            $token = $tokenGenerator->generateToken();
            $user->setDerniertempExport(new DateTime());
            $user->setTentativeExport(0);
            $user->setResetToken($token);
            $user->setEtablissement($etablissement);
            $user->setRoles([$contact['fonction']]);
            $user->setMjTv($contact['maj'] ?? 0);
            $user->setChangeCat($contact['changecategorie'] ?? 0);
            $user->setTELEVISION($contact['tv'] ?? 0);
            $user->setSTATISTIQUECHAINETV($contact['stattv'] ?? 0);
            $user->setRADIO($contact['radio'] ?? 0);
            $user->setSERVICE($contact['service'] ?? 0);
            $user->setVOD($contact['vod'] ?? 0);
            $user->setMUSIQUE($contact['musique'] ?? 0);
            $user->setJEUX($contact['jeux'] ?? 0) ;
            $user->setSERVICESPAYANTS($contact['servicepayant'] ?? 0);
            $user->setQUESTIONNAIRE($contact['qst'] ?? 0) ;
            $user->setAPPLICATION($contact['application'] ?? 0) ;
            $user->setANNONCES($contact['annance'] ?? 0);
            $user->setCHARTES($contact['charte'] ?? 0) ;
            $user->setVIDEOS($contact['video'] ?? 0) ;
            $user->setSupportConnect($contact['supp'] ?? 0) ;
            $user->setRMOBILE($contact['Rmobile'] ?? 0) ;
            $user->setRREMOTE($contact['Rremote'] ?? 0);
            $user->setLIVREAUDIO($contact['livreaudio'] ?? 0); 
            $user->setMessagePersonnel($contact['msgperso'] ?? 0);
            $user->setAjoutTV($contact['ajouter'] ?? 0);
            $user->setModifierTv($contact['modifier'] ?? 0);
            $user->setSupprimerTV($contact['supprimer'] ?? 0);
            $user->setSauvegarderTv($contact['sauvegarder'] ?? 0);
            $user->setGratuiteTv($contact['chainegratuite'] ?? 0);
            $user->setAjoutRadio($contact['ajouterradio'] ?? 0);
            $user->setModifierRadio($contact['modifierradio'] ?? 0);
            $user->setSupprimerRadio($contact['supprimerradio'] ?? 0);
            $user->setSauvegarderRadio($contact['sauvegarderradio'] ?? 0);
            $user->setAjouteService($contact['ajouterservice'] ?? 0);
            $user->setModifierService($contact['modifierservice'] ?? 0);
            $user->setSupprimerService($contact['supprimerservice'] ?? 0);
            $user->setSauvegarderService($contact['sauvegarderservice'] ?? 0);
            $user->setLancerArretService($contact['lancerservice'] ?? 0);
            $user->setAjouteVod($contact['ajoutervod'] ?? 0);
            $user->setModifierVod($contact['modifiervod'] ?? 0);
            $user->setSupprimerVod($contact['supprimervod'] ?? 0);
            $user->setSauvegarderVod($contact['sauvegardervod'] ?? 0);
            $user->setLancerVod($contact['lancervod'] ?? 0);
            $user->setAjouterJeux($contact['ajouterjeux'] ?? 0);
            $user->setModifierJeux($contact['modifierjeux'] ?? 0);
            $user->setSupprimerJeux($contact['supprimerjeux'] ?? 0);
            $user->setSauvegarderJeux($contact['sauvegarderjeux'] ?? 0);
            $user->setAjoutApp($contact['ajouterapplication'] ?? 0);
            $user->setModifierApp($contact['modifierapplication'] ?? 0);
            $user->setSupprimerApp($contact['supprimerapplication'] ?? 0);
            $user->setSauvegarderApp($contact['sauvegarderapplication'] ?? 0);
            $user->setAjouteqs($contact['ajouterquestionnaire'] ?? 0);
            $user->setModifierqs($contact['modifierquestionnaire'] ?? 0);
            $user->setSupprimerqs($contact['supprimerquestionnaire'] ?? 0);
            $user->setSauvgarderqs($contact['sauvegarderquestionnaire'] ?? 0);
            $user->setAjoutLiveAudio($contact['ajouterlivreaudio'] ?? 0);
            $user->setModifierLiveAudio($contact['modifierlivreaudio'] ?? 0);
            $user->setSupprimerLiveAudio($contact['supprimerlivreaudio'] ?? 0);
            $user->setSauvegarderLiveAudio($contact['sauvegarderlivreaudio'] ?? 0);
            $user->setlancerRadio($contact['lancerRadio'] ?? '0');
            $user->setAjouteSupport($contact['ajoutersupport'] ?? 0);
            $user->setModifierSupport($contact['modifiersupport'] ?? 0);
            $user->setSupprimerSupport($contact['supprimersupport'] ?? 0);
            $user->setRedimarerSupport($contact['redemarrersupport'] ?? 0);
            $user->setEnvoyerMessage($contact['envoyermsg'] ?? 0);
            $user->setAjouterAnnonce($contact['ajouterannonce'] ?? 0);
            $user->setModifierAnnonce($contact['modifierannonce'] ?? 0);
            $user->setSuppAnnonce($contact['supprimerannonce'] ?? 0);
            $user->setSauvegarderAnnonce($contact['sauvegarderannonce'] ?? 0);
            $user->setRREMOTE($contact['Rremote'] ?? 0);
            $user->setRMOBILE($contact['Rmobile'] ?? 0);
            $user->setAjoutCategorie($contact['Ajoutecategorie'] ?? 0);
            $user->setSauvegarderAcceuil($contact['SauvegarderAcceuil'] ?? 0);
            $user->setFondEcran($contact['fondEcran'] ?? 0);
            $user->setlancerTV($contact['lancerTv'] ?? 0);
            $user->setajoutMusique($contact['ajoutermusique'] ?? 0);
            $user->setModifierMusique($contact['modifiermusique'] ?? 0);
            $user->setSuppMusique($contact['supprimermusique'] ?? 0);
            $user->setSauvegarderMusique($contact['sauvegardermusique'] ?? 0);
            $user->setlancerMusique($contact['lancerMusique'] ?? 0);
            $user->setlancerJeux($contact['lancerJeux'] ?? 0); 
            $user->setlancerVideo($contact['lancerVideo'] ?? 0); 
            $user->setlancerLivreAudio($contact['lancerLivreAudio'] ?? 0); 
            $user->setajoutVideo($contact['ajoutervideo'] ?? 0);
            $user->setModifierVideo($contact['modifiervideo'] ?? 0);
            $user->setSauvegarderVideo($contact['sauvegardervideo'] ?? 0);
            $user->setsuppVideo($contact['supprimervideo'] ?? 0);

            $user->setCatLivreAudio($contact['CategorieLivreAudio'] ?? 0);
            $user->setCatRadio($contact['CategorieRadio'] ?? 0);
            $user->setCatVod($contact['CategorieVod'] ?? 0);
            $user->setServiceEnChambre($contact['ServiceEnChambre'] ?? 0);
            
            $user->setajoutCatLivreAudio($contact['ajouterCategorieLivreAudio'] ?? 0);
            $user->setModifierCatLivreAudio($contact['modifierCategorieLivreAudio'] ?? 0);
            $user->setSauvegarderCatLivreAudio($contact['sauvegarderCategorieLivreAudio'] ?? 0);
            $user->setSuppCatLivreAudio($contact['supprimerCategorieLivreAudio'] ?? 0);

            $user->setAjoutCatRadio($contact['ajouterCategorieRadio'] ?? 0);
            $user->setModifierCatRadio($contact['modifierCategorieRadio'] ?? 0);
            $user->setSauvegarderCatRadio($contact['sauvegarderCategorieRadio'] ?? 0);
            $user->setSuppCatRadio($contact['supprimerCategorieRadio'] ?? 0);

            $user->setAjoutCatVod($contact['ajouterCategorieVod'] ?? 0);
            $user->setModifierCatVod($contact['modifierCategorieVod'] ?? 0);
            $user->setSauvegarderCatVod($contact['sauvegarderCategorieVod'] ?? 0);
            $user->setSuppCatVod($contact['supprimerCategorieVod'] ?? 0);

            $user->setAjoutServiceEnChambre($contact['ajouterServiceEnChambre'] ?? 0);
            $user->setModifierServiceEnChambre($contact['modifierServiceEnChambre'] ?? 0);
            $user->setSauvegarderServiceEnChambre($contact['sauvegarderServiceEnChambre'] ?? 0);
            $user->setSuppServiceEnChambre($contact['supprimerServiceEnChambre'] ?? 0);
            $user->setResultatQs($contact['Resultqst'] ?? 0);

            $user->setSauvegarderChartPatient($contact['sauvegarderChartPatient'] ?? 0);
            $user->setSauvgarderServicePayant($contact['sauvegarderServicePayant'] ?? 0);
            $user->setCocherMeteo($contact['SauvegarderMeteo'] ?? 0);
            $user->setCocherLogo($contact['SauvegarderLogo'] ?? 0);

            $user->setModifierRmobile($contact['sauvegarderRmobile'] ?? 0);
            $user->setAjouteServiceEtablissement($contact['AjouteServiceEtablissement'] ?? 0);
            $user->setCheckSupport($contact['sauvegarderCheck'] ?? 0);
            $user->setImportRadio($contact['ImporterRadio'] ?? 0);
            $user->setExportRadio($contact['exporterRadio'] ?? 0);
            $user->setImportTV($contact['ImporterTelevision'] ?? 0);
            $user->setExportTv($contact['exporterTelevision'] ?? 0);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_gestion_profile');
    }
    else{
        $this->addFlash('success', " Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('app_home');
    }
    }

// Fonction pour afficher le formulaire de modification d'un utilisateur

    #[Route('/ModifierUtilisateur/{id}',name:'ModifierUtilisateur')]
    public function ModifierUtilisateur(string $id):Response
    {
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_SUPER_ADMIN')) {
            
            $id = (int)$id; 
            if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $appConfig  = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['etablissement'=>$etablissement,'id'=>$id]);
        return $this->render('gestion_profile/modifier.html.twig', [
            'appConfig' => $appConfig,
            'users'=>$user,
        ]);
    }
    else{
        $this->addFlash('success', " Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('app_home');
    }
    }

// Fonction pour faire des modifications sur l'utilisateur dans la base de donnee
    #[Route('/Update/{id}',name:'Update')]
    public function Update(Request $request,string $id,UserPasswordHasherInterface $passwordHasher):Response
    {
        if ($this->security->isGranted('ROLE_ADMIN')  || $this->security->isGranted('ROLE_SUPER_ADMIN')) {
    
            $id = (int)$id; 
            if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['etablissement'=>$etablissement,'id'=>$id]);
        $contact =$request->query->all()['contact'];
        if($contact['plainPassword']['first'])
        {
            $hashedPassword = $passwordHasher->hashPassword($user, $contact['plainPassword']['second']);
            $user->setPassword($hashedPassword);
        }
        $user->setUsername($contact['Identifiant']);
        $user->setEmail($contact['email']);
        // $response = $this->client->request('GET', 'https://emailvalidation.abstractapi.com/v1/', [
            //     'query' => [
            //         'api_key' => '28550f2f43bd4e808bfa565b3bc2adab',
            //         'email' => $contact['email'],
            //     ]
            // ]);
            // $data = $response->toArray();
            // $deliverability = $data['deliverability'] ?? 'Unknown';
            // if($deliverability != 'DELIVERABLE')
            // {
                //     $this->addFlash('danger','L\'adresse e-mail est invalide ou inaccessible. Veuillez entrer une adresse e-mail existante.');
                //     return $this->redirectToRoute('ModifierUtilisateur',['id'=>$id]);
                // }
            $user->setEmailAdmin($this->getUser()->getEmail());
            $user->setEtablissement($etablissement);
            $user->setRoles([$contact['fonction']]);
            $user->setMjTv($contact['maj'] ?? '0' );
            $user->setChangeCat($contact['changecategorie'] ?? '0' );
            $user->setTELEVISION($contact['tv'] ?? '0' );
            $user->setSTATISTIQUECHAINETV($contact['stattv'] ?? '0' );
            $user->setRADIO($contact['radio'] ?? '0');
            $user->setSERVICE($contact['service']?? '0');
            $user->setVOD($contact['vod']?? '0');
            $user->setMUSIQUE($contact['musique']?? '0');
            $user->setJEUX($contact['jeux'] ?? '0') ;
            $user->setSERVICESPAYANTS($contact['servicepayant'] ?? '0');
            $user->setQUESTIONNAIRE($contact['qst']?? '0') ;
            $user->setAPPLICATION($contact['application'] ?? '0') ;
            $user->setANNONCES($contact['annance']?? '0');
            $user->setCHARTES($contact['charte'] ?? '0') ;
            $user->setVIDEOS($contact['video'] ?? '0') ;
            $user->setSupportConnect($contact['supp'] ?? '0') ;
            $user->setRMOBILE($contact['Rmobile'] ?? '0') ;
            $user->setRREMOTE($contact['Rremote'] ?? '0');
            $user->setLIVREAUDIO($contact['livreaudio']?? '0'); 
            $user->setMessagePersonnel($contact['msgperso'] ?? '0');
            $user->setAjoutTV($contact['ajouter'] ?? '0');
            $user->setModifierTv($contact['modifier'] ?? '0');
            $user->setSupprimerTV($contact['supprimer'] ?? '0');
            $user->setSauvegarderTv($contact['sauvegarder'] ?? '0');
            $user->setGratuiteTv($contact['chainegratuite'] ?? '0');
            $user->setAjoutRadio($contact['ajouterradio'] ?? '0');
            $user->setModifierRadio($contact['modifierradio'] ?? '0');
            $user->setSupprimerRadio($contact['supprimerradio'] ?? '0');
            $user->setSauvegarderRadio($contact['sauvegarderradio'] ?? '0');
            $user->setlancerRadio($contact['lancerRadio'] ?? '0');
            $user->setAjouteService($contact['ajouterservice'] ?? '0');
            $user->setModifierService($contact['modifierservice'] ?? '0');
            $user->setSupprimerService($contact['supprimerservice'] ?? '0');
            $user->setSauvegarderService($contact['sauvegarderservice'] ?? '0');
            $user->setLancerArretService($contact['lancerservice'] ?? '0');
            $user->setAjouteVod($contact['ajoutervod'] ?? '0');
            $user->setModifierVod($contact['modifiervod'] ?? '0');
            $user->setSupprimerVod($contact['supprimervod'] ?? '0');
            $user->setSauvegarderVod($contact['sauvegardervod'] ?? '0');
            $user->setLancerVod($contact['lancervod'] ?? '0');
            $user->setAjouterJeux($contact['ajouterjeux'] ?? '0');
            $user->setModifierJeux($contact['modifierjeux'] ?? '0');
            $user->setSupprimerJeux($contact['supprimerjeux'] ?? '0');
            $user->setSauvegarderJeux($contact['sauvegarderjeux'] ?? '0');
            $user->setAjoutApp($contact['ajouterapplication'] ?? '0');
            $user->setModifierApp($contact['modifierapplication'] ?? '0');
            $user->setSupprimerApp($contact['supprimerapplication'] ?? '0');
            $user->setSauvegarderApp($contact['sauvegarderapplication']?? '0');
            $user->setAjouteqs($contact['ajouterquestionnaire'] ?? '0');
            $user->setModifierqs($contact['modifierquestionnaire'] ?? '0');
            $user->setSupprimerqs($contact['supprimerquestionnaire']?? '0');
            $user->setSauvgarderqs($contact['sauvegarderquestionnaire'] ?? '0');
            $user->setAjoutLiveAudio($contact['ajouterlivreaudio'] ?? '0');
            $user->setModifierLiveAudio($contact['modifierlivreaudio'] ?? '0');
            $user->setSupprimerLiveAudio($contact['supprimerlivreaudio'] ?? '0');
            $user->setSauvegarderLiveAudio($contact['sauvegarderlivreaudio'] ?? '0');
            $user->setAjouteSupport($contact['ajoutersupport'] ?? '0');
            $user->setModifierSupport($contact['modifiersupport'] ?? '0');
            $user->setSupprimerSupport($contact['supprimersupport']?? '0');
            $user->setRedimarerSupport($contact['redemarrersupport'] ?? '0');
            $user->setEnvoyerMessage($contact['envoyermsg'] ?? '0');
            $user->setAjouterAnnonce($contact['ajouterannonce'] ?? 0);
            $user->setModifierAnnonce($contact['modifierannonce'] ?? 0);
            $user->setSuppAnnonce($contact['supprimerannonce'] ?? 0);
            $user->setSauvegarderAnnonce($contact['sauvegarderannonce'] ?? 0);
            $user->setRREMOTE($contact['Rremote'] ?? 0);
            $user->setRMOBILE($contact['Rmobile'] ?? 0);
            $user->setAjoutCategorie($contact['Ajoutecategorie'] ?? 0);
            $user->setSauvegarderAcceuil($contact['SauvegarderAcceuil'] ?? 0);
            $user->setFondEcran($contact['fondEcran'] ?? 0);
            $user->setlancerTV($contact['lancerTv'] ?? 0);
            $user->setajoutMusique($contact['ajoutermusique'] ?? 0);
            $user->setModifierMusique($contact['modifiermusique'] ?? 0);
            $user->setSuppMusique($contact['supprimermusique'] ?? 0);
            $user->setSauvegarderMusique($contact['sauvegardermusique'] ?? 0);
            $user->setlancerMusique($contact['lancerMusique'] ?? 0);
            $user->setlancerJeux($contact['lancerJeux'] ?? 0); 
            $user->setlancerVideo($contact['lancerVideo'] ?? 0); 
            $user->setlancerLivreAudio($contact['lancerLivreAudio'] ?? 0); 
            $user->setajoutVideo($contact['ajoutervideo'] ?? 0);
            $user->setModifierVideo($contact['modifiervideo'] ?? 0);
            $user->setSauvegarderVideo($contact['sauvegardervideo'] ?? 0);
            $user->setsuppVideo($contact['supprimervideo'] ?? 0);

            $user->setCatLivreAudio($contact['CategorieLivreAudio'] ?? 0);
            $user->setCatRadio($contact['CategorieRadio'] ?? 0);
            $user->setCatVod($contact['CategorieVod'] ?? 0);
            $user->setServiceEnChambre($contact['ServiceEnChambre'] ?? 0);
            $user->setajoutCatLivreAudio($contact['ajouterCategorieLivreAudio'] ?? 0);
            $user->setModifierCatLivreAudio($contact['modifierCategorieLivreAudio'] ?? 0);
            $user->setSauvegarderCatLivreAudio($contact['sauvegarderCategorieLivreAudio'] ?? 0);
            $user->setSuppCatLivreAudio($contact['supprimerCategorieLivreAudio'] ?? 0);
            
            $user->setAjoutCatRadio($contact['ajouterCategorieRadio'] ?? 0);
            $user->setModifierCatRadio($contact['modifierCategorieRadio'] ?? 0);
            $user->setSauvegarderCatRadio($contact['sauvegarderCategorieRadio'] ?? 0);
            $user->setSuppCatRadio($contact['supprimerCategorieRadio'] ?? 0);
            
            $user->setAjoutCatVod($contact['ajouterCategorieVod'] ?? 0);
            $user->setModifierCatVod($contact['modifierCategorieVod'] ?? 0);
            $user->setSauvegarderCatVod($contact['sauvegarderCategorieVod'] ?? 0);
            $user->setSuppCatVod($contact['supprimerCategorieVod'] ?? 0);
            
            $user->setAjoutServiceEnChambre($contact['ajouterServiceEnChambre'] ?? 0);
            $user->setModifierServiceEnChambre($contact['modifierServiceEnChambre'] ?? 0);
            $user->setSauvegarderServiceEnChambre($contact['sauvegarderServiceEnChambre'] ?? 0);
            $user->setSuppServiceEnChambre($contact['supprimerServiceEnChambre'] ?? 0);
            $user->setResultatQs($contact['Resultqst'] ?? 0);
            
            $user->setSauvegarderChartPatient($contact['sauvegarderChartPatient'] ?? 0);
            $user->setSauvgarderServicePayant($contact['sauvegarderServicePayant'] ?? 0);
            $user->setCocherMeteo($contact['SauvegarderMeteo'] ?? 0);
            $user->setCocherLogo($contact['SauvegarderLogo'] ?? 0);
            
            $user->setModifierRmobile($contact['sauvegarderRmobile'] ?? 0);
            $user->setAjouteServiceEtablissement($contact['AjouteServiceEtablissement'] ?? 0);
            $user->setCheckSupport($contact['sauvegarderCheck'] ?? 0);
            
            $user->setImportRadio($contact['ImporterRadio'] ?? 0);
            $user->setExportRadio($contact['exporterRadio'] ?? 0);
            $user->setImportTV($contact['ImporterTelevision'] ?? 0);
            $user->setExportTv($contact['exporterTelevision'] ?? 0);
            $appConfig  = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);
            $existingEmailUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $contact['email']]);
            $existingUsernameUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' =>$contact['Identifiant']]);

            if ($existingEmailUser !== null && $id !== $existingEmailUser->getId()) {
                $bugs['email'] = 'Cet email est déjà utilisé.';
            }
            if ($existingUsernameUser !== null && $id !== $existingUsernameUser->getId()) {
                $bugs['username'] = 'Cet identifiant est déjà utilisé.';
            }
            if(($existingEmailUser && $id !== $existingEmailUser->getId())  || ($existingUsernameUser && ($id !== $existingUsernameUser->getId())) )
            {
                return $this->render('gestion_profile/modifier.html.twig', [
                    'appConfig' => $appConfig,
                    'email' => $contact['email'],
                    'userName' => $contact['Identifiant'],
                    'bugs' => $bugs,
                    'users'=>$user,
                ]);
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_gestion_profile');
        }
        else{
            $this->addFlash('success', " Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('app_home');
        }
        }

// Fonction pour afficher les details de chaque utilisateur
    #[Route('/DetailUtilisateur/{id}',name:'DetailUtilisateur')]
    public function DetailUtilisateur(string $id):Response
    {
        if ($this->security->isGranted('ROLE_ADMIN')  || $this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $id = (int)$id; 
            if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();
        $appConfig  = $this->entityManager->getRepository(ConfigApp::class)->findOneBy(['etablissement'=>$etablissement]);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['etablissement'=>$etablissement,'id'=>$id]);
        return $this->render('gestion_profile/detail.html.twig', [
            'appConfig' => $appConfig,
            'user'=>$user
        ]);
    }
    else{
        $this->addFlash('success', " Vous n'avez pas le droit d'accéder à cette page.");
        return $this->redirectToRoute('app_home');
    }
    }




// Fonction pour supprimer un utilisateur 
    #[Route('/DeleteUser/{id}',name:'DeleteUser')]
    public function DeleteUtilisateur(string $id):Response
    {
        if ($this->security->isGranted('ROLE_ADMIN')  || $this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $id = (int)$id; 
            if( !$this->getUser())
            return $this->redirectToRoute('app_login');
        $etablissement = $this->getUser()->getEtablissement();  
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['etablissement'=>$etablissement,'id'=>$id]);
        if($user)
        {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_gestion_profile');
    }
        else{
            $this->addFlash('success', " Vous n'avez pas le droit d'accéder à cette page.");
            return $this->redirectToRoute('app_home');
        }
    }
// fonction pour verifier est ce que d'autre utilisateur deja utilise le meme email et nom
    #[Route('/searchExisteUtilisateur',name:'searchExisteUtilisateur',methods:'POST')]
    public function searchExisteUtilisateur(Request $request)
    {   
        if ($this->security->isGranted('ROLE_ADMIN')  || $this->security->isGranted('ROLE_SUPER_ADMIN')) {
        $data = json_decode($request->getContent(), true);
        $existingEmailUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        $existingUsernameUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']]);
        if($existingEmailUser || $existingUsernameUser)
        return new JsonResponse(['exists' => true], 200);
        else 
        return new JsonResponse(['exists' => false], 200);
    }
else{
    $this->addFlash('success', " Vous n'avez pas le droit d'accéder à cette page.");
    return $this->redirectToRoute('app_home');}
    }
}
