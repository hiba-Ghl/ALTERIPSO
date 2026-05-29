<?php

namespace App\Tests\Support;

use App\Entity\Etablissement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Base class for functional tests that browse the real controllers and Twig templates.
 */
abstract class WebDatabaseTestCase extends WebTestCase
{
    use TestDatabaseTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->resetSchema();
    }

    protected function createAuthenticatedClient(Etablissement $etablissement, array $flags = [], string $identifier = 'backoffice@test.local')
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $managedEtablissement = $this->entityManager->find(Etablissement::class, $etablissement->getId()) ?? $etablissement;
        $client->loginUser($this->createBackOfficeUser($managedEtablissement, $flags, $identifier));
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        return $client;
    }

    protected function withSuperGlobals(array $get = [], array $post = [], array $files = [], array $server = []): void
    {
        $_GET = $get;
        $_POST = $post;
        $_FILES = $files;
        $_REQUEST = array_merge($get, $post);
        $_SERVER = array_merge($_SERVER, $server);
    }
}