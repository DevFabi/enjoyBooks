<?php


namespace App\Service\Subscriptions;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class GetUserSubscriptionService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em )
    {
        $this->em = $em;
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getUserSubscriptions($userId)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $userId]);

        return $user->getSubscriptions();
    }
}