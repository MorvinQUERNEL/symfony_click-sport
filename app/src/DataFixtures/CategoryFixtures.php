<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Football',
            'Basketball',
            'Tennis',
            'Golf',
            'Natation',
            'Fitness',
            'Running',
            'Vélo',
            'Escalade',
            'Yoga'
        ];

        foreach ($categories as $categoryName) {
            $category = new Categories();
            $category->setTitle($categoryName);
            $manager->persist($category);
        }

        $manager->flush();
    }
} 