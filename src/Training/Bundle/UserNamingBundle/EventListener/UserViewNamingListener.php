<?php

namespace Training\Bundle\UserNamingBundle\EventListener;

use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;
use Oro\Bundle\UserBundle\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserViewNamingListener
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function onUserView(BeforeListRenderEvent $event)
    {
        /** @var User $user */
        $user = $event->getEntity();
        if (!$user) {
            return;
        }

        $template = $event->getEnvironment()->render(
            '@TrainingUserNaming/User/namingData.html.twig',
            ['entity' => $user]
        );

        $event->getScrollData()->addNamedBlock(
            'training_full_name',
            $this->translator->trans('training.usernaming.user_view.scroll_data.full_name.title')
        );
        $subBlockId = $event->getScrollData()->addSubBlock('training_full_name');
        $event->getScrollData()->addSubBlockData('training_full_name', $subBlockId, $template);
    }
}
