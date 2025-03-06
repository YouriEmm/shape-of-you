<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Outfit;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content')
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('outfit', EntityType::class, [
                'class' => Outfit::class,
'choice_label' => 'id',
            ])
            ->add('owner', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
            ])
            ->add('parent', EntityType::class, [
                'class' => Comment::class,
'choice_label' => 'id',
'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
