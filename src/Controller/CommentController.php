<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentRate;
use App\Repository\CommentRateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * Rate up down a commment
     * @Route("/post/show/{id}/uprateComment", name="comment_uprate")
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
                'rates' => $sum,
                'state' => 0
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
            'rates' => $sum,
            'state' => 1
        ],200);
    }

    /**
     * Rate down a comment
     * @Route("/post/show/{id}/downrateComment", name="comment_downrate")
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
                'rates' => $sum,
                'state' => 1
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
            'rates' => $sum,
            'state' => 0
        ],200);
     }
}
