<?php

namespace App\Tests\Support;

use App\Entity\Annonce;
use App\Entity\Chambre;
use App\Entity\ConfigApp;
use App\Entity\Etablissement;
use App\Entity\Questionnaire;
use App\Entity\ServiceEnChambre;
use App\Entity\ServiceEtablissement;
use App\Entity\User;
use App\Entity\TypeServiceEnChambre;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Shared database helpers for integration and functional tests.
 */
trait TestDatabaseTrait
{
    protected EntityManagerInterface $entityManager;

    protected function resetSchema(): void
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        if ($metadata === []) {
            return;
        }

        try {
            $schemaTool->dropSchema($metadata);
        } catch (\Throwable) {
            // Fresh SQLite database or partially initialized schema.
        }

        $schemaTool->createSchema($metadata);
    }

    protected function setProperty(object $object, string $property, mixed $value): void
    {
        $reflectionProperty = new \ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    protected function persist(object $entity): object
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    protected function createEtablissement(int $id = 1001, string $name = 'Hotel Test'): Etablissement
    {
        $etablissement = new Etablissement();
        $etablissement->setId($id);
        $etablissement->setNom($name);
        $etablissement->setCode('HT-' . $id);
        $etablissement->setDescription('Etablissement de test pour les modules Back-Office.');
        $etablissement->setLicence(20);
        $etablissement->setAdresse('1 rue des tests');
        $etablissement->setLogo('logo-test.png');
        $etablissement->setBackground('background-test.jpg');
        $etablissement->setType('Hotel');
        $etablissement->setMsgbienvenu(1);
        $etablissement->setMsgap(1);
        $etablissement->setAccessTvInCheckout(0);
        $etablissement->setVille('Paris');
        $etablissement->setNomEtablissement($name);
        $etablissement->setPrenom('Test');
        $etablissement->setPays('FR');
        $etablissement->setGenre('Hotel');
        $etablissement->setLogoactive('1');
        $etablissement->setMeteoactive('1');
        $etablissement->setTypeText('scroll');
        $etablissement->setCouleurText('#ffffff');
        $etablissement->setTailleText(18);
        $etablissement->setVolumeDemarage(10);
        $etablissement->setRss('https://example.test/rss');

        return $this->persist($etablissement);
    }

    protected function createConfigApp(Etablissement $etablissement): ConfigApp
    {
        $configApp = new ConfigApp();
        $configApp->setEtablissement($etablissement);

        $defaults = [
            'ServerHostRsmartv' => 'localhost',
            'ServerHostPaytv' => 'localhost',
            'ServerHostVod' => 'localhost',
            'ServerHostLivreaudio' => 'localhost',
            'ServerHostToukan' => 'localhost',
            'ServerHostCanalplus' => 'localhost',
            'ServerHostMail' => 'localhost',
            'DatabaseNameRsmartv' => 'rsmartv_test',
            'DatabaseUserRsmartv' => 'root',
            'DatabasePassRsmartv' => 'root',
            'DatabaseNamePaytv' => 'paytv_test',
            'DatabaseUserPaytv' => 'root',
            'DatabasePassPaytv' => 'root',
            'DatabaseNameVod' => 'vod_test',
            'DatabaseUserVod' => 'root',
            'DatabasePassVod' => 'root',
            'DatabaseNameCanalplus' => 'canalplus_test',
            'DatabaseUserCanalplus' => 'root',
            'DatabasePassCanalplus' => 'root',
            'EnableTELEVISION' => '1',
            'EnableSTATISTIQUECHAINETV' => '1',
            'EnableRADIO' => '1',
            'EnableSERVICE' => '1',
            'EnableVOD' => '1',
            'EnableMUSIQUE' => '1',
            'EnableLIVREAUDIO' => '1',
            'EnableJEUX' => '1',
            'EnableSERVICESPAYANTS' => '1',
            'EnableQUESTIONNAIRE' => '1',
            'EnableAPPLICATION' => '1',
            'EnableENREGISTREMENT' => '1',
            'EnableANNONCES' => '1',
            'EnableCHARTES' => '1',
            'EnableVIDEOS' => '1',
            'EnableSupportConnect' => '1',
            'EnableMessagePersonnels' => '1',
            'EnableRApplication' => '1',
            'EnableCategories' => '1',
            'CHECKIN' => '1',
            'CHECKOUT' => '1',
            'CODEPORTAIL' => '1234',
            'StatusServeur' => 'Online',
        ];

        foreach ($defaults as $property => $value) {
            $this->setProperty($configApp, $property, $value);
        }

        return $this->persist($configApp);
    }

    protected function createServiceEtablissement(Etablissement $etablissement, int $id = 2001, string $name = 'Géneral'): ServiceEtablissement
    {
        $service = new ServiceEtablissement();
        $service->setId($id);
        $service->setEtablissement($etablissement);
        $service->setNom($name);
        $service->setDescription('Service de test');
        $service->setActive('1');
        $service->setBackground('service-background.png');

        return $this->persist($service);
    }

    protected function createTypeServiceEnChambre(Etablissement $etablissement, string $name = 'Menu'): TypeServiceEnChambre
    {
        $type = new TypeServiceEnChambre();
        $type->setNom($name);
        $type->setDescription('Type de service de test');
        $type->setEtablissement($etablissement);

        return $this->persist($type);
    }

    protected function createQuestionnaire(Etablissement $etablissement, ServiceEtablissement $service, string $question = 'La chambre est-elle propre ?', int $position = 1): Questionnaire
    {
        $questionnaire = new Questionnaire();
        $questionnaire->setEtablissement($etablissement);
        $questionnaire->setService($service);
        $questionnaire->setQuestion($question);
        $questionnaire->setPosition($position);
        $questionnaire->setActive(1);
        $questionnaire->setFr($question);
        $questionnaire->setEn('Is the room clean?');
        $questionnaire->setEs('¿La habitación está limpia?');
        $questionnaire->setPt('O quarto está limpo?');
        $questionnaire->setIt('La camera è pulita?');
        $questionnaire->setRu('Комната чистая?');
        $questionnaire->setDe('Ist das Zimmer sauber?');
        $questionnaire->setZh('房间干净吗？');
        $questionnaire->setAr('هل الغرفة نظيفة؟');

        return $this->persist($questionnaire);
    }

    protected function createChambre(Etablissement $etablissement, ServiceEtablissement $service, array $overrides = []): Chambre
    {
        $chambre = new Chambre();
        $chambre->setNom($overrides['nom'] ?? 'CH-101');
        $chambre->setIp($overrides['ip'] ?? '192.168.0.101');
        $chambre->setMac($overrides['mac'] ?? 'AA:BB:CC:DD:EE:FF');
        $chambre->setActive($overrides['active'] ?? '1');
        $chambre->setEtage($overrides['etage'] ?? '1');
        $chambre->setSupport($overrides['support'] ?? 'R-TV');
        $chambre->setType($overrides['type'] ?? 'Samsung');
        $chambre->setDate($overrides['date'] ?? '2026-05-25');
        $chambre->setVersion($overrides['version'] ?? '1.0.0');
        $chambre->setClient($overrides['client'] ?? 'Client Test');
        $chambre->setCin($overrides['cin'] ?? '0');
        $chambre->setCout($overrides['cout'] ?? '0');
        $chambre->setCheckval($overrides['checkval'] ?? '1');
        $chambre->setDrois($overrides['drois'] ?? '1/1/1/1/1/1/1/1/1/1');
        $chambre->setLangue($overrides['langue'] ?? 'fr');
        $chambre->setToken($overrides['token'] ?? 'token-test');
        $chambre->setBackground($overrides['background'] ?? 'background-test.jpg');
        $chambre->setTypeaffichage($overrides['typeaffichage'] ?? '1');
        $chambre->setLogin($overrides['login'] ?? 'login-test');
        $chambre->setMdp($overrides['mdp'] ?? 'secret');
        $chambre->setEtablissement($etablissement);
        $chambre->setService($service);

        if (isset($overrides['chaine'])) {
            $chambre->setChaine($overrides['chaine']);
        }

        return $this->persist($chambre);
    }

    protected function createAnnonce(Etablissement $etablissement, array $overrides = []): Annonce
    {
        $annonce = new Annonce();
        $annonce->setEtablissement($etablissement);
        $annonce->setNom($overrides['nom'] ?? 'Annonce test');
        $annonce->setType($overrides['type'] ?? 'Message');
        $annonce->setUrl($overrides['url'] ?? 'Message de test');
        $annonce->setDatedebut($overrides['datedebut'] ?? '2026-05-25');
        $annonce->setDatefin($overrides['datefin'] ?? '2026-06-25');
        $annonce->setDuree($overrides['duree'] ?? 30);
        $annonce->setTheme($overrides['theme'] ?? 'theme-test');
        $annonce->setPosition($overrides['position'] ?? 'Haut');
        $annonce->setCouleur($overrides['couleur'] ?? '#ffffff');
        $annonce->setCouleurBande($overrides['couleurBande'] ?? '#ff0000');
        $annonce->setPolice($overrides['police'] ?? 'Arial');
        $annonce->setTaille($overrides['taille'] ?? 20);
        $annonce->setStyle($overrides['style'] ?? 'normal');
        $annonce->setAnimation($overrides['animation'] ?? 'scroll');
        $annonce->setEsMessage($overrides['esMessage'] ?? 'Message ES');
        $annonce->setPtMessage($overrides['ptMessage'] ?? 'Message PT');
        $annonce->setItMessage($overrides['itMessage'] ?? 'Message IT');
        $annonce->setRuMessage($overrides['ruMessage'] ?? 'Message RU');
        $annonce->setDeMessage($overrides['deMessage'] ?? 'Message DE');
        $annonce->setZhMessage($overrides['zhMessage'] ?? 'Message ZH');
        $annonce->setArMessage($overrides['arMessage'] ?? 'Message AR');
        $annonce->setActive($overrides['active'] ?? true);
        $annonce->setFrMessage($overrides['frMessage'] ?? 'Message FR');
        $annonce->setEnMessage($overrides['enMessage'] ?? 'Message EN');
        $annonce->setVitesseDefilement($overrides['vitesseDefilement'] ?? 5);

        return $this->persist($annonce);
    }

    protected function createBackOfficeUser(Etablissement $etablissement, array $flags = [], string $identifier = 'backoffice@test.local'): User
    {
        $user = new User();
        $user->setEmail($identifier);
        $user->setUsername($identifier);
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);
        $user->setResetToken('test-reset-token');
        $user->setEtablissement($etablissement);

        $reflectionClass = new \ReflectionClass($user);
        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();

            if (in_array($propertyName, ['id', 'email', 'roles', 'password', 'username', 'resetToken', 'etablissement'], true)) {
                continue;
            }

            $type = $property->getType();
            if ($type === null) {
                continue;
            }

            $typeName = $type instanceof \ReflectionNamedType ? $type->getName() : null;

            if ($typeName === 'bool') {
                $this->setProperty($user, $propertyName, true);
                continue;
            }

            if ($typeName === 'int') {
                $this->setProperty($user, $propertyName, 0);
                continue;
            }

            if ($typeName === 'array') {
                $this->setProperty($user, $propertyName, ['ROLE_USER']);
                continue;
            }

            if ($typeName === 'DateTimeInterface' || is_a($typeName, \DateTimeInterface::class, true)) {
                $this->setProperty($user, $propertyName, new \DateTimeImmutable());
                continue;
            }

            if ($propertyName === 'EmailAdmin') {
                $this->setProperty($user, $propertyName, $identifier);
            }
        }

        return $this->persist($user);
    }
}