<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminAuthorControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $adminUser = $userRepository->findOneByEmail('fabiola.belet@gmail.com');

        $client->loginUser($adminUser);

        $crawler = $client->request('GET', '/admin/author');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Author handler');
    }

    public function testCreate()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $adminUser = $userRepository->findOneByEmail('fabiola.belet@gmail.com');

        $client->loginUser($adminUser);

        $crawler = $client->request('GET', '/admin/author/create');

        $form = $crawler->selectButton('create')->form();

        $form['author[name]'] = 'NouveauPetit Nom';

        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }
}
