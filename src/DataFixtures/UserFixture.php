<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher){}

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = new User();
        $user->setUsername('dev_freeloader');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->appendRole('ROLE_FREE');
        $manager->persist($user);

        $user = new User();
        $user->setUsername('dev_whale');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->appendRole('ROLE_PREMIUM');
        $manager->persist($user);

        $user = new User();
        $user->setUsername('dev_admin');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->appendRole('ROLE_ADMIN');
        $manager->persist($user);

        $manager->flush();
    }
}
