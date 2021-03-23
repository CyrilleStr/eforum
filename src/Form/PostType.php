<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,[
                'label' => " "
            ])
            ->add('category',EntityType::class,[
                'label' => "Categorie",
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('type',EntityType::class,[
                'label' => "Type",
                'class' => Type::class,
                'choice_label' => 'name'
            ])
            ->add('description',TextareaType::class,[
                'label' => " "
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
