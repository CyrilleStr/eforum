<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\CommentRate;
use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/new_type", name="new_type")
     */
    public function addType(EntityManagerInterface $manager){
        $type = new Type;
        $type->setName("Divers");
        $manager->persist($type);
        $manager->flush();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/new_category", name="new_category")
     */
    public function addCategory(EntityManagerInterface $manager){
        $category = new Category;
        $category->setName("Entreprise");
        $manager->persist($category);
        $manager->flush();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/test", name="test")
     */

    public function test(){
        $repoCommentRate = $this->getDoctrine()->getRepository(CommentRate::class);
        $a = $repoCommentRate->find(80);
        


        return $this->render('main/test.html.twig', [
            'controller_name' => 'test',
        ]);
    }     
}
