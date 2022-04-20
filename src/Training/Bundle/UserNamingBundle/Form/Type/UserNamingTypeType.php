<?php

namespace Training\Bundle\UserNamingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Training\Bundle\UserNamingBundle\Entity\UserNamingType;

class UserNamingTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'training.usernaming.usernamingtype.title.label',
                    'required' => true
                ]
            )
            ->add(
                'format',
                TextType::class,
                [
                    'label' => 'training.usernaming.usernamingtype.format.label',
                    'required' => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => UserNamingType::class]);
    }
}
