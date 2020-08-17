<?php

declare(strict_types = 1);

namespace App\Service\Subscriptions;


use App\Entity\Author;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class AddSubscriptionService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }


    public function addSubscription(User $user, $data)
    {
        $author = $this->em->getRepository(Author::class)->findOneBy(['name' => $data]);

        $this->logger->info('User'. $user->getEmail(). ' just add author '. $author->getName());

        /** @var Author $author */
        $user->addSubscription($author);

        $this->em->persist($user);
        $this->em->flush();
    }
}