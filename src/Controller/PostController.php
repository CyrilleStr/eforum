<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\CommentRate;
use App\Form\CommentType;
use App\Form\PostType;
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
            'formCommentPost' => $form->createView(),
            'editMode' => $editMode
        ]);
    }

    /**
     * @Route("/post/show/{id}", name="show_post")
     */

     public function show($id, Request $request,EntityManagerInterface $manager){

        // Authentification Check
        $securityContext = $this->container->get('security.authorization_checker');

        // Get post
        $repoPost = $this->getDoctrine()->getRepository(Post::class);
        $post = $repoPost->find($id);

        // New comment
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class,$comment);
        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid()){
            $comment->setCreationDate(new \DateTime);
            $comment->setAuthor($this->getUser());
            $comment->setPost($post);
            $manager->persist($comment);
            $manager->flush();
        }

        // Get comments       
        $repoComment = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repoComment->findBy(
            ['post' => $post],
            ['creation_date' => 'DESC']
        );


        return $this->render('post/show.html.twig',[
            'formComment' => $formComment->createView(),
            'post' => $post,
            'comments' => $comments
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

    


//  // New Comment Rate

//  if($securityContext->isGranted('IS_AUTHENTICATED_FULLY') && (isset($_POST['utile']) || isset($_POST['inutile']))){
//     if(isset($_POST['utile'])){
//         foreach($comments as $comment) {
//             if ($comment->getId() == $_POST['utile']) {
//                 $comment1 = $comment;
//                 break;
//             }
//         }
//         $commentRate = new CommentRate();
//         $commentRate->setComment($comment1);
//         $commentRate->setUser($this->getUser());
//         $commentRate->setNote(1);
//     }else{
//         foreach($comments as $comment) {
//             if ($comment->getId() == ($_POST['inutile'] )) {
//                 $comment1 = $comment;
//                 break;
//             }
//         }
//         $commentRate = new CommentRate();
//         $commentRate->setComment($comment1);
//         $commentRate->setUser($this->getUser());
//         $commentRate->setNote(-1);
//     }
//     $manager->persist($commentRate);
//     $manager->flush();
// }

// // Get commetRate's comment

// $repoCommentRate = $this->getDoctrine()->getRepository(CommentRate::class);
// $commentsRate = array();

// echo '<pre>';
// var_dump($repoCommentRate->findBy(
//     ['comment' => $comments[0]]
// ));
// echo '</pre>';
// die;