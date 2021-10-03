<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Member;
use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => false,
            ])
            ->add('birthDay', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'js-datepicker'
                ]
            ])
            ->add('session', EntityType::class, [
                'label' => false,
                'class' => Session::class,
                'choice_label' => 'name',
            ])
            ->add('post', EntityType::class, [
                'label' => false,
                'class' => Post::class,
                'choice_label' => 'name'
            ])
            ->add('photoFile', VichImageType::class, [
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
        ]);
    }
}
