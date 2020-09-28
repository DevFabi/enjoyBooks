<?php

namespace App\Tests\Service\Subscriptions;

use App\Entity\Author;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Subscriptions\GetUserSubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GetUserSubscriptionServiceTest extends TestCase
{
    public function testGetUserSubscriptions()
    {
        $author = new Author();
        $author->setName('Marc Levy');

        $user = new User();
        $user->setEmail('tutu@gmail.com')->setPassword('xxx')->addSubscription($author);

        $em = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $userRepository = $this->getMockBuilder(UserRepository::class)->setMethods(['findOneBy'])->disableOriginalConstructor()->getMock();

        $em->method('getRepository')->willReturn($userRepository);

        $userRepository->method('findOneBy')->willReturn($user);

        $getUserSubcriptionService = new GetUserSubscriptionService($em);
        $subscriptions = $getUserSubcriptionService->getUserSubscriptions(12);

        $this->assertEquals($subscriptions[0]->getName(), 'Marc Levy');
    }
}
