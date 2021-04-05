<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\CommentRate;
use App\Entity\Post;
use App\Entity\Type;
use App\Entity\User;
use App\Form\SearchPostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index($error = null): Response
    {
        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request, PostRepository $postRepo): Response
    {
        $posts = [];

        if(isset($_POST['key'])){
            $key = $_POST['key'];
            $posts = $postRepo->search($key);
        }else{
            $key = null;
        }

        return $this->render('main/search.html.twig', [
            'posts' => $posts,
            'key' => $key
        ]);
    } 


    /**
     * @Route("/test", name="test")
     */

    public function test(EntityManagerInterface $manager){
        echo '<pre>';
        // var_dump($a);
        echo '</pre>';

        die;

        return $this->render('main/test.html.twig', [
            'controller_name' => 'test',
        ]);
    }

    /**
     * @Route("/fixtures1", name="fixtures1") 
     */
    public function fixtures1(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
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

        $manager->flush();
        echo 'finish: now call fixtures2';
        die;
    }

    /**
     * @Route("/fixtures2", name="fixtures2") 
     */
    public function fixtures2(EntityManagerInterface $manager, UserRepository $userRepo, TypeRepository $typeRepo, CategoryRepository $categoryRepo){
        
        $faker = \Faker\Factory::create('fr_FR');
        $users = $userRepo->findAll();
        $types = $typeRepo->findAll();
        $categories = $categoryRepo->findAll();

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
