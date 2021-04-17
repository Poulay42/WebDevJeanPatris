<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, array(
                'type'              => PasswordType::class,
                'mapped'            => false,
                'first_options'     => array('label' => 'New password'),
                'second_options'    => array('label' => 'Confirm new password'),
                'invalid_message' => 'The password fields must match.',
                'required' => false
            ))
            ->add('Email', EmailType::class)
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Image',
                ],
                'download_label' => '',
                'image_uri' => '',
                'allow_delete' => false
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
