<?php

namespace albertborsos\ddd\validators;

use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\traits\TargetRepositoryPropertyTrait;

class ExistValidator extends \yii\validators\Validator
{
    use TargetRepositoryPropertyTrait;

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
        $this->fillTargetAttributes($entity, $form, $attribute);

        if (!$this->targetRepository->exists($entity, $this->targetAttribute)) {
            $this->addError($form, $attribute, $this->message);
        }
    }

    protected function initMessage(): void
    {
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} is invalid.');
        }
    }

    private function fillTargetAttributes(AbstractEntity $entity, AbstractEntity $form, $attribute): void
    {
        $targetAttribute = $this->targetAttribute === null ? $attribute : $this->targetAttribute;
        if (is_array($targetAttribute)) {
            foreach ($targetAttribute as $formAttribute => $entityAttribute) {
                $entity->{$entityAttribute} = $form->{$formAttribute};
            }

            return;
        }

        $entity->setAttributes([$targetAttribute => $form->$attribute], false);
    }
}
