<?php

namespace App\Form;

use App\Entity\Contact;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label' =>'Prénom'])
            ->add('lastName', TextType::class, ['label' =>'Nom de famille'])
            ->add('email', EmailType::class, ['label' =>'email'])
            ->add('subject', TextType::class, ['label' =>'Sujet'])
            ->add('message', CKEditorType::class, ['label' =>'Message'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
