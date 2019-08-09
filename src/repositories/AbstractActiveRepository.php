<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\data\ActiveEvent;
use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\Event;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;

/**
 * Class AbstractActiveRepository
 * @package albertborsos\ddd\repositories
 * @since 2.0.0
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

        return $this->hydrate($model);
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
     * @param EntityInterface|Model $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool|mixed
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function save(EntityInterface $entity, $runValidation = true, $attributeNames = null)
    {
        /** @var ActiveRecord $activeRecord */
        $activeRecord = $this->findOrCreate($entity);

        if ($activeRecord->isNewRecord) {
            return $this->insertInternal($entity, $runValidation, $attributeNames, $activeRecord);
        }

        return $this->updateInternal($entity, $runValidation, $attributeNames, $activeRecord);
    }

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     */
    public function insert(EntityInterface $entity, $runValidation = true, $attributeNames = null)
    {
        /** @var ActiveRecord $activeRecord */
        $activeRecord = $this->findOrCreate($entity);

        if (!$activeRecord->isNewRecord) {
            throw new InvalidArgumentException('Entity is already exists, but `insert` method is called');
        }

        return $this->insertInternal($entity, $runValidation, $attributeNames, $activeRecord);
    }

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     */
    public function update(EntityInterface $entity, $runValidation = true, $attributeNames = null)
    {
        /** @var ActiveRecord $activeRecord */
        $activeRecord = $this->findOrCreate($entity);

        if ($activeRecord->isNewRecord) {
            throw new InvalidArgumentException('Entity is not stored yet, but `update` method is called');
        }

        return $this->updateInternal($entity, $runValidation, $attributeNames, $activeRecord);
    }

    /**
     * @param EntityInterface|Model $entity
     * @return bool|int
     * @throws \yii\base\InvalidConfigException
     */
    public function delete(EntityInterface $entity)
    {
        /** @var ActiveRecordInterface $activeRecord */
        $activeRecord = call_user_func([$this->dataModelClass, 'findOne'], $entity->getDataAttributes());

        if (empty($activeRecord)) {
            return false;
        }

        if ($activeRecord->delete() !== false) {
            $entity->trigger(EntityInterface::EVENT_AFTER_DELETE, new ActiveEvent(['sender' => $activeRecord]));
            return true;
        }

        return false;
    }

    /**
     * @param EntityInterface|Model $entity
     * @param bool $skipEmptyAttributes
     * @return ActiveRecord
     * @throws \yii\base\InvalidConfigException
     */
    protected function findOrCreate(EntityInterface $entity, $skipEmptyAttributes = false)
    {
        $keys = is_array($entity->getPrimaryKey()) ? $entity->getPrimaryKey() : [$entity->getPrimaryKey()];

        $condition = [];
        array_walk($keys, function ($key) use (&$condition, $entity) {
            $condition[$key] = $entity->{$key};
        });

        if ($skipEmptyAttributes) {
            $condition = array_filter($condition);
        }

        if (empty($condition)) {
            return \Yii::createObject($this->dataModelClass, [$entity->getDataAttributes()]);
        }

        /** @var ActiveRecord $activeRecord */
        $activeRecord = \Yii::createObject([$this->dataModelClass, 'findOne'], [$condition]);

        if (!empty($activeRecord)) {
            $activeRecord->setAttributes($entity->getDataAttributes(), false);
            return $activeRecord;
        }

        return \Yii::createObject($this->dataModelClass, [$entity->getDataAttributes()]);
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

    /**
     * @param EntityInterface $entity
     * @param $runValidation
     * @param $attributeNames
     * @param ActiveRecord $activeRecord
     * @return bool
     * @throws \Throwable
     */
    private function insertInternal(EntityInterface $entity, $runValidation, $attributeNames, ActiveRecord $activeRecord): bool
    {
        if ($activeRecord->insert($runValidation, $attributeNames)) {
            $entity->trigger(EntityInterface::EVENT_AFTER_SAVE, new ActiveEvent(['sender' => $activeRecord, 'scenario' => ActiveEvent::SCENARIO_INSERT]));
            $entity->setPrimaryKey($activeRecord);
            return true;
        }

        $entity->addErrors($activeRecord->getErrors());

        return false;
    }

    /**
     * @param EntityInterface $entity
     * @param $runValidation
     * @param $attributeNames
     * @param ActiveRecord $activeRecord
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function updateInternal(EntityInterface $entity, $runValidation, $attributeNames, ActiveRecord $activeRecord): bool
    {
        if ($activeRecord->update($runValidation, $attributeNames)) {
            $entity->trigger(EntityInterface::EVENT_AFTER_SAVE, new ActiveEvent(['sender' => $activeRecord, 'scenario' => ActiveEvent::SCENARIO_UPDATE]));
            $entity->setPrimaryKey($activeRecord);
            return true;
        }

        $entity->addErrors($activeRecord->getErrors());

        return false;
    }
}
