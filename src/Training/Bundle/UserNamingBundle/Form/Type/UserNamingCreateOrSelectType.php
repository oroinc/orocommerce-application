<?php

namespace Training\Bundle\UserNamingBundle\Form\Type;

use Oro\Bundle\FormBundle\Form\Type\OroEntitySelectOrCreateInlineType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for UserNamingBundle entity with inline create & select buttons
 */
class UserNamingCreateOrSelectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'user_naming_types',
                'create_form_route' => 'training_user_naming_type_create',
                'grid_name' => 'training-user-naming-type-grid-select'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return OroEntitySelectOrCreateInlineType::class;
    }
}
