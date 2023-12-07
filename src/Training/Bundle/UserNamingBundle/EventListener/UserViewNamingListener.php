<?php

namespace Training\Bundle\UserNamingBundle\EventListener;

use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;

class UserViewNamingListener
{
    /**
     * @param BeforeListRenderEvent $event
     *
     * @return void
     */
    public function onUserView(BeforeListRenderEvent $event): void
    {
        $user = $event->getEntity();
        if (!$user instanceof User) {
            return;
        }

        $template = $event->getEnvironment()->render(
            '@TrainingUserNaming/User/userNameBlock.html.twig',
            ['entity' => $user]
        );

        $subBlockId = $event->getScrollData()->addSubBlock(0);
        $event->getScrollData()->addSubBlockData(0, $subBlockId, $template);
    }
}
