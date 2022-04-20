<?php

namespace Training\Bundle\UserNamingBundle\Controller;

use Oro\Bundle\FormBundle\Model\UpdateHandler;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Training\Bundle\UserNamingBundle\Entity\UserNamingType;
use Training\Bundle\UserNamingBundle\Form\Type\UserNamingTypeType;

/**
 * The controller for UserNamingType entity.
 * @Route("/user_naming_type")
 */
class UserNamingTypeController extends AbstractController
{
    /**
     * @Route("/view/{id}", name="training_user_naming_type_view", requirements={"id"="\d+"})
     * @Template
     * @Acl(
     *      id="training_user_naming_type_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="TrainingUserNamingBundle:UserNamingType"
     * )
     */
    public function viewAction(UserNamingType $userNamingType)
    {
        return [
            'entity' => $userNamingType
        ];
    }

    /**
     * Create UserNamingType form
     * @Route("/create", name="training_user_naming_type_create", options={"expose"=true})
     * @Template("@TrainingUserNaming/UserNamingType/update.html.twig")
     * @Acl(
     *      id="training_user_naming_type_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="TrainingUserNamingBundle:UserNamingType"
     * )
     */
    public function createAction()
    {
        return $this->update(new UserNamingType());
    }

    /**
     * Update user form
     * @Route("/update/{id}", name="training_user_naming_type_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="training_user_naming_type_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="TrainingUserNamingBundle:UserNamingType"
     * )
     */
    public function updateAction(UserNamingType $userNamingType)
    {
        return $this->update($userNamingType);
    }

    /**
     * @Route(
     *      "/{_format}",
     *      name="training_user_naming_type_index",
     *      requirements={"_format"="html|json"},
     *      defaults={"_format" = "html"}
     * )
     * @Template
     * @AclAncestor("training_user_naming_type_view")
     */
    public function indexAction()
    {
        return [
            'entity_class' => UserNamingType::class
        ];
    }

    /**
     * @param UserNamingType $entity
     *
     * @return array
     */
    protected function update(UserNamingType $entity)
    {
        $form = $this->createForm(UserNamingTypeType::class, $entity);

        return $this->get(UpdateHandler::class)->update(
            $entity,
            $form,
            $this->get(TranslatorInterface::class)->trans('training.usernaming.controller.usernamingtype.saved.message')
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class,
                UpdateHandler::class
            ]
        );
    }
}
