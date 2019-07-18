<?php

namespace albertborsos\ddd\data;

use Yii;
use yii\data\ActiveDataProvider;
use yii\base\InvalidConfigException;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\HydratorInterface;

class ActiveEntityDataProvider extends ActiveDataProvider
{
    public $entityClass;

    /** @var HydratorInterface */
    public $hydrator;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty($this->entityClass)) {
            throw new InvalidConfigException(get_class($this) . '::$entityClass must be set.');
        }

        if (!Yii::createObject($this->entityClass) instanceof EntityInterface) {
            throw new InvalidConfigException(get_class($this) . '::$entityClass must implements ' . EntityInterface::class);
        }

        if (!$this->hydrator instanceof HydratorInterface) {
            throw new InvalidConfigException(get_called_class() . '::$hydrator must implements `' . HydratorInterface::class . '`');
        }
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    protected function prepareModels()
    {
        return $this->hydrator->hydrateAll($this->entityClass, parent::prepareModels());
    }
}
