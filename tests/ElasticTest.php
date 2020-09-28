<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ElasticTest extends WebTestCase
{
    public function testSearchBookBis()
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $adminUser = $userRepository->findOneByEmail('fabiola.belet@gmail.com');

        $client->loginUser($adminUser);

        $crawler = $client->request('GET', '/searchBooksBis');

        $form = $crawler->selectButton('submit')->form();

        $form['form[text]'] = 'HarryPotter';

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
