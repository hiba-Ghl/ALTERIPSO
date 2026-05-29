<?php

namespace App\Tests\Support;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Base class for integration tests that talk directly to Doctrine.
 */
abstract class KernelDatabaseTestCase extends KernelTestCase
{
    use TestDatabaseTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->resetSchema();
    }
}