<?php


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

    /**
     * AddSubscriptionService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em )
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @param $data
     */
    public function addSubscription(User $user, $data)
    {
        $authorId = $data['author'];

        $author = $this->em->getRepository(Author::class)->findOneBy(['id' => $authorId]);

        $user->addSubscription($author);

        $this->em->persist($user);
        $this->em->flush();
    }
}