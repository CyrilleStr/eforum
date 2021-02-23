<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/show/{id}", name="show_user")
     */

    public function userPage($id){
        $user = new User;
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);
        return $this->render('user/show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/list/{filter}", name="list_users")
     */

    public function list($filter="none"){
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = array();

        switch($filter){
            case "name":
                break;
            default:
            $users = $repo->findAll();
            break;
        }

        if(sizeof($users)>10){
            $users = array_slice($users,0,10);
        }

        return $this->render('user/list.html.twig', [
            'users' => $users
        ]);
    }
    /**
     * @Route("/user/follow/{id}", name="user_follow")
     */
    public function userFollow(User $userFollowed, EntityManagerInterface $manager, UserRepository $userRepo){
        $user = $this->getUser();
        
        if(!$user) return $this->json([
            'code' => 403,
            'message' => "Unauthorized"
        ],403);

        if($userFollowed == null) return $this->json([
            'code' => 406,
            'message' => "Followed user can not be found"
        ],403);

        if($userFollowed === $user) return $this->json([
            'code' => 406,
            'message' => "Followed user can not be himself"
        ],403);

        if($user->isUserFollowed($userFollowed)){
            $user->removeUsersFollowed($userFollowed);
            $manager->persist($user);
            $manager->flush();
            return $this->json([
                'code' => 200,
                'message' => "Relation deleted succesfully",
                'state' => 0
            ],200);
        }else{
            $user->addUsersFollowed($userFollowed);
            $manager->persist($user);
            $manager->flush();
            return $this->json([
                'code' => 200,
                'message' => "New relation set succesfully",
                'state' => 1
            ],200);
        }
    }
}
