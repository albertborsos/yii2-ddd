<?php

namespace albertborsos\ddd\behaviors;

use albertborsos\ddd\base\EntityEvent;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\RepositoryInterface;
use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\traits\EvaluateAttributesTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class SluggableBehavior extends \yii\behaviors\SluggableBehavior
{
    use EvaluateAttributesTrait;

    /** @var RepositoryInterface */
    public $repository;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->setDefaultAttributes();
        $this->checkAttributeOrValueIsConfigured();
        $this->checkRepositoryIsConfigured();
    }

    /**
     * @param null $interface
     * @return RepositoryInterface
     * @throws InvalidConfigException
     */
    protected function getRepository($interface = null): RepositoryInterface
    {
        if (!empty($interface)) {
            return Yii::createObject($interface);
        }

        return \Yii::createObject($this->repository);
    }

    /**
     * Checks if given slug value is unique.
     * @param string $slug slug value
     * @return bool whether slug is unique.
     * @throws InvalidConfigException
     */
    protected function validateSlug($slug)
    {
        /* @var $validator \albertborsos\ddd\validators\UniqueValidator */
        $validator = Yii::createObject(array_merge(
            [
                'class' => \albertborsos\ddd\validators\UniqueValidator::class,
                'targetRepository' => $this->repository,
            ],
            $this->uniqueValidator
        ));

        /* @var $entity AbstractEntity|EntityInterface */
        $entity = clone $this->owner;
        $entity->{$this->slugAttribute} = $slug;
        $validator->validateAttribute($entity, $this->slugAttribute);
        return !$entity->hasErrors();
    }

    protected function setDefaultAttributes(): void
    {
        if (!empty($this->attributes)) {
            return;
        }

        $this->attributes = [
            EntityInterface::EVENT_BEFORE_INSERT => $this->slugAttribute,
            EntityInterface::EVENT_BEFORE_UPDATE => $this->slugAttribute,
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    protected function checkAttributeOrValueIsConfigured(): void
    {
        if ($this->attribute === null && $this->value === null) {
            throw new InvalidConfigException('Either "attribute" or "value" property must be specified.');
        }
    }

    /**
     * @throws InvalidConfigException
     */
    protected function checkRepositoryIsConfigured(): void
    {
        if ($this->ensureUnique && empty($this->repository)) {
            throw new InvalidConfigException(get_called_class() . '::$repository must be set!');
        }
    }
}
