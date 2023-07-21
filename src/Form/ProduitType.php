<?php

namespace App\Form;

use App\Entity\Cepage;
use App\Entity\Produit;
use App\Entity\TypeProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation', TextType::class, [
                'label' => 'Designation'
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
        
            ])
            ->add('millesime', IntegerType::class, [
                'label' => 'Millésime'
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock'
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image'
            ])
            ->add('format', TextType::class, [
                'label' => 'Format'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du produit'
            ])
            ->add('typeProduit', EntityType::class, [
                'class' => TypeProduit::class,
                'choice_label' => 'name'
            ] )
            ->add('cepage', EntityType::class, [
                'class' => Cepage::class,
                'choice_label' => 'name'
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
