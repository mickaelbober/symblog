<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Avatar;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\User;
use App\Entity\View;
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

        $avatars = new ArrayCollection();
        for ($i = 0; $i < 10; $i++) {
            $avatar = new Avatar();
            $avatar->setTitle($faker->sentence(1, false))
                ->setImage($faker->imageUrl(80, 80))
                ->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $avatars->add($avatar);
            $manager->persist($avatar);
        }

        $users = new ArrayCollection();
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $password = $this->encoder->encodePassword($user, $faker->password);
            $user->setEmail($faker->email)
                ->setUsername($faker->userName)
                ->setPassword($password)
                ->setAvatar($avatars->get(mt_rand(0, 9)));
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
                ->setUser($users->get(mt_rand(0, 9)))
                ->setView($faker->numberBetween(0, 2000));
            $articles->add($article);
            $manager->persist($article);
        }

        $comments = new ArrayCollection();
        for ($i = 0; $i < 600; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->paragraph())
                ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                ->setUser($users->get(mt_rand(0, 9)))
                ->setArticle($articles->get(mt_rand(0, 59)));
            $comments->add($comment);
            $manager->persist($comment);
        }

        $likes = new ArrayCollection();
        for ($i = 0; $i < 180; $i++) {
            $like = new Like();
            $like->setArticle($articles->get(mt_rand(0, 59)))
                ->setUser($users->get(mt_rand(0, 9)));
            $likes->add($like);
            $manager->persist($like);
        }

        $views = new ArrayCollection();
        for ($i = 0; $i < 180; $i++) {
            $view = new View();
            $view->setArticle($articles->get(mt_rand(0, 59)))
                ->setUser($users->get(mt_rand(0, 9)));
            $views->add($view);
            $manager->persist($view);
        }

        $manager->flush();
    }
}
