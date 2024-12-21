<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileForm extends AbstractType
{
    private $countries;

    public function __construct(ParameterBagInterface $params)
    {
        $this->countries = $params->get('countries');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'minlength' => 3,
                    'maxlength' => 30,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom d’utilisateur doit comporter au moins {{ limit }} caractères.',
                        'max' => 30,
                        'maxMessage' => 'Le nom d’utilisateur ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('code', TextType::class, [
                'label' => 'Code',
                'attr' => [
                    'minlength' => 20,
                    'maxlength' => 20,
                    'pattern' => '^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{20}$',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{20}$/',
                        'message' => 'Le code doit contenir au moins une lettre, un chiffre et ne doit pas contenir de caractères spéciaux.'
                    ])
                ]
            ])
            ->add('avatar', FileType::class, [
                "mapped" => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
                "constraints" => [
                    new File(
                        maxSize: '20M',
                        maxSizeMessage: 'Le fichier est trop lourd',
                        extensions: ['jpg', 'jpeg', 'png', 'webp'],
                        extensionsMessage: 'Le fichier doit être une image (jpg, jpeg, png, webp)'
                    )
                ]
            ])
            ->add('banner', FileType::class, [
                "mapped" => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
                "constraints" => [
                    new File(
                        maxSize: '20M',
                        maxSizeMessage: 'Le fichier est trop lourd',
                        extensions: ['jpg', 'jpeg', 'png', 'webp'],
                        extensionsMessage: 'Le fichier doit être une image (jpg, pjeg, png, webp)'
                    )
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'minlength' => 3,
                    'maxlength' => 30,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.',
                        'max' => 30,
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'minlength' => 3,
                    'maxlength' => 30,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le prénom doit comporter au moins {{ limit }} caractères.',
                        'max' => 30,
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Biographie',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'La biographie ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'maxlength' => 15,
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(?:\+33|0033|0)[1-9](?:[\s.-]?[0-9]{2}){4}$/',
                        'message' => 'Veuillez renseigner un numéro de téléphone valide.',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'L’adresse ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class)
            ->add('country', ChoiceType::class, [
                'label' => 'Pays',
                'required' => false,
                'choices' => $this->countries,
                'choice_label' => function ($choice, $key, $value) {
                    return $value;
                },
                'placeholder' => 'Sélectionnez un pays',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'La ville ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ]
            ])
            ->add('zipCode', TextType::class, [
                'required' => false,
                'label' => 'Code postal',
                'attr' => [
                    'maxlength' => 5,
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '#^\d{5}$#',
                        'message' => 'Le code postal doit comporter 5 chiffres.',
                    ]),
                ],
            ])
            ->add('website', TextType::class, [
                'required' => false,
                'label' => 'Site web',
                'constraints' => [
                    new Regex([
                        'pattern' => '#^https?://#',
                        'message' => 'Le site web doit commencer par http:// ou https://.',
                    ]),
                ],
            ])
            ->add('job', TextType::class, [
                'required' => false,
                'label' => 'Profession',
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'La profession ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('visible', CheckboxType::class, [
                'required' => false,
            ])
            ->add('password', PasswordType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'minlength' => 8,
                    'maxlength' => 30,
                    'pattern' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,30}$',
                ],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères.',
                        'max' => 30,
                        'maxMessage' => 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,30}$#',
                        'message' => 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.',
                    ]),
                ],
                'label' => 'Mot de passe',
            ])
            ->add('confirmPassword', PasswordType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'minlength' => 8,
                ],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères.',
                    ]),
                ],
                'label' => 'Confirmer le mot de passe',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Mettre à jour le profil',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
