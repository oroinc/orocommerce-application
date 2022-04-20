<?php

namespace Training\Bundle\UserNamingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UserNamingFormat extends Constraint
{
    /**
     * @var string
     */
    public $message = 'training.validators.user_naming_format.message';
}
