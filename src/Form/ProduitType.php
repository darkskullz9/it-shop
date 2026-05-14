<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation', TextType::class, [
                'label' => 'Product name',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Product name...'],
                'label_attr' => ['class' => 'form-label'],
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Description...'],
                'label_attr' => ['class' => 'form-label'],
            ])

            ->add('prix', MoneyType::class, [
                'label' => 'Price',
                'currency' => 'EUR',
                'html5' => false,
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
            ])

            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'designation',
                'placeholder' => 'Choose a category',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
            ])

            ->add('imageFile', FileType::class, [
                'label' => 'Product\'s picture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'mimeTypesMessage' => 'Please, upload a valid picture (jpg, png, webp)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
