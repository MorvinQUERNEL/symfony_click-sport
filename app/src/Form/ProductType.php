<?php

namespace App\Form;

use App\Entity\Products;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du produit *',
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
            ->add('price', NumberType::class, [
                'label' => 'Prix (€) *',
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'placeholder' => '29.99',
                    'class' => 'form-input',
                    'step' => '0.01',
                    'min' => '0'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un prix',
                    ]),
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Le prix doit être un nombre valide',
                    ]),
                    new Positive([
                        'message' => 'Le prix doit être positif',
                    ]),
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'title',
                'label' => 'Catégorie *',
                'placeholder' => 'Sélectionnez une catégorie',
                'attr' => [
                    'class' => 'form-input'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une catégorie',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description détaillée du produit, caractéristiques, matériaux...',
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
                    'placeholder' => 'Instructions d\'utilisation, conseils d\'entretien, avertissements...',
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
            ->add('pictureFiles', FileType::class, [
                'label' => 'Images du produit (plusieurs possibles)',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-input file-input',
                    'accept' => 'image/*'
                ],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\All([
                        new \Symfony\Component\Validator\Constraints\File([
                            'maxSize' => '5M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/gif',
                            ],
                            'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG, etc.).',
                            'maxSizeMessage' => 'L\'image ne doit pas dépasser 5Mo.',
                        ])
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
} 