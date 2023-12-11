<?php

namespace Training\Bundle\UserNamingBundle\Provider;

use Oro\Bundle\EntityBundle\Provider\EntityNameProviderInterface;
use Oro\Bundle\UserBundle\Entity\User;

class EntityNameProviderDecorator implements EntityNameProviderInterface
{
    /**
     * @param EntityNameProviderInterface $decorated
     * @param UserFullNameProvider $userFullNameProvider
     */
    public function __construct(
        private EntityNameProviderInterface $decorated,
        private UserFullNameProvider $userFullNameProvider
    ) {

    }

    /**
     * @param $format
     * @param $locale
     * @param $entity
     *
     * @return string
     */
    public function getName($format, $locale, $entity): string
    {
        if ($entity instanceof User) {
            if ($entity->getNamingType()) {
                return $this->userFullNameProvider->getFullName($entity, $entity->getNamingType()->getFormat());
            }
        }

        return $this->decorated->getName($format, $locale, $entity);
    }

    /**
     * @param $format
     * @param $locale
     * @param $className
     * @param $alias
     *
     * @return string
     */
    public function getNameDQL($format, $locale, $className, $alias): string
    {
        return $this->decorated->getNameDQL($format, $locale, $className, $alias);
    }
}
