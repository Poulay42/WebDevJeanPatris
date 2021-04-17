<?php

namespace App\DataFixtures;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $slugify = new Slugify();
        $user = new User();

        $user->setFirstName('Jean');
        $user->setLastName(('Patris'));
        $user->setImage('default.png');
        $user->setUsername('Poulay');
        $user->setEmail('thepoulay@gmail.com');
        $password = $this->encoder->encodePassword($user, 'password');

        $user->setPassword($password);
        $user->setSlug($slugify->slugify($user->getUsername()));

        $user->setRoles(array_unique(['ROLE_SUPER_ADMIN']));

        $manager->persist($user);

        for($i = 1; $i <= 30; $i++)
        {
            $user = new User();

            $user->setFirstName($faker->firstName);
            $user->setLastName(($faker->lastName));
            $user->setImage('default.png');
            $user->setUsername($user->getFirstName().$user->getLastName());
            $user->setEmail($user->getFirstName().'.'.$user->getLastName().'@gmail.com');
            $password = $this->encoder->encodePassword($user, 'password');

            $user->setPassword($password);
            $user->setSlug($slugify->slugify($user->getUsername()));

            $user->setRoles(array_unique(['ROLE_USER']));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
