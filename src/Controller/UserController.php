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

    public function show($id){
        $user = new User;
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);
        if($this->getUser() == $user) return $this->redirectToRoute('my_account');

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
     * @Route("/user/my_account", name="my_account")
     */

    public function myAccount(){
        
        $user = $this->getUser();
        if(!$user) return $this->redirectToRoute('app_login');
        
        return $this->render('user/my_account.html.twig', [
            'user' => $user
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

    /**
     * @Route("/user/my_account/modify", name="modify_account")
     */

    public function modifyAccount(EntityManagerInterface $manager){
        
        $user = $this->getUser();
        if(!$user) return $this->redirectToRoute('app_login');

        if(array_key_exists('firstName',$_POST) && array_key_exists('lastName',$_POST) && array_key_exists('email',$_POST) && array_key_exists('activity',$_POST)){
            $user->setFirstName($_POST['firstName'])
                 ->setLastName($_POST['lastName'])
                 ->setEmail($_POST['email'])
                 ->setActivity($_POST['activity']);
            $manager->persist($user);
            $manager->flush();
            return $this->json([
                'code' => 200,
                'message' => "Accounts informations successfully modified"
            ],200);
        }else{
            return $this->json([
            'code' => 403,
            'message' => 'One or many fields are empty'
            ],403);
        }
    }
    
}
