<?php

namespace albertborsos\ddd\behaviors;

use albertborsos\ddd\base\EntityEvent;
use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\RepositoryInterface;
use albertborsos\ddd\traits\EvaluateAttributesTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\validators\UniqueValidator;

class SluggableBehavior extends \yii\behaviors\SluggableBehavior
{
    use EvaluateAttributesTrait;

    /** @var ActiveRepositoryInterface */
    public $repository;

    public function init()
    {
        if (empty($this->attributes)) {
            $this->attributes = [
                EntityInterface::EVENT_BEFORE_INSERT => $this->slugAttribute,
                EntityInterface::EVENT_BEFORE_UPDATE => $this->slugAttribute,
            ];
        }

        if ($this->attribute === null && $this->value === null) {
            throw new InvalidConfigException('Either "attribute" or "value" property must be specified.');
        }

        if ($this->ensureUnique && empty($this->repository)) {
            throw new InvalidConfigException(get_called_class() . '::$repository must be set!');
        }

        if ($this->repository) {
            /** @var ActiveRepositoryInterface $repository */
            $repository = \Yii::createObject($this->repository);
            $this->setRepository($repository);
        }
    }

    /**
     * @return ActiveRepositoryInterface
     */
    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param ActiveRepositoryInterface $repository
     */
    protected function setRepository(ActiveRepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue($event)
    {
        if (!$this->isNewSlugNeededByEvent($event)) {
            return $this->owner->{$this->slugAttribute};
        }

        if ($this->attribute !== null) {
            $slugParts = [];
            foreach ((array)$this->attribute as $attribute) {
                $part = ArrayHelper::getValue($this->owner, $attribute);
                if ($this->skipOnEmpty && $this->isEmpty($part)) {
                    return $this->owner->{$this->slugAttribute};
                }
                $slugParts[] = $part;
            }
            $slug = $this->generateSlug($slugParts);
        } else {
            $slug = parent::getValue($event);
        }

        return $this->ensureUnique ? $this->makeUnique($slug) : $slug;
    }

    protected function isNewSlugNeededByEvent(EntityEvent $event)
    {
        if (empty($this->owner->{$this->slugAttribute})) {
            return true;
        }

        if ($this->immutable) {
            return false;
        }

        if ($this->attribute === null) {
            return true;
        }

        foreach ((array)$this->attribute as $attribute) {
            if (in_array($attribute, array_keys($event->dirtyAttributes))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if given slug value is unique.
     * @param string $slug slug value
     * @return bool whether slug is unique.
     */
    protected function validateSlug($slug)
    {
        /* @var $validator UniqueValidator */
        /* @var $model BaseActiveRecord */
        $validator = Yii::createObject(array_merge(
            [
                'class' => UniqueValidator::className(),
            ],
            $this->uniqueValidator
        ));

        $model = Yii::createObject($this->repository->getDataModelClass());
        $model->clearErrors();
        $model->{$this->slugAttribute} = $slug;

        $validator->validateAttribute($model, $this->slugAttribute);
        return !$model->hasErrors();
    }
}
