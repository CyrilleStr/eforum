<?php

namespace App\Form;

use App\Entity\CommentRate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentRateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('utile', ButtonType::class, [
                'attr' => ['class' => 'utile'],
            ])
            ->add('inutile', ButtonType::class, [
                'attr' => ['class' => 'inutile'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CommentRate::class,
        ]);
    }
}
