<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\CommentRate;
use App\Entity\Post;
use App\Entity\Type;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\TypeRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
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
        $users = [];
        for ($i=0; $i <10 ; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setActivity($faker->paragraph(5));
            $user->setPassword($this->encoder->encodePassword($user,"123"));
            $user->setAccountCreationDate(new \DateTime);
            $manager->persist($user);
            $users[] = $user;
        }
        
        $nameCat = ["Placements","e-Buisness","Entreprise"];
        $categories = [];
        for ($i=0; $i < 3; $i++) { 
            $category = new Category();
            $category->setName($nameCat[$i]);
            $manager->persist($category);
            $categories[] = $category;
        }

        $nameType = ["Question","Débat","Divers"];
        $types = [];
        for ($i=0; $i < 3; $i++) { 
            $type = new Type();
            $type->setName($nameType[$i]);
            $manager->persist($type);
            $types[] = $type;
        }

//         for ($i=0; $i < 10; $i++) { 
//             $post = new Post();
//             $post->setAuthor($faker->randomElement($users));
//             $post->setTitle($faker->sentence(4));
//             $post->setStatus($faker->randomElement([0,1]));
//             $post->setCreationDate($faker->dateTimeBetween('-6 months')); 
//             $post->setDescription($faker->paragraphs(3,true));
//             $post->setCategory($faker->randomElement($categories));
//             $post->setType($faker->randomElement($types));
//             $manager->persist($post);

//             for ($i=0; $i < 5; $i++) { 
//                 $comment = new Comment();
//                 $comment->setAuthor($faker->randomElement($users));
//                 $comment->setContent($faker->sentence(10));
//                 $comment->setCreationDate($faker->dateTimeBetween('-6 months'));
//                 $comment->setPost($post);
//                 $manager->persist($comment);

//                 for ($i=0; $i < 10; $i++) { 
//                     $commentRate = new CommentRate();
//                     $commentRate->setComment($comment);
//                     $commentRate->setNote($faker->randomElement([-1,1]));
//                     $commentRate->setUser($faker->randomElement($users));
//                     $manager->persist($commentRate);
//                 }

//             }
//         }

        $manager->flush();
    }
}
