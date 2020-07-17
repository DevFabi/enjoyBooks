<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $authors = [
                    "Delphine de Vigan",
                    "Mary Higgins Clark",
                    "Guillaume Musso",
                    "Marc Levy",
                    "Gillian Flynn",
                    "Anna Gavalda"
                    ];

        foreach ($authors as $authorToAdd) {
            $author = new Author();
            $author->setName($authorToAdd);
            $manager->persist($author);
        }

        $manager->flush();

    
    }
}
