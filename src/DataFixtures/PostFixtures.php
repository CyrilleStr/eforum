<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Type;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        $user = new User;
        $user->setId(25);
        for($i=0;$i<5;$i++){
            $category = new Category;
            $category->setId(1);
            $type = new Type;
            $type->setId(1);
            $post = new Post;
            $post->setTitle($faker->sentence(5));
            $post->setType($type);
            $post->setCreationDate($faker->dateTimeBetween('- 6 months'));
            $post->setDescription($faker->sentence(5));
            $post->setStatus(0);
            $post->setAuthor($user);
            $post->setCategory($category);
            $manager->persist($post);
        }
        for($i=0;$i<5;$i++){
            $category = new Category;
            $category->setId(2);
            $type = new Type;
            $type->setId(2);
            $post = new Post;
            $post->setTitle($faker->sentence(5));
            $post->setType($type);
            $post->setCreationDate($faker->dateTimeBetween('- 12 months'));
            $post->setDescription($faker->sentence(5));
            $post->setStatus(0);
            $post->setAuthor($user);
            $post->setCategory($category);
            $manager->persist($post);
        }
        for($i=0;$i<5;$i++){
            $category = new Category;
            $category->setId(3);
            $type = new Type;
            $type->setId(3);
            $post = new Post;
            $post->setTitle($faker->sentence(5));
            $post->setCreationDate($faker->dateTimeBetween('- 6 months'));
            $post->setDescription($faker->sentence(5));
            $post->setStatus(0);
            $post->setAuthor($user);
            $post->setCategory($category);
            $post->setType($type);
            $manager->persist($post);
        }

        $manager->flush();
    }
}