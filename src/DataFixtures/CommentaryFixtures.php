<?php

namespace App\DataFixtures;

use App\Entity\Commentary;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentaryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create(); //Instanciation de Faker
        $users = $manager->getRepository(User::class)->findAll();
        $products = $manager->getRepository(Product::class)->findAll();

        for($i = 1; $i <= 2000; $i++)
        {
            $com = new Commentary();

            $com->setText($faker->paragraphs(3, true));
            $com->setProduct($products[$faker->numberBetween(0, count($products)-1)]);
            $com->setUser($users[$faker->numberBetween(0, count($users)-1)]);
            $com->setCreatedAt($faker->dateTimeBetween('-30 days', 'now'));

            $manager->persist($com);
        }

        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class,
            ProductFixtures::class
        ];
    }
}
