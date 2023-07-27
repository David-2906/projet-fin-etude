<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
            'label' => 'Adresse Email (*)',
            'attr' => [
                'placeholder' => 'adresse@',
                
            ],
        ])
            ->add('nom', TextType::class,[
                'label' => 'Nom (*)',

            ])
            ->add('prenom', TextType::class,[
                'label' => 'Prenom (*)',

            ])
            ->add('dateNaissance', DateType::class,[
                'label' => 'Date de Naissance (*)',
                'years' => range(date('Y') - 100, date('Y') + 0),
                'format' => 'dd-MM-yyyy',
                'constraints' => [ 
                    //  la classe LessThanOrEqual de Symfony pour vérifier que la date de naissance fournie est inférieure ou égale à 18 ans avant la date actuelle.
                    new Assert\LessThanOrEqual([
                        'value' => '-18 years', 
                        'message' => 'Vous devez avoir 18 ans pour vous inscrire sur ce site !'
                    ])
                ]
            ])
            ->add('adresse', TextType::class,[
                'label' => 'Adresse (*)'
            ])
            ->add('complementAdresse', TextType::class,[
                'label' => 'Complement d\'adresse'
            ])
            ->add('ville', TextType::class,[
                'label' => 'Ville (*)'
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code Postal (*)'
            ])
            
            
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "J'accepte les CGU",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'J\'accepte les conditions d\'utilisations',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Mot de Passe (*)'],
                'second_options' => ['label' => 'Confirmer mot de passe (*)'],
                'invalid_message' => 'Les mots de passe doivent être concordants',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex('/[a-z]/', 'Votre mot de passe doit contenir au moins une lettre minuscule'),
                    new Regex('/[A-Z]/', 'Votre mot de passe doit contenir au moins une lettre majuscule'),
                    new Regex('/[0-9]/', 'Votre mot de passe doit contenir au moins un chiffre'),
                    new Regex('/[\$\^@\\/\+\*_\-\.\!]/', 'Votre mot de passe doit contenir au moins un caractère spécial'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
