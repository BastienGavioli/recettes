<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $user = (new User());
        $user->setEmail('admin@admin.com')
            ->setUsername('admin')
            ->setPassword($this->hasher->hashPassword($user, 'admin'))
            ->setRoles(['ROLE_ADMIN'])
            ->setApiToken('admin_token')
            ->setIsVerified(true);
        $manager->persist($user);

        for ($i=1; $i<=10; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@admin.com")
                ->setUsername("user{$i}")
                ->setPassword($this->hasher->hashPassword($user, '0000'))
                ->setRoles(['ROLE_USER'])
                ->setApiToken("user{$i}")
                ->setIsVerified(true);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
