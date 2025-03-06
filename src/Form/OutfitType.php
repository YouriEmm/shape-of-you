<?php

namespace App\Form;

use App\Entity\ClothingItem;
use App\Entity\Outfit;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutfitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('public')
            ->add('owner', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
            ])
            ->add('items', EntityType::class, [
                'class' => ClothingItem::class,
'choice_label' => 'id',
'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outfit::class,
        ]);
    }
}
