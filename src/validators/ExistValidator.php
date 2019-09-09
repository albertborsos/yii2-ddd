<?php

namespace albertborsos\ddd\validators;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\traits\RepositoryPropertyTrait;

class ExistValidator extends \yii\validators\Validator
{
    use RepositoryPropertyTrait;

    public $targetAttribute;

    public function init()
    {
        parent::init();
        $this->initRepository();
        $this->initMessage();
    }

    /**
     * @param AbstractEntity $form
     * @param string $attribute
     */
    public function validateAttribute($form, $attribute)
    {
        $entity = $this->repository->newEntity();
        $entity->setPrimaryKey($form);

        if ($this->repository->exists($entity, [$attribute], !$entity->isNew())) {
            $this->addError($form, $attribute, $this->message);
        }
    }

    protected function initMessage(): void
    {
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} is invalid.');
        }
    }
}
