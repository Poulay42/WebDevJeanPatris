<?php

namespace App\Form;

use App\Entity\Product;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('price', FloatType::class, ['label' => 'Prix'])
            ->add('imageFile', VichImageType::class, ['label' => 'Votre image', 'required' => false, 'attr' => ['placeholder' => 'image'] ])
            ->add('discount', IntegerType::class, ['label' => 'Réduction'])
            ->add('description', TextareaType::class, ['label' => 'Votre contenu'])
            ->add('category', EntityType::class, ['label' => 'Sélectionnez une catégorie', 'placeholder' => 'Sélectionnez...', 'class' => 'App:Category', 'choice_label' => 'name'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
