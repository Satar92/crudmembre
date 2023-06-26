<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', TextType::class, [
                'label' => 'Référence',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 40,
                        'maxMessage' => '40 caratère max svp!'
                    ]),
                ],
            ])
            ->add('titre', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 100]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['max' => 255]),
                ],
            ])
            ->add('couleur', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 40]),
                ],
            ])
            ->add('taille', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 4]),
                ],
            ])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Masculin' => 'm',
                    'Féminin' => 'f',
                ],
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('photo', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'image/svg',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG, PNG, WEBP ou SVG',
                    ]),
                ],
            ])
            ->add('prix', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^\d+(\.\d{1,2})?$/',
                        'message' => 'Le prix doit être un nombre décimal avec deux chiffres après la virgule.',
                    ]),
                ],
            ])
            ->add('stock', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Le stock doit être un nombre entier positif.',
                    ]),
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('Envoyer', SubmitType::class, [
                'attr' => ['class' => 'btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}

