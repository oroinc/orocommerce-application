<?php

namespace Oro\Bridge\ContactUs\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Training\Bundle\UserNamingBundle\Entity\UserNamingType;

/**
 * Loads Contact Us Form content widget.
 */
class LoadUserNamingTypeData extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private array $namingTypes = [
        'Official' => 'PREFIX FIRST MIDDLE LAST SUFFIX',
        'Unofficial' => 'FIRST LAST',
        'First name only' => 'FIRST'
    ];

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('oro_user.manager');
        /** @var User $owner */
        $owner = $userManager->findUserByUsername('admin');

        foreach ($this->namingTypes as $title => $format) {
            $entity = $this->getType($owner, $title, $format);
            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getType(User $owner, string $title, string $format): UserNamingType
    {
        $organization = $owner->getOrganization();

        $entity = new UserNamingType();
        $entity->setTitle($title)
            ->setFormat($format)
            ->setOwner($owner)
            ->setOrganization($organization);

        return $entity;
    }
}
