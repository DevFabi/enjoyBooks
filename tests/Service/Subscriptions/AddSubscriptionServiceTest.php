<?php

namespace App\Tests\Service\Subscriptions;

use App\Entity\Author;
use App\Entity\User;
use App\Repository\AuthorRepository;
use App\Service\Subscriptions\AddSubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AddSubscriptionServiceTest extends TestCase
{

    public function testAddSubscription()
    {
        $data['author'] = "Delphine de Vigan";

        $user = new User();
        $user->setEmail('tutu@gmail.com')->setPassword('xxx');

        $author = new Author();
        $author->setName('Delphine de Vigan');

        $em = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $authorRepository = $this->getMockBuilder(AuthorRepository::class)->disableOriginalConstructor()->getMock();

        $em->method('getRepository')->willReturn($authorRepository);

        $authorRepository->method('findOneBy')->willReturn($author);

        $log = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();

        $addSubscription = new AddSubscriptionService($em,$log);

        $addSubscription->addSubscription($user, $data);

        $userSubscriptions = $user->getSubscriptions();
        $this->assertEquals($userSubscriptions[0]->getName(), 'Delphine de Vigan');
    }
}
