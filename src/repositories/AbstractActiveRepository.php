<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\data\ActiveEvent;
use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;

/**
 * Class AbstractActiveRepository
 * @package albertborsos\ddd\repositories
 * @since 1.1.0
 */
abstract class AbstractActiveRepository extends AbstractRepository implements ActiveRepositoryInterface
{
    protected $dataModelClass;

    public function init()
    {
        parent::init();
        if (empty($this->dataModelClass) || !\Yii::createObject($this->dataModelClass) instanceof ActiveRecordInterface) {
            throw new InvalidConfigException(get_called_class() . '::dataModelClass() must implements `yii\db\ActiveRecordInterface`');
        }
    }

    /**
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface]] instance.
     */
    public function find()
    {
        return call_user_func([$this->dataModelClass, 'find']);
    }

    /**
     * @param $condition
     * @return EntityInterface|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function findOne($condition)
    {
        $model = call_user_func([$this->dataModelClass, 'findOne'], $condition);

        if (empty($model)) {
            return null;
        }

        return $this->hydrate($model->attributes);
    }

    /**
     * @param $condition
     * @return EntityInterface[]|array
     */
    public function findAll($condition)
    {
        $models = call_user_func([$this->dataModelClass, 'findAll'], $condition);

        return $this->hydrateAll($models);
    }

    /**
     * @param EntityInterface|Model $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool|mixed
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function save(EntityInterface $model, $runValidation = true, $attributeNames = null)
    {
        /** @var ActiveRecord $activeRecord */
        $activeRecord = $this->findOrCreate($model);

        if ($activeRecord->save($runValidation, $attributeNames)) {
            $model->trigger(EntityInterface::EVENT_AFTER_SAVE, new ActiveEvent(['sender' => $activeRecord]));
            $model->setPrimaryKey($activeRecord);
            return true;
        }

        return false;
    }

    /**
     * @param EntityInterface|Model $model
     * @return bool|int
     * @throws \yii\base\InvalidConfigException
     */
    public function delete(EntityInterface $model)
    {
        /** @var ActiveRecordInterface $activeRecord */
        $activeRecord = $this->findOrCreate($model);

        if ($activeRecord->delete() !== false) {
            $model->trigger(EntityInterface::EVENT_AFTER_DELETE, new ActiveEvent(['sender' => $activeRecord]));
            return true;
        }

        return false;
    }

    /**
     * @param EntityInterface|Model $model
     * @param bool $skipEmptyAttributes
     * @return ActiveRecord
     * @throws \yii\base\InvalidConfigException
     */
    protected function findOrCreate(EntityInterface $model, $skipEmptyAttributes = false)
    {
        $keys = is_array($model->getPrimaryKey()) ? $model->getPrimaryKey() : [$model->getPrimaryKey()];

        $condition = [];
        array_walk($keys, function ($key) use (&$condition, $model) {
            $condition[$key] = $model->{$key};
        });

        if ($skipEmptyAttributes) {
            $condition = array_filter($condition);
        }

        $dataAttributes = $model->dataAttributes;

        if (empty($condition)) {
            return \Yii::createObject($this->dataModelClass, [$dataAttributes]);
        }

        /** @var ActiveRecord $activeRecord */
        $activeRecord = \Yii::createObject([$this->dataModelClass, 'findOne'], [$condition]);

        if (!empty($activeRecord)) {
            $activeRecord->setAttributes($dataAttributes, false);
            return $activeRecord;
        }

        return \Yii::createObject($this->dataModelClass, [$dataAttributes]);
    }

    /**
     * @return string
     */
    public function getDataModelClass(): string
    {
        return $this->dataModelClass;
    }

    /**
     * @param $className
     * @throws InvalidConfigException
     */
    public function setDataModelClass($className): void
    {
        if (empty($className) || !\Yii::createObject($className) instanceof ActiveRecordInterface) {
            throw new InvalidConfigException(get_called_class() . '::dataModelClass() must implements `' . ActiveRecordInterface::class . '`');
        }
        $this->dataModelClass = $className;
    }
}
