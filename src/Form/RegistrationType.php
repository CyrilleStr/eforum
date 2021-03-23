<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class,[
                'label' =>"Prénom"
            ])
            ->add('lastName',TextType::class,[
                'label' =>"Nom de famille"
            ])
            ->add('email',TextType::class,[
                'label' =>"Prénom"
            ])
            ->add('activity',TextareaType::class,[
                'label' =>"Activité"
            ])
            ->add('password',PasswordType::class,[
                'label' =>"Mot de passe"
            ])
            ->add('confirm_password',PasswordType::class,[
                'label' =>"Confirmez le mot de passe"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
