<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testRegistration()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('submit')->form();

        $form['account[firstName]'] = 'Lulu';
        $form['account[lastName]'] = 'Toto';
        $form['account[email]'] = 'lutoluto@gmail.com';
        $form['account[password]'] = 'Heylutolu';
        $form['account[passwordConfirm]'] = 'Heylutolu';

        $crawler = $client->submit($form);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
