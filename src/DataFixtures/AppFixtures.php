<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $categories = new ArrayCollection();
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence(2, false))
                ->setDescription($faker->paragraph())
                ->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $categories->add($category);
            $manager->persist($category);
        }

        $articles = new ArrayCollection();
        for ($i = 0; $i < 60; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence(2, false))
                ->setContent($faker->text(600))
                ->setImage($faker->imageUrl(1280, 800))
                ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                ->setCategory($categories->get(mt_rand(0, 9)));
            $articles->add($article);
            $manager->persist($article);
        }

        $manager->flush();
    }
}
