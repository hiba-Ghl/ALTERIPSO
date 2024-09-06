<?php
namespace App\Tests\Controller;

use App\Entity\Questionnaire;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class QuestionnaireControllerTest extends WebTestCase
{
    public function testSupprimerQuestionExists()
    {
        $client = static::createClient();

        // Créez un mock pour l'EntityManager
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // Créez un mock pour le Questionnaire
        $questionnaire = $this->createMock(Questionnaire::class);

        // Créez un mock pour le Repository
        $repository = $this->createMock(\Doctrine\Persistence\ObjectRepository::class);
        $repository->expects($this->once())
                   ->method('find')
                   ->willReturn($questionnaire);

        $entityManager->expects($this->once())
                      ->method('getRepository')
                      ->willReturn($repository);

        $entityManager->expects($this->once())
                      ->method('remove')
                      ->with($questionnaire);

        $entityManager->expects($this->once())
                      ->method('flush');

        // Simulez la requête GET
        $client->request('GET', '/questionnaire/supprimer/296');

        // Assurez-vous que la réponse est une redirection
        $this->assertResponseRedirects('/questionnaire');
    }

    public function testSupprimerQuestionNotFound()
    {
        $client = static::createClient();

        // Créez un mock pour l'EntityManager
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // Créez un mock pour le Repository
        $repository = $this->createMock(\Doctrine\Persistence\ObjectRepository::class);
        $repository->expects($this->once())
                   ->method('find')
                   ->willReturn(null);

        $entityManager->expects($this->once())
                      ->method('getRepository')
                      ->willReturn($repository);

        // Simulez la requête GET
        $client->request('GET', '/questionnaire/supprimer/999');

        // Assurez-vous que la réponse est une exception NotFoundHttpException
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}

?>