<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Notif;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotifController extends AbstractController
{
    #[Route('/notif', name: 'notif')]
    public function index(): Response
    {
        return $this->render('notif/index.html.twig', [
            'controller_name' => 'NotifController',
        ]);
    }

    #[Route('/notif/get', name: 'get_notif')]
    public function getNotif() {
        
        $user = $this->getUser();

        if (!$user) return $this->json([
            'code' => 403,
            'message' => 'Unauthorized'
        ],403);

        $notifs = $user->getNotifs();
        $msg = [];
        $link = [];
        $id = [];
        foreach($notifs as $notif) {
            $msg[] = $notif->getMsg();
            $link[] = $notif->getLink();
            $id[] = $notif->getId();
        }
        $response = [$msg,$link,$id];
        return new JsonResponse($response);
    }
    
    #[Route('/notif/delete/{id}', name: 'delete_notif')]
    public function deleteNotif(Notif $notif, EntityManagerInterface $manager) {
        $user = $this->getUser();
        if (!$user) return $this->json([
            'code' => 403,
            'message' => 'Unauthorized'
        ],403);
        
        if(!$notif) return $this->json([
            'code' => 403,
            'message' => "Notification inexistant or already removed"
        ],403);
        
        $manager->remove($notif);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => "Notification removed succesfully"
        ],200);
    }
}
