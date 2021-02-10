<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Category;
use App\Entity\Type;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Catch_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/post/create", name="create_post")
     */
    public function create(Request $request, EntityManagerInterface $manager)    {
        $post = new Post();
        $form  = $this->createForm(PostType::class,$post);
        $editMode = false;

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $post->setCreationDate(new \DateTime);
            $post->setStatus(0);
            $post->setAuthor($this->getUser());
            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('home');
        }
        
        return $this->render('post/create.html.twig', [
            'formPost' => $form->createView(),
            'editMode' => $editMode
        ]);
    }

    /**
     * @Route("/post/show/{id}", name="show_post")
     */

     public function show($id){
        $repo = $this->getDoctrine()->getRepository(Post::class);
        $post = $repo->find($id);
        return $this->render('post/show.html.twig',[
            'post' => $post 
        ]);
     }

     /**
     * @Route("/post/list/{filter}", name="list_posts")
     */

    public function list($filter = "none"){
        $repoPost = $this->getDoctrine()->getRepository(Post::class);     
        $posts = array();
    
        switch($filter){
            case "asc":
                // ...
                break;
            case "none":
                $posts = $repoPost->findAll();
                break;
            default:
            // a category filter 
            $repoCat = $this->getDoctrine()->getRepository(Category::class);
            $categories = $repoCat->findAll();
            foreach($categories as $category){
                if(strtolower($category->getName()) == strtolower($filter)){
                    $posts = $repoPost->findBy(
                        ['category' => $category],
                        ['title' => 'ASC']
                    );
                    break;
                }
            }
            
            break;
        }

        if(sizeof($posts)>10){
            $posts = array_slice($posts,0,10);
        }

        return $this->render('post/list.html.twig',[
            'posts' => $posts
        ]);
     }
}