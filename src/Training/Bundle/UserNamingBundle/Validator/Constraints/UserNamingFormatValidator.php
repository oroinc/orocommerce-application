<?php

namespace Training\Bundle\UserNamingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Training\Bundle\UserNamingBundle\Provider\EntityNameProviderDecorator;

class UserNamingFormatValidator extends ConstraintValidator
{
    private array $allowedPlaceholders = [
        EntityNameProviderDecorator::PREFIX,
        EntityNameProviderDecorator::FIRST,
        EntityNameProviderDecorator::MIDDLE,
        EntityNameProviderDecorator::LAST,
        EntityNameProviderDecorator::SUFFIX,
    ];

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UserNamingFormat) {
            throw new UnexpectedTypeException($constraint, UserNamingFormat::class);
        }

        if (!$this->isValidateValue($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ placeholders }}', implode(', ', $this->allowedPlaceholders))
                ->addViolation();
        }
    }

    private function isValidateValue($value): bool
    {
        $valueExp = array_filter(explode(' ', $value));

        $containsOnlyUnique = count(array_unique($valueExp)) === count($valueExp);
        return !($containsOnlyUnique === false || count(array_diff($valueExp, $this->allowedPlaceholders)) > 0);
    }
}
