<?php

namespace App\Tests\Functional\Service;

use App\Entity\ServiceEnChambre;
use App\Tests\Support\WebDatabaseTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Functional test for the service-in-room back-office pages.
 */
class ServiceEnChambreControllerTest extends WebDatabaseTestCase
{
    public function testIndexDisplaysTheServiceCardsForAnAuthorizedUser(): void
    {
        $etablissement = $this->createEtablissement();
        $this->createConfigApp($etablissement);
        $typeService = $this->createTypeServiceEnChambre($etablissement, 'Menu');

        $service = new ServiceEnChambre();
        $service->setNom('Room menu');
        $service->setPosition(1);
        $service->setActive(true);
        $service->setContenu('[]');
        $service->setLogo('service-menu.png');
        $service->setDescription('Service de chambre');
        $service->setTypeServiceEnChambre($typeService);
        $service->setEtablissement($etablissement);
        $this->persist($service);

        $client = $this->createAuthenticatedClient($etablissement, [
            'isServiceEnChambre' => true,
            'isAjoutServiceEnChambre' => true,
            'getAjoutServiceEnChambre' => true,
            'getSauvegarderServiceEnChambre' => true,
            'getSuppServiceEnChambre' => true,
            'getModifierServiceEnChambre' => true,
        ]);

        $client->request('GET', '/serviceenchambre');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.service-header', 'Service en chambre');
        self::assertSelectorExists('.card');
    }

    public function testAddingAServiceTypeAndServiceCreatesTheRecordFlow(): void
    {
        $etablissement = $this->createEtablissement();
        $this->createConfigApp($etablissement);
        $typeService = $this->createTypeServiceEnChambre($etablissement, 'Menu');

        $client = $this->createAuthenticatedClient($etablissement, [
            'isServiceEnChambre' => true,
            'isAjoutServiceEnChambre' => true,
            'getAjoutServiceEnChambre' => true,
        ]);

        $client->request('POST', '/ajoutercategoriesservice', [
            'nom' => 'Spa',
            'Description' => 'Services de spa',
        ]);

        self::assertResponseIsSuccessful();

        $projectDir = static::getContainer()->getParameter('kernel.project_dir');
        $tempPath = tempnam(sys_get_temp_dir(), 'svc');
        file_put_contents($tempPath, file_get_contents($projectDir . '/tests/fixtures/test_image.jpg'));

        $uploadedFile = new UploadedFile($tempPath, 'test_image.jpg', 'image/jpeg', null, true);

        $client->request('POST', '/ajouterservice', [
            'nom' => 'Massage',
            'Description' => 'Massage relaxant',
            'listrequte' => '[]',
            'EN' => 'Massage',
            'ES' => 'Masaje',
            'PT' => 'Massagem',
            'IT' => 'Massaggio',
            'RU' => 'Массаж',
            'DE' => 'Massage',
            'ZH' => '按摩',
            'AR' => 'تدليك',
            'type_service_en_chambre' => $typeService->getId(),
        ], [
            'logo' => $uploadedFile,
        ]);

        self::assertResponseIsSuccessful();

        $createdService = $this->entityManager->getRepository(ServiceEnChambre::class)->findOneBy([
            'etablissement' => $etablissement,
            'nom' => 'Massage',
        ]);

        self::assertNotNull($createdService);
    }
}