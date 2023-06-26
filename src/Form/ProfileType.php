<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 20,
                        'maxMessage' => '20 caratère max svp!'
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 20,
                        'maxMessage' => '20 caratère max svp!'
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 20,
                        'maxMessage' => '20 caratère max svp!'
                    ]),
                ],
            ])
            ->add('civilite', ChoiceType::class, [
                'label' => 'Civilité',
                'choices' => [
                    'Masculin' => 'm',
                    'Féminin' => 'f',
                    
                ],
                'data' => 'f', // Définit 'Feminin' comme valeur par défaut
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Choice(['choices' => ['m', 'f']]),
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 50,
                        'maxMessage' => '50 caratère max svp!'
                    ]),
                ],
            ])
            ->add('code_postal', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 15,
                        'maxMessage' => '15 caratère max svp!'
                    ]),
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 50,
                        'maxMessage' => '50 caratère max svp!'
                    ]),
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
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Actif' => 1,
                    'Inactif' => 0,
                ],
                'required' => false,
            ])
            
            
            ->add('Envoyer', SubmitType::class, [
                'attr' => ['class' => 'btn-success'],
            ])
            ;


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
            
        ]);
    }
}
