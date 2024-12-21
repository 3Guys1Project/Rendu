<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class CommentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => [
                    'minlength' => 10,
                    'maxlength' => 1000,
                ],
            ])
            ->add('stars', NumberType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                ],
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'La note doit être comprise entre 1 et 5',
                        'max' => 5,
                        'maxMessage' => 'La note doit être comprise entre 1 et 5',
                    ]),
                    new Regex([
                        'pattern' => '#^[1-5]$#',
                        'message' => 'La note doit être comprise entre 1 et 5',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
