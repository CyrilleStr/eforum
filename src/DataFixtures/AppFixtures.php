<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\CommentRate;
use App\Entity\Post;
use App\Entity\Type;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\TypeRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $manager->flush();
    }
}
