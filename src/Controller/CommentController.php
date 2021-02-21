<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentRate;
use App\Entity\Post;
use App\Repository\CommentRateRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="comment")
     */
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /**
     * @Route("/comment/create/{id}", name="create_comment")
     */
    
    public function create(Post $post, EntityManagerInterface $manager){

        $user = $this->getUser();

        if (!$user) return $this->json([
            'code' => 403,
            'message' => 'Unauthorized'
        ],403);

        if(array_key_exists('content',$_POST)){
            $comment = new Comment();
            $now = new \DateTime;
            $comment->setCreationDate($now);
            $comment->setAuthor($user);
            $comment->setContent($_POST['content']);
            $comment->setPost($post);
            $manager->persist($comment);
            $manager->flush();
            $creationDate = $now->format('d/m/Y à H:i');
            return $this->json([
                'code' => 200,
                'message' => "Comment succesfully added",
                'id' => $comment->getId(),
                'creationDate' => $creationDate
            ],200);            
        }else{
            return $this->json([
            'code' => 403,
            'message' => 'Content field is empty'
            ],403);
        }
    }
    
    /**
     * @Route("/comment/delete/{id}", name="delete_comment")
     */

    public function delete(Comment $comment, EntityManagerInterface $manager, CommentRepository $commentRepo){
        $user = $this->getUser();

        if(!$user) return $this->json([
            'code' => 403,
            'message' => "Unauthorized"
        ],403);

        if(isset($comment)){
            $tmp = $user->getcomments();
            
            $usersComments = $commentRepo->findBy(['author' => $user, 'id' => $comment->getId()]);
            
            if($usersComments != null){
                $manager->remove($comment);
                $manager->flush();
                return $this->json([
                    'code' => 200,
                    'message' => "Comment removed succesfully"
                ]);
            }else{
                return $this->json([
                    'code' => 403,
                    'message' => "Unauthorized"
                ],403);
            }
        }else{
            return $this->json([
                'code' => 403,
                'message' => "Comment's id not specified"
            ],403);
        }

    }

    /**
     * Rate up down a commment
     * @Route("/comment/uprate/{id}", name="comment_uprate")
     *
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function uprate(Comment $comment, EntityManagerInterface $manager,CommentRateRepository $repoCommentRate): Response {
        $user = $this->getUser();

        if(!$user) return $this->json([
            'code' => 403,
            'message' => 'Unauthorized'
        ],403);
        $sum = (int) 0;
        if($comment->isRatedUp($user)){
            $commentRate = $repoCommentRate->findBy([
                'user' => $user,
                'comment' => $comment
            ]);

            $manager->remove($commentRate[0]);
            $manager->flush();

            foreach($repoCommentRate->findBy(['comment' => $comment]) as $commentRate){
                $sum += $commentRate->getNote();
            }

            return $this->json([
                'code' => 200,
                'message' => 'Uprate removed on comment',
                'rates' => $sum
            ],200);
        }

        $commentRate = new CommentRate();
        $commentRate->setComment($comment)
                    ->setUser($user)
                    ->setNote(1);
            
        $manager->persist($commentRate);
        $manager->flush();

        foreach($repoCommentRate->findBy(['comment' => $comment]) as $commentRate){
            $sum += $commentRate->getNote();
        }

        return $this->json([
            'code' => 200, 
            'message' => 'Uprate added on comment',
            'rates' => $sum
        ],200);
    }

    /**
     * Rate down a comment
     * @Route("/comment/downrate/{id}", name="comment_downrate")
     *
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function downrate(Comment $comment, EntityManagerInterface $manager,CommentRateRepository $repoCommentRate): Response {
        $user = $this->getUser();   

        if(!$user) return $this->json([
            'code' => 403,
            'message' => 'Unauthorized'
        ],403);

        $sum = (int) 0;

        if($comment->isRatedDown($user)){
            $commentRate = $repoCommentRate->findBy([
                'user' => $user,
                'comment' => $comment
            ]);
            $manager->remove($commentRate[0]);
            $manager->flush();

            foreach($repoCommentRate->findBy(['comment' => $comment]) as $commentRate){
                $sum += $commentRate->getNote();
            }
            
            return $this->json([
                'code' => 200,
                'message' => 'Downrate removed on comment',
                'rates' => $sum
            ],200);
        }

        $commentRate = new CommentRate();
        $commentRate->setComment($comment)
                    ->setUser($user)
                    ->setNote(-1);
            
        $manager->persist($commentRate);
        $manager->flush();

        foreach($repoCommentRate->findBy(['comment' => $comment]) as $commentRate){
            $sum += $commentRate->getNote();
        }

        return $this->json([
            'code' => 200, 
            'message' => 'Downrate added on comment',
            'rates' => $sum
        ],200);
     }
}
