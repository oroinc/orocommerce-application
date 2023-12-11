<?php

namespace Training\Bundle\UserNamingBundle\Controller;

use Training\Bundle\UserNamingBundle\Entity\UserNamingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contains CRUD actions for User Naming
 */
class UserNamingController
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
}
