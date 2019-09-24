<?php

namespace albertborsos\ddd\validators;

use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\repositories\AbstractCycleRepository;
use albertborsos\ddd\traits\TargetRepositoryPropertyTrait;
use Yii;

class UniqueValidator extends \yii\validators\Validator
{
    use TargetRepositoryPropertyTrait;

    public $targetAttribute;

    public $filter = [];

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
        /** @var AbstractEntity $entity */
        $entity = $this->targetRepository->newEntity();
        $entity->setAttributes([$attribute => $form->{$attribute}], false);

        if ($this->targetRepository->exists($entity, [$attribute], !empty($this->filter) ? $this->filter : $this->defaultFilterCondition($form))) {
            $this->addError($form, $attribute, $this->message);
        }
    }

    protected function initMessage(): void
    {
        if ($this->message !== null) {
            return;
        }
        if (is_array($this->targetAttribute) && count($this->targetAttribute) > 1) {
            $this->message = Yii::t('yii', 'The combination {values} of {attributes} has already been taken.');
        } else {
            $this->message = Yii::t('yii', '{attribute} "{value}" has already been taken.');
        }
    }

    private function defaultFilterCondition(AbstractEntity $entity)
    {
        $condition = [];

        if ($entity->isNew()) {
            return $condition;
        }

        $primaryKeys = is_array($entity->getPrimaryKey()) ? $entity->getPrimaryKey() : [$entity->getPrimaryKey()];
        array_walk($primaryKeys, function ($key) use (&$condition, $entity) {
            switch (true) {
                case $this->targetRepository instanceof AbstractCycleRepository:
                    $condition[] = [$key, '!=', $entity->{$key}];
                    break;
            }
        });

        return $condition;
    }
}
