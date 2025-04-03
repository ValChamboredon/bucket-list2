<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORIES = [
        'Travel & Adventure',
        'Sport',
        'Entertainment',
        'Human Relations',
        'Others'
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $index => $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);


            $this->addReference('category-' . $index, $category);
        }

        $manager->flush();
    }




}

