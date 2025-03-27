<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WishFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $wish = new Wish();
            $wish->setTitle($faker->sentence);
            $wish->setDescription($faker->paragraph);
            $wish->setAuthor($faker->name);
            $wish->setIsPublished($faker->boolean);
            $wish->setDateCreated($faker->dateTimeBetween('-1 year', 'now'));

            $manager->persist($wish);
        }

        $manager->flush();
    }
}
