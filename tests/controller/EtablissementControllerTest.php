<?php 
namespace App\Tests\Controller;

use App\Entity\Chambre;
use App\Entity\Etablissement;
use App\Entity\User;
use App\Repository\ChambreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\HttpFoundation\File\UploadedFile;


class EtablissementControllerTest extends WebTestCase
{
   // public function testModifierBackgroundAction()
    // {
    //     // Créer le client HTTP pour simuler des requêtes
    //     $client = static::createClient();

    //     // Mock d'un fichier uploadé
    //     $filePath = __DIR__ . '/../fixtures/test_image.jpg';  // Chemin relatif
    //     $uploadedFile = new UploadedFile(
    //         $filePath,
    //         'test_image.jpg',
    //         'image/jpeg',
    //         null,
    //         true
    //     );
        

    //     // Simuler un utilisateur connecté avec un établissement
    //     $user = $this->createMock(User::class);
    //     $etablissement = $this->createMock(Etablissement::class);
    //     $user->method('getEtablissement')->willReturn($etablissement);

    //     $client->loginUser($user);

    //     // Mock du repository et des entités
    //     $entityManager = $this->createMock(EntityManagerInterface::class);
    //     $chambreRepository = $this->createMock(ChambreRepository::class);
    //     $entityManager->method('getRepository')->willReturn($chambreRepository);

    //     // Mock des chambres
    //     $chambre = $this->createMock(Chambre::class);
    //     $chambreRepository->method('findBy')->willReturn([$chambre]);

    //     // Simuler la requête avec un fichier et les paramètres de type et d'affectation
    //     $client->request('POST', '/modifierbackground', [], [
    //         'backgound' => $file,
    //     ], [
    //         'type' => 'images',
    //         'typa' => 'images',
    //     ]);

    //     // Assertion que la réponse est une redirection vers la route app_home
    //     $this->assertResponseRedirects('/home');

    //     // Assertion que l'image a bien été modifiée dans la base de données
    //     // Cette partie peut varier en fonction de vos besoins
    //     $chambre->expects($this->once())->method('setBackground');
    //     $etablissement->expects($this->once())->method('setBackground');
    //     $entityManager->expects($this->once())->method('flush');
    // }

    public function testModifierBackgroundAction()
{
    $client = static::createClient();

    // Chemin vers un fichier temporaire pour simuler l'upload
    $filePath = sys_get_temp_dir() . '/test_image.jpg';
    file_put_contents($filePath, 'test');  // Crée un fichier temporaire

    // Simulez l'upload d'un fichier
    $file = new UploadedFile(
        $filePath,
        'test_image.jpg',
        'image/jpeg',
        null,
        true
    );

    // Simulez l'utilisateur et l'établissement
    $user = $this->createMock(User::class);
    $etablissement = $this->createMock(Etablissement::class);

    $user->expects($this->any())
         ->method('getEtablissement')
         ->willReturn($etablissement);

    // Injection dans la requête
    $client->request('POST', '/modifierbackground', [], ['backgound' => $file], [
        'CONTENT_TYPE' => 'multipart/form-data',
    ]);

    // Assurez-vous que la réponse est une redirection
    $this->assertResponseRedirects('app_home');
}

}
?>