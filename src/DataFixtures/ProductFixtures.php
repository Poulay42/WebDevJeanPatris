<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Commentary;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create(); //Instanciation de Faker
        $categories = $manager->getRepository(Category::class)->findAll(); // Récupération des objets categories
        $users = $manager->getRepository(User::class)->findAll();

        $slugify = new Slugify();
        for($i = 1; $i <= 30; $i++)
        {
            $prod = new Product();
            $prod->setName($faker->words(3, true))
                ->setDescription($faker->paragraphs(3, true))
                ->setDateAdd($faker->dateTimeBetween('-30 days', 'now'))
                ->setCategory($categories[$faker->numberBetween(0, count($categories)-1)])
                ->setImage($i.'.png')
                ->setPrice($faker->numberBetween(1,100))
                ->setDiscount($faker->numberBetween(0,90))
                ->setDateUpdate($faker->dateTimeBetween('now'))
                ->setSlug($slugify->slugify($prod->getName()));
            $manager->persist($prod);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    }
}
