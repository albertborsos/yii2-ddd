<?php

namespace albertborsos\ddd\data;

use albertborsos\ddd\factories\EntityFactory;
use albertborsos\ddd\interfaces\EntityInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

class ActiveEntityDataProvider extends ActiveDataProvider
{
    public $entityClass;

    public function init()
    {
        parent::init();
        if (empty($this->entityClass)) {
            throw new InvalidConfigException(get_class($this) . '::$entityClass must be set.');
        }

        if (!Yii::createObject($this->entityClass) instanceof EntityInterface) {
            throw new InvalidConfigException(get_class($this) . '::$entityClass must implements ' . EntityInterface::class);
        }
    }

    protected function prepareModels()
    {
        $models = parent::prepareModels();

        return EntityFactory::createAll($this->entityClass, $models);
    }
}