<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => 'Nom d\'utilisateur'])
            ->add('password', PasswordType::class, ['label' => 'Votre mot de passe'])
            ->add('firstName', TextType::class, ['label' => 'Votre prénom'])
            ->add('lastName', TextType::class, ['label' => 'Votre nom'])
            ->add('Email', EmailType::class, ['label' => 'Votre email'])
            ->add('imageFile', VichImageType::class, ['label' => 'Votre image', 'required' => false, 'attr' => ['placeholder' => 'image'] ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
