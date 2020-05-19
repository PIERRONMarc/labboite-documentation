<?php

namespace App\Form;

use App\Entity\Tool;
use App\Form\InformationType;
use App\Form\CharacteristicType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FinalInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('information', InformationType::class, [
                'label' => false
            ])
            ->add('characteristic', CollectionType::class, [
                'entry_type' => CharacteristicType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'label' => false
            ])
            ->add('resources', CollectionType::class, [
                'entry_type' => ResourceType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tool::class,
        ]);
    }
}
