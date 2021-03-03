<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\CommentRate;
use App\Entity\Post;
use App\Entity\Type;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/new_type", name="new_type")
     */
    public function addType(EntityManagerInterface $manager){
        $type = new Type;
        $type->setName("Divers");
        $manager->persist($type);
        $manager->flush();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/new_category", name="new_category")
     */
    public function addCategory(EntityManagerInterface $manager){
        $category = new Category;
        $category->setName("Entreprise");
        $manager->persist($category);
        $manager->flush();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/test", name="test")
     */

    public function test(EntityManagerInterface $manager){
        echo '<pre>';
        var_dump($a);
        echo '</pre>';

        die;


        return $this->render('main/test.html.twig', [
            'controller_name' => 'test',
        ]);
    }

    /**
     * @Route("/fixtures", name="fixtures") 
     */
    public function fixtures(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        $faker = \Faker\Factory::create('fr_FR');
        $users = [];
        for ($i=0; $i <10 ; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setActivity($faker->paragraph(5));
            $user->setPassword($encoder->encodePassword($user,"123"));
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

        for ($i=0; $i < 10; $i++) { 
            $post = new Post();
            $post->setAuthor($faker->randomElement($users));
            $post->setTitle($faker->sentence(4));
            $post->setStatus($faker->randomElement([0,1]));
            $post->setCreationDate($faker->dateTimeBetween('-6 months')); 
            $post->setDescription($faker->paragraphs(3,true));
            $post->setCategory($faker->randomElement($categories));
            $post->setType($faker->randomElement($types));
            $manager->persist($post);

            for ($j=0; $j < mt_rand(0,10); $j++) { 
                $comment = new Comment();
                $comment->setAuthor($faker->randomElement($users));
                $comment->setContent($faker->sentence(10));
                $comment->setCreationDate($faker->dateTimeBetween('-6 months'));
                $comment->setPost($post);
                $manager->persist($comment);

                for ($k=0; $k < mt_rand(0,10); $k++) { 
                    $commentRate = new CommentRate();
                    $commentRate->setComment($comment);
                    $commentRate->setNote($faker->randomElement([-1,1]));
                    $commentRate->setUser($faker->randomElement($users));
                    $manager->persist($commentRate);
                }
            }
        }

        $manager->flush();
        echo 'finish';
        die;
    }
}
