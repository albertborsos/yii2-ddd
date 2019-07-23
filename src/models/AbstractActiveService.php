<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\BusinessObject;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\FormObject;
use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Class AbstractDomain
 * @package albertborsos\ddd\models
 * @since 1.1.0
 */
abstract class AbstractActiveService extends AbstractService
{
    public function __construct(FormObject $form = null, EntityInterface $model = null, $config = [])
    {
        parent::__construct($form, $model, $config);
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
    public function execute()
    {
        /** @var FormObject $form */
        $form = $this->getForm();

        /** @var AbstractEntity $model */
        $model = $this->getModel() ?? $this->getRepository()->hydrate([]);
        $model->setAttributes($form->attributes, false);

        if ($this->getRepository()->save($model)) {
            $this->setId($model->id);
            return true;
        }

        $form->addErrors($model->getErrors());

        return false;
    }
}
