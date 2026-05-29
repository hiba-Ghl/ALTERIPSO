<?php

namespace App\Tests\Integration\Questionnaire;

use App\Entity\Questionnaire;
use App\Tests\Support\KernelDatabaseTestCase;

/**
 * Integration test for the custom Questionnaire repository query.
 */
class QuestionnaireRepositoryTest extends KernelDatabaseTestCase
{
    public function testItFindsQuestionnairesByEtablissement(): void
    {
        $etablissement = $this->createEtablissement();
        $service = $this->createServiceEtablissement($etablissement);
        $questionnaire = $this->createQuestionnaire($etablissement, $service, 'Le Wi-Fi fonctionne-t-il ?', 1);

        $this->entityManager->clear();

        $results = $this->entityManager
            ->getRepository(Questionnaire::class)
            ->findByEtablissement($etablissement);

        self::assertCount(1, $results);
        self::assertSame($questionnaire->getId(), $results[0]->getId());
        self::assertSame('Le Wi-Fi fonctionne-t-il ?', $results[0]->getQuestion());
    }
}