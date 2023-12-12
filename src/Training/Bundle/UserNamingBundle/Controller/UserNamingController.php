<?php

namespace Training\Bundle\UserNamingBundle\Controller;

use Training\Bundle\UserNamingBundle\Entity\UserNamingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;

/**
 * Contains CRUD actions for User Naming
 */
class UserNamingController extends AbstractController
{
    /**
     * @Route("/", name="user_naming_index")
     * @Template
     *
     * @return array
     */
    public function indexAction(): array
    {
        return [
            'entity_class' => UserNamingType::class
        ];
    }

    /**
     * @Route("/view/{id}", name="user_naming_view", requirements={"id"="\d+"})
     * @Template
     *
     * @param UserNamingType $type
     *
     * @return array
     */
    public function viewAction(UserNamingType $entity): array
    {
        return [
            'entity' => $entity,
        ];
    }

    /**
     * @return UpdateHandlerFacade[]|string[]|TranslatorInterface[]
     */
    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class,
                UpdateHandlerFacade::class,
            ]
        );
    }
}
