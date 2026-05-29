<?php

namespace App\Tests\Unit\Questionnaire;

use App\Entity\Questionnaire;
use App\Entity\ResultatQuestionnaire;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for questionnaire bidirectional relation helpers.
 */
class QuestionnaireEntityTest extends TestCase
{
    public function testItAddsAndRemovesResultsFromTheCollection(): void
    {
        $questionnaire = new Questionnaire();
        $resultat = new ResultatQuestionnaire();

        $questionnaire->addResultatQuestionnaire($resultat);

        self::assertCount(1, $questionnaire->getResultatQuestionnaires());
        self::assertSame($questionnaire, $resultat->getQuestionnaire());

        $questionnaire->removeResultatQuestionnaire($resultat);

        self::assertCount(0, $questionnaire->getResultatQuestionnaires());
    }
}