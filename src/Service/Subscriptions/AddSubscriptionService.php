<?php

declare(strict_types = 1);

namespace App\Service\Subscriptions;


use App\Entity\Author;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AddSubscriptionService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em )
    {
        $this->em = $em;
    }


    public function addSubscription(User $user, $data)
    {
        $authorId = $data['author'];

        $author = $this->em->getRepository(Author::class)->findOneBy(['id' => $authorId]);

        /** @var Author $author */
        $user->addSubscription($author);

        $this->em->persist($user);
        $this->em->flush();
    }
}