<?php

namespace Training\Bundle\UserNamingBundle\Provider;

use Oro\Bundle\EntityBundle\Provider\EntityNameProviderInterface;
use Oro\Bundle\UserBundle\Entity\User;

class EntityNameProviderDecorator implements EntityNameProviderInterface
{
    public const PREFIX = 'PREFIX';
    public const FIRST = 'FIRST';
    public const MIDDLE = 'MIDDLE';
    public const LAST = 'LAST';
    public const SUFFIX = 'SUFFIX';
    
    private $decoratedProvider;

    public function __construct(EntityNameProviderInterface $decoratedProvider)
    {
        $this->decoratedProvider = $decoratedProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getName($format, $locale, $entity)
    {
        if ($entity instanceof User && null !== $entity->getNamingType()) {
            $userNamingFormat = $entity->getNamingType()->getFormat();

            $userFullName = $this->getUserFullName($entity, $userNamingFormat);
            $userFullName = trim(preg_replace('/\s+/', ' ', $userFullName));

            return $userFullName ?: false;
        }

        return $this->decoratedProvider->getName($format, $locale, $entity);
    }

    /**
     * {@inheritDoc}
     */
    public function getNameDQL($format, $locale, $className, $alias)
    {
        return $this->decoratedProvider->getNameDQL($format, $locale, $className, $alias);
    }

    public function getUserFullName(User $user, string $format): string
    {
        return strtr(
            $format,
            [
                self::PREFIX => $user->getNamePrefix(),
                self::FIRST => $user->getFirstName(),
                self::MIDDLE => $user->getMiddleName(),
                self::LAST => $user->getLastName(),
                self::SUFFIX => $user->getNameSuffix()
            ]
        );
    }
}
