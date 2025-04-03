<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WishFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $wish = new Wish();
            $wish->setTitle($faker->sentence);
            $wish->setDescription($faker->paragraph);
            $wish->setIsPublished($faker->boolean());
            $wish->setDateCreated(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));


            $category = $this->getReference('category-' . mt_rand(0, 4),Category::class);
            $wish->setCategory($category);

            $manager->persist($wish);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }

}
