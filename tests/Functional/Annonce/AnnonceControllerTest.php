<?php

namespace App\Tests\Functional\Annonce;

use App\Entity\Annonce;
use App\Tests\Support\WebDatabaseTestCase;

/**
 * Functional test for the announcement back-office pages and CRUD flow.
 */
class AnnonceControllerTest extends WebDatabaseTestCase
{
    public function testIndexShowsTheAnnouncementTableForAnAuthorizedUser(): void
    {
        $etablissement = $this->createEtablissement();
        $this->createConfigApp($etablissement);
        $this->createAnnonce($etablissement);

        $client = $this->createAuthenticatedClient($etablissement, [
            'getANNONCES' => true,
            'getAjouterAnnonce' => true,
            'getModifierAnnonce' => true,
            'getSuppAnnonce' => true,
            'getSauvegarderAnnonce' => true,
        ]);

        $client->request('GET', '/annonce');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('table');
        self::assertSelectorTextContains('.annonce-header', 'Gestion des Annonces');
    }

    public function testAddAndDeleteAnnouncementWorkForAValidBackOfficeUser(): void
    {
        $etablissement = $this->createEtablissement();
        $this->createConfigApp($etablissement);

        $client = $this->createAuthenticatedClient($etablissement, [
            'getANNONCES' => true,
            'getAjouterAnnonce' => true,
            'getSuppAnnonce' => true,
        ]);

        $client->request('POST', '/annonce/ajouter', [
            'valider' => '1',
            'nom' => 'Annonce de test',
            'type' => 'Message',
            'position' => 'Haut',
            'datedebut' => '2026-05-25',
            'datefin' => '2026-06-25',
            'duree' => 15,
            'FRMessage' => 'Message FR',
            'ENMessage' => 'Message EN',
            'ESMessage' => 'Message ES',
            'PTMessage' => 'Message PT',
            'ITMessage' => 'Message IT',
            'RUMessage' => 'Message RU',
            'DEMessage' => 'Message DE',
            'ZHMessage' => 'Message ZH',
            'ARMessage' => 'Message AR',
            'font' => 'Arial',
            'fontSize' => 20,
            'style' => 'normal',
            'active' => 1,
            'animation' => 'scroll',
            'vitesseDefilement' => 5,
            'themee' => 'autre',
            'theme' => 'theme-test',
            'textColorDefilement' => '#ffffff',
            'bgColorDefilement' => '#ff0000',
            'text' => 'Bienvenue',
        ]);

        self::assertResponseRedirects('/annonce');

        $createdAnnonce = $this->entityManager->getRepository(Annonce::class)->findOneBy([
            'etablissement' => $etablissement,
            'nom' => 'Annonce de test',
        ]);

        self::assertNotNull($createdAnnonce);

        $client->request('GET', '/annonce/supprimer/' . $createdAnnonce->getId());

        self::assertResponseRedirects('/annonce');
    }
}