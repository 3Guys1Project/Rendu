<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class RegisterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'attr' => [
                    'minlength' => 3,
                    'maxlength' => 30,
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_-]{3,30}$/',
                        'message' => 'Le nom d\'utilisateur doit contenir uniquement des lettres, des chiffres, des tirets et des underscores.'
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
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                "mapped" => false,
                'attr' => [
                    'minlength' => 8,
                    'maxlength' => 30,
                    'pattern' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,30}$',
                ],
                "constraints" => [
                    new NotBlank(),
                    new NotNull(),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Il faut au moins 8 caractères pour le mdp!',
                        'max' => 30,
                        'maxMessage' => 'Il faut moins de 20 caractères pour le mdp!'
                    ]),
                    new Regex([
                        'pattern' => '#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,30}$#',
                        'message' => 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial'
                    ])
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
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
            ])
            ->add('visible', CheckboxType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Inscription',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
