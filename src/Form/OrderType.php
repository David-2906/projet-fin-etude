<?php

namespace App\Form;

use App\Entity\Transporter;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = new User();
        $builder
            ->add('transporteur', EntityType::class, [
                'class' => Transporter::class,
                'label' => false,
                'required' => true,
                'multiple' => false, // Ne peut choisir qu'un seul transporteur
                'expanded' => true, // Mettre le type radio
            ])

            
            // ->add('adresse', EntityType::class, [
            //     'class' => User::class,
            //     'label' => false,
            //     'required' => true,
            //     'choices' => $user->getAdresse(),
            //     'multiple' => false, 
            //     'expanded' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            // 'user' => []
        ]);
    }
}
