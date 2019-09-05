<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\FormObject;

/**
 * Class AbstractActiveService
 * @package albertborsos\ddd\models
 * @since 2.0.0
 */
abstract class AbstractActiveService extends AbstractService
{
    public function __construct(FormObject $form = null, EntityInterface $entity = null, $config = [])
    {
        parent::__construct($form, $entity, $config);
    }

    /**
     * @return ActiveRepositoryInterface
     */
    protected function getRepository(): ActiveRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        /** @var FormObject $form */
        $form = $this->getForm();
        $action = $this->hasEntity() ? 'update' : 'insert';

        /** @var AbstractEntity $entity */
        $entity = $this->hasEntity() ? $this->getEntity() : $this->getRepository()->newEntity();
        $entity->setAttributes($form->attributes, false);

        if (call_user_func_array([$this->getRepository(), $action], [$entity])) {
            $this->setId($entity->id);
            return true;
        }

        $form->addErrors($entity->getErrors());

        return false;
    }
}
