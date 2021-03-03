<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\CommentRate;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
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
            return $this->redirectToRoute('show_post',['id' => $post->getId()]);
        }
        
        return $this->render('post/create.html.twig', [
            'formPost' => $form->createView(),
            'editMode' => $editMode
        ]);
    }

    /**
     * @Route("/post/show/{id}", name="show_post")
     */

     public function show($id, Request $request,EntityManagerInterface $manager){

        // Get post
        $repoPost = $this->getDoctrine()->getRepository(Post::class);
        $post = $repoPost->find($id);

        // Get comments       
        $repoComment = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repoComment->findBy(
            ['post' => $post],
            ['creation_date' => 'DESC']
        );


        return $this->render('post/show.html.twig',[
            'post' => $post,
            'comments' => $comments
        ]);
     }

     /**
     * @Route("/post/list/{cat}", name="list_posts")
     */

    public function list($cat = "all", PostRepository $repoPost, CategoryRepository $repoCat){
        $posts = array();
    
        switch($cat){
            case "all":
                $posts = $repoPost->findAll();
                break;
            default:
            $repoCat = $this->getDoctrine()->getRepository(Category::class);
            $categories = $repoCat->findAll();
            foreach($categories as $category){
                if(strtolower($category->getName()) == strtolower($cat)){
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