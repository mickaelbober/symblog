<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $users = new ArrayCollection();
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, $faker->password);
            $user->setEmail($faker->email)
                ->setUsername($faker->userName)
                ->setPassword($password);
            $users->add($user);
            $manager->persist($user);
        }

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
                ->setCategory($categories->get(mt_rand(0, 9)))
                ->setUser($users->get(mt_rand(0, 9)));
            $articles->add($article);
            $manager->persist($article);
        }

        $manager->flush();
    }
}
