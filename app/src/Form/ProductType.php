<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use App\Form\PicturesType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du produit',
                'attr' => [
                    'placeholder' => 'Ex: Ballon de football professionnel',
                    'class' => 'form-input'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un titre pour le produit',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 50,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
                'attr' => [
                    'placeholder' => '29.99',
                    'class' => 'form-input'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un prix',
                    ]),
                    new Positive([
                        'message' => 'Le prix doit être positif',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description détaillée du produit...',
                    'class' => 'form-input',
                    'rows' => 4
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('notice', TextareaType::class, [
                'label' => 'Notice / Instructions',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Instructions d\'utilisation, conseils...',
                    'class' => 'form-input',
                    'rows' => 3
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'La notice ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('pictures', FileType::class, [
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
} 