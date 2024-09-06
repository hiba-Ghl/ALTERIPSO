<?php
//tests/Controller/HomeControllerTest.php
//namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Entity\Etablissement;

class HomeControllerTest extends WebTestCase
{
    private function createAuthenticatedClient()
    {
        $client = static::createClient();

        // Crée une instance de l'établissement
        $etablissement = new Etablissement();
        $etablissement->setName('Test Etablissement'); // Mets les champs requis ici

        // Crée un mock de l'utilisateur
        $user = $this->createMock(User::class);
        $user->method('getEtablissement')->willReturn($etablissement);

        // Connecte l'utilisateur au client
        $client->loginUser($user);

        return $client;
    }

    public function testIndex()
    {
        $etablissement = new Etablissement();
        // Remplacez `setName` par la méthode correcte, par exemple `setNom`.
        $etablissement->setNom('Example Nom');
    
        // Ajoutez les assertions en fonction des modifications
        $this->assertEquals('Example Nom', $etablissement->getNom());
    
        // Autres assertions et tests...
    }
    
    public function testModifierPosition()
    {
        $etablissement = new Etablissement();
        // Utilisez la méthode appropriée ici aussi.
        $etablissement->setNom('Nouveau Nom');
    
        // Ajoutez les assertions nécessaires
        $this->assertEquals('Nouveau Nom', $etablissement->getNom());
    
        // Autres assertions et tests...
    }
    
    public function testModifierHome()
    {
        $etablissement = new Etablissement();
        // Utilisez la méthode correcte pour modifier l'entité.
        $etablissement->setNom('Home Nom');
    
        // Ajoutez les assertions pour vérifier le bon fonctionnement
        $this->assertEquals('Home Nom', $etablissement->getNom());
    
        // Autres assertions et tests...
    }
    
}

?>