<?php


namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testIndexPage()
    {
        $client = static::createClient();
        $client->request('GET', '/index');
        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est un code 200
        $this->assertSelectorTextContains('h1', 'IndexController'); // Vérifie le texte dans un élément <h1>
    }

    public function testFaqPage()
    {
        $client = static::createClient();
        $client->request('GET', '/faq');
        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est un code 200
        $this->assertSelectorExists('h1'); // Vérifie que l'élément <h1> existe
    }

    public function testProduitsPage()
    {
        $client = static::createClient();
        $client->request('GET', '/produits');
        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est un code 200
        $this->assertSelectorExists('h1'); // Vérifie que l'élément <h1> existe
    }

    public function testSmartplusPage()
    {
        $client = static::createClient();
        $client->request('GET', '/smartplus');
        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est un code 200
        $this->assertSelectorExists('h1'); // Vérifie que l'élément <h1> existe
    }

    public function testContactPage()
    {
        $client = static::createClient();
        $client->request('GET', '/contact');
        $this->assertResponseIsSuccessful(); // Vérifie que la réponse est un code 200
        $this->assertSelectorExists('h1'); // Vérifie que l'élément <h1> existe
    }
}
 ?>