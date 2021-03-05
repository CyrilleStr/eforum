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
use OutOfRangeException;
use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node\Stmt\Break_;
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
     * @Route("/post/list/{startAt}/{categoryName}/{orderBy}/{onlyPost}", name="list_posts",  defaults={"startAt": 0, "categoryName": "all", "orderBy":"none", "onlyPost":"false"}))
     */

    public function list($startAt, $categoryName, $orderBy, $onlyPost, PostRepository $repoPost, CategoryRepository $repoCat){
        $posts = array();
        $more = false;
        $pageCapacity = 3;

        switch($orderBy){
            case "none":
                $orderBySQL = ['creationDate'=>"DESC"];
                break;
            case "dateAsc":
                $orderBySQL = ['creationDate'=>"ASC"];
                break;
            case "dateDesc";
                $orderBySQL = ['creationDate'=>"DESC"];
                break;
            case "titleAsc";
                $orderBySQL = ['title'=>"ASC"];
                break;
            case "titleDesc";
                $orderBySQL = ['title'=>"DESC"];
                break;
            case "statusOpen":
                $orderBySQL = ['status'=>"ASC"];
                break;
            case "statusClose":
                $orderBySQL = ['status'=>"DESC"];
                break;
            default:
                $orderBySQL = ['creationDate'=>"DESC"];
        }
    
        switch($categoryName){
            case "all":
                $categorySQL = [];
                break;
            default:
                $category = $repoCat->findBy(['name' => $categoryName]);
                if($category == null){
                    echo "error category name not found";
                    die;
                }
                $categorySQL = ['category' => $category];
        }

        $posts = $repoPost->findBy($categorySQL,$orderBySQL);

        $size = count($posts);

        if($onlyPost == "true"){
            $posts = array_slice($posts,$startAt);
            if(sizeof($posts)>$pageCapacity){
                $posts = array_slice($posts,0,$pageCapacity);
                $more = true;
            }
            
            return $this->render('post/showMore.html.twig',[
                'posts' => $posts,
                'more' => $more,
                'printedPost' => $startAt + $pageCapacity,
                'orderBy' => $orderBy,
                'categoryName' => $categoryName
            ]);

        }else{
            if(count($posts)>$pageCapacity){
                $posts = array_slice($posts,0,$pageCapacity);
                $more = true;
            }
    
            return $this->render('post/list.html.twig',[
                'posts' => $posts,
                'size'=>  $size,
                'category' => $categoryName,
                'more' => $more,
                'orderBy' => $orderBy,
                'categoryName' => $categoryName
            ]);
        }

        
    }

}