<?php

namespace App\Tests\Functional\Chambre;

use App\Tests\Support\WebDatabaseTestCase;

/**
 * Functional test for the room back-office pages and CRUD endpoints.
 */
class ChambreControllerTest extends WebDatabaseTestCase
{
    public function testAddFormIsAccessibleForAnAuthorizedUser(): void
    {
        $etablissement = $this->createEtablissement();
        $this->createConfigApp($etablissement);

        $client = $this->createAuthenticatedClient($etablissement, [
            'getSupportConnect' => true,
            'getAjouteSupport' => true,
        ]);

        $client->request('GET', '/chambre/ajouter');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.support-add-title', 'Ajouter Support');
    }

    public function testDeleteRoomRedirectsAfterRemoval(): void
    {
        $etablissement = $this->createEtablissement();
        $this->createConfigApp($etablissement);
        $service = $this->createServiceEtablissement($etablissement);
        $chambre = $this->createChambre($etablissement, $service);

        $client = $this->createAuthenticatedClient($etablissement, [
            'getSupportConnect' => true,
            'getSupprimerSupport' => true,
        ]);

        $client->request('GET', '/chambre/supprimer/' . $chambre->getId());

        self::assertResponseRedirects('/chambre');
    }

    public function testAddRoomPersistsTheSupportWhenTheBackOfficeRequestIsValid(): void
    {
        $etablissement = $this->createEtablissement();
        $this->createConfigApp($etablissement);
        $service = $this->createServiceEtablissement($etablissement);

        $client = $this->createAuthenticatedClient($etablissement, [
            'getSupportConnect' => true,
            'getAjouteSupport' => true,
        ]);

        $this->withSuperGlobals(
            [
                'nom' => 'CH-301',
                'typesupport' => 'Samsung',
                'type' => '1',
                'ip' => '192.168.0.301',
                'mac' => 'AA:BB:CC:DD:EE:11',
                'etage' => '3',
                'Service' => $service->getId(),
                'back' => 'background-test.jpg',
                'typeaffichage' => '1',
                'valider' => '1',
            ],
            [],
            [],
            [
                'REQUEST_METHOD' => 'GET',
            ]
        );

        $client->request('GET', '/chambre/ajouter');

        self::assertResponseRedirects('/chambre');
    }
}