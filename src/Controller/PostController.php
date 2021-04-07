<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\CommentRate;
use APP\Entity\Notif;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use OutOfRangeException;
use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\ElseIf_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/post/create", name="create_post")
     */
    public function create(Request $request, EntityManagerInterface $manager, PostRepository $postRepo) {

        $user = $this->getUser();
        if($user == null) return $this->redirectToRoute('app_login');

        $post = new Post();
        $form  = $this->createForm(PostType::class,$post);
        $editMode = false;

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // Create post
            $post->setCreationDate(new \DateTime);
            $post->setStatus(0);
            $post->setAuthor($this->getUser());
            $manager->persist($post);
            $manager->flush();

            // Send notifs
            
            $followers = $user->getUsersFollower();

            if($followers != null) {
                $postId = $postRepo->find($post)->getId();
                $userFirstName = $user->getFirstName();
                $userLastName = $user->getLastName();

                foreach ($followers as $follower) {
                    $notif = new Notif();
                    $notif->setMsg( $userFirstName . ' ' . $userLastName . " a publié un nouveau post !");
                    $notif->setLink("/post/show/" . $postId);
                    $manager->persist($notif);
                    $follower->addNotif($notif);
                    $manager->persist($follower);
                }
                $manager->flush();
            }

            return $this->redirectToRoute('show_post',  ['id' => $post->getId()]);
        }
        
        return $this->render('post/create.html.twig', [
            'formPost' => $form->createView(),
            'editMode' => $editMode
        ]);
    }

    /**
     * @Route("/post/show/{id}", name="show_post")
     */

     public function show($id, PostRepository $postRepo){

        $post = $postRepo->find($id);

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
      * @Route("/post/delete/{id}", name="delete_post")
      */

    public function deletePost(Post $post, EntityManagerInterface $manager) {
        $user = $this->getUser();
        if(!$user) return $this->redirectToRoute('app_login');

        if($post->getAuthor() != $user) return $this->json([
            'code' => 403,
            'message' => "Unauthorized"
        ],403);

        try {
            $manager->remove($post);
            $manager->flush();
        } catch(Exception $e) {
            return $this->json([
                'code' => 403,
                'message' => print_r($e)
            ],403);
        }

        return $this->json([
            'code' => 200,
            'message' => "Post succesfully removed"
        ],200);
        
    }

     /**
     * @Route("/post/list/{startAt}/{catName}/{subCatName}/{orderBy}/{onlyPost}", name="list_posts",  defaults={"startAt": 0, "subCatName": "all", "catName": "all", "orderBy":"none", "onlyPost":"false"}))
     */

    public function list($startAt, $catName, $subCatName, $orderBy, $onlyPost, PostRepository $repoPost, CategoryRepository $repoCat){
        $posts = array();
        $more = false;
        $pageCapacity = 10;

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
                // Implement error
                $orderBySQL = ['creationDate'=>"DESC"];
        }
    
        if($catName == "all"){
            $categorySQL = [];
        } else if($subCatName == "all"){
            $category = $repoCat->findBy(['catName' => $catName]);
            if($category == null){
                // Implement error
                echo "error category name not found";
                die;
            }
            $categorySQL = ['category' => $category];
        } else {
            $category = $repoCat->findBy(['subCatName' => $subCatName , 'catName' => $catName]);
            if($category == null){
                // Implement error
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
                'catName' => $catName,
                'subCatName' => $subCatName
            ]);

        }else{
            if(count($posts)>$pageCapacity){
                $posts = array_slice($posts,0,$pageCapacity);
                $more = true;
            }
    
            return $this->render('post/list.html.twig',[
                'posts' => $posts,
                'size'=>  $size,
                'more' => $more,
                'orderBy' => $orderBy,
                'catName' => $catName,
                'subCatName' => $subCatName
            ]);
        }

        
    }

}