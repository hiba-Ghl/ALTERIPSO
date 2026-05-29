<?php

namespace App\Tests\Functional\Questionnaire;

use App\Entity\Questionnaire;
use App\Tests\Support\WebDatabaseTestCase;

/**
 * Functional test for questionnaire pages and form submissions.
 */
class QuestionnaireControllerTest extends WebDatabaseTestCase
{
    public function testIndexDisplaysQuestionnaireTableForAnAuthorizedUser(): void
    {
        $etablissement = $this->createEtablissement();
        $this->createConfigApp($etablissement);
        $service = $this->createServiceEtablissement($etablissement);
        $this->createQuestionnaire($etablissement, $service);

        $client = $this->createAuthenticatedClient($etablissement, [
            'getQUESTIONNAIRE' => true,
            'getAjouteqs' => true,
            'getSauvgarderqs' => true,
            'getModifierqs' => true,
            'getSupprimerqs' => true,
        ]);

        $client->request('GET', '/questionnaire');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('table');
        self::assertSelectorTextContains('#titre', 'Liste QUESTIONNAIRE');
    }

    public function testAddAndDeleteQuestionnaireWorkWithTheBackOfficePermissions(): void
    {
        $etablissement = $this->createEtablissement();
        $this->createConfigApp($etablissement);
        $service = $this->createServiceEtablissement($etablissement);

        $client = $this->createAuthenticatedClient($etablissement, [
            'getQUESTIONNAIRE' => true,
            'getAjouteqs' => true,
            'getSupprimerqs' => true,
            'getSauvgarderqs' => true,
        ]);

        $client->request('POST', '/questionnaire/ajouter', [
            'Position' => 1,
            'Service' => $service->getId(),
            'copie' => 'La chambre est-elle propre ?',
            'Active' => 1,
            'fr' => 'La chambre est-elle propre ?',
            'EN' => 'Is the room clean?',
            'ES' => '¿La habitación está limpia?',
            'IT' => 'La camera è pulita?',
            'ZH' => '房间干净吗？',
            'RU' => 'Комната чистая?',
            'DE' => 'Ist das Zimmer sauber?',
            'PT' => 'O quarto está limpo?',
            'AR' => 'هل الغرفة نظيفة؟',
            'valider' => '1',
        ]);

        self::assertResponseRedirects('/questionnaire?serviceId=' . $service->getId());

        $createdQuestionnaire = $this->entityManager->getRepository(Questionnaire::class)->findOneBy([
            'etablissement' => $etablissement,
            'service' => $service,
            'question' => 'La chambre est-elle propre ?',
        ]);

        self::assertNotNull($createdQuestionnaire);

        $client->request('GET', '/questionnaire/supprimer/' . $createdQuestionnaire->getId());

        self::assertResponseRedirects('/questionnaire');
    }
}