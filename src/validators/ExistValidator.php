<?php

namespace albertborsos\ddd\validators;

use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\traits\DefaultFilterConditionTrait;
use albertborsos\ddd\traits\TargetRepositoryPropertyTrait;

class ExistValidator extends \yii\validators\Validator
{
    use TargetRepositoryPropertyTrait;
    use DefaultFilterConditionTrait;

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
        /** @var AbstractEntity $entity */
        $entity = $this->targetRepository->newEntity();

        $this->normalizeTargetAttribute($attribute);
        $this->fillEntity($entity, $form, $attribute);

        if (!$this->targetRepository->exists($entity, array_values($this->targetAttribute), !empty($this->filter) ? $this->filter : $this->defaultFilterCondition($form))) {
            $this->addError($form, $attribute, $this->message);
        }
    }

    protected function initMessage(): void
    {
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} is invalid.');
        }
    }

    protected function fillEntity(AbstractEntity $entity, AbstractEntity $form, $attribute): void
    {
        foreach ($this->targetAttribute as $formAttribute => $entityAttribute) {
            $entity->{$entityAttribute} = $form->{$formAttribute};
        }
    }

    /**
     * @param $attribute
     * @return mixed
     */
    protected function normalizeTargetAttribute($attribute)
    {
        if (empty($this->targetAttribute)) {
            $this->targetAttribute = [$attribute];
        }

        if (is_string($this->targetAttribute)) {
            $this->targetAttribute = [$this->targetAttribute];
        }

        foreach ($this->targetAttribute as $formAttribute => $entityAttribute) {
            if (is_int($formAttribute)) {
                unset($this->targetAttribute[$formAttribute]);
                $this->targetAttribute[$entityAttribute] = $entityAttribute;
            }
        }
    }
}
