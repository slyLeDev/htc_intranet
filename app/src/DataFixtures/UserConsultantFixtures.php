<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\ResetAutoincrementEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserConsultantFixtures extends Fixture implements OrderedFixtureInterface
{

    private UserPasswordHasherInterface $userPasswordHasher;
    private ResetAutoincrementEntity $resetAutoincrementEntity;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        ResetAutoincrementEntity $resetAutoincrementEntity
    ) {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->resetAutoincrementEntity = $resetAutoincrementEntity;
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->resetAutoincrementEntity->resetAutoincrement('user');

        $faker = Factory::create('fr_FR');
        $consultantUser = [];
        for ($i = 0; $i < 15; $i++) {
            $consultantUser[$i] = new User();
            $consultantUser[$i]->setFullName($faker->name);
            $consultantUser[$i]->setEmail($faker->safeEmail);
            $consultantUser[$i]->setIsEnable((bool) rand(0, 1));
            $consultantUser[$i]->setRoles([User::ROLE_CONSULTANT]);

            $consultantUser[$i]->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $consultantUser[$i],
                    'test1234'
                )
            );

            $manager->persist($consultantUser[$i]);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 10; // smaller means sooner
    }
}
