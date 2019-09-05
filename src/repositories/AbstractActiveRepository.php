<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\base\EntityEvent;
use albertborsos\ddd\data\ActiveEvent;
use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\db\Connection;
use yii\db\Transaction;

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
        $this->validateDataModelClass();
    }

    /**
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface]] instance.
     */
    protected function find(): ActiveQueryInterface
    {
        return call_user_func([$this->getDataModelClass(), 'find']);
    }

    /**
     * @param $condition
     * @return EntityInterface|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function findById($id): ?EntityInterface
    {
        $model = $this->find()->andWhere(['id' => $id])->one();

        if (empty($model)) {
            return null;
        }

        return $this->hydrate($model);
    }

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @param bool $checkIsNewRecord
     * @return bool
     * @throws InvalidConfigException
     * @throws \Throwable
     */
    public function insert(EntityInterface $entity, $runValidation = true, $attributeNames = null, $checkIsNewRecord = true): bool
    {
        if ($checkIsNewRecord && $this->exists($entity)) {
            throw new InvalidArgumentException('Entity already exists, but `insert` method is called');
        }

        /** @var ActiveRecord $activeRecord */
        $activeRecord = \Yii::createObject($this->getDataModelClass(), [$entity->getDataAttributes()]);

        return $this->insertInternal($entity, $activeRecord, $runValidation, $attributeNames);
    }

    /**
     * @param EntityInterface $entity
     * @param ActiveRecordInterface|null $activeRecord
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws InvalidConfigException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function update(EntityInterface $entity, $runValidation = true, $attributeNames = null): bool
    {
        /** @var ActiveRecord $activeRecord */
        $activeRecord = $this->getActiveRecordByEntity($entity);

        if (empty($activeRecord)) {
            throw new InvalidArgumentException('Entity is not stored yet, but `update` method is called');
        }

        return $this->updateInternal($entity, $activeRecord, $runValidation, $attributeNames);
    }

    /**
     * @param EntityInterface|Model $entity
     * @return bool|int
     * @throws InvalidConfigException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function delete(EntityInterface $entity): bool
    {
        /** @var ActiveRecord $activeRecord */
        $activeRecord = $this->getActiveRecordByEntity($entity);

        if (empty($activeRecord)) {
            return false;
        }

        if (!$this->beforeDelete($entity)) {
            return false;
        }

        if ($activeRecord->delete() !== false) {
            $entity->trigger(EntityInterface::EVENT_AFTER_DELETE, new ActiveEvent(['sender' => $activeRecord]));
            return true;
        }

        return false;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function exists(EntityInterface $entity): bool
    {
        return $this->find()->andWhere($this->createFindConditionByEntityKeys($entity))->exists();
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
        $this->dataModelClass = $className;
        $this->validateDataModelClass();
    }

    /**
     * @param EntityInterface $entity
     * @param ActiveRecord $activeRecord
     * @param $runValidation
     * @param $attributes
     * @return bool
     * @throws \Throwable
     */
    private function insertInternal(EntityInterface $entity, ActiveRecord $activeRecord, $runValidation, $attributes): bool
    {
        if (!$this->beforeSave(true, $entity)) {
            return false;
        }

        $activeRecord->setAttributes($entity->getDataAttributes(), false);

        if ($activeRecord->insert($runValidation, $attributes)) {
            $entity->trigger(EntityInterface::EVENT_AFTER_SAVE, new ActiveEvent(['sender' => $activeRecord, 'scenario' => ActiveEvent::SCENARIO_INSERT]));
            $entity->setPrimaryKey($activeRecord);
            return true;
        }

        $entity->addErrors($activeRecord->getErrors());

//        $this->afterSave(true, []);

        return false;
    }

    /**
     * @param EntityInterface $entity
     * @param ActiveRecord $activeRecord
     * @param $runValidation
     * @param $attributes
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function updateInternal(EntityInterface $entity, ActiveRecord $activeRecord, $runValidation, $attributes): bool
    {
        $activeRecord->setAttributes($entity->getDataAttributes(), false);

        if (!$this->beforeSave(false, $entity, $activeRecord->getDirtyAttributes())) {
            return false;
        }

        // update attributes again, which are modified by behaviors
        $activeRecord->setAttributes($entity->getDataAttributes(), false);

        if ($activeRecord->update($runValidation, $attributes) !== false) {
            $entity->trigger(EntityInterface::EVENT_AFTER_SAVE, new ActiveEvent(['sender' => $activeRecord, 'scenario' => ActiveEvent::SCENARIO_UPDATE]));
            $entity->setPrimaryKey($activeRecord);
            return true;
        }

        $entity->addErrors($activeRecord->getErrors());

        return false;
    }

    /**
     * @throws InvalidConfigException
     */
    private function validateDataModelClass(): void
    {
        if (empty($this->dataModelClass) || !\Yii::createObject($this->dataModelClass) instanceof ActiveRecordInterface) {
            throw new InvalidConfigException(get_called_class() . '::$dataModelClass must implements `yii\db\ActiveRecordInterface`');
        }
    }

    /**
     * @param null $isolationLevel
     * @return Transaction
     * @throws InvalidConfigException
     */
    public function beginTransaction($isolationLevel = null): Transaction
    {
        return $this->resolveDatabase()->beginTransaction($isolationLevel);
    }

    /**
     * @return Connection
     * @throws InvalidConfigException
     */
    protected function resolveDatabase(): Connection
    {
        return \Yii::createObject([$this->getDataModelClass(), 'getDb']);
    }

    /**
     * @param bool $insert
     * @param EntityInterface $entity
     * @param array $dirtyAttributes
     * @return bool
     */
    public function beforeSave(bool $insert, EntityInterface $entity, array $dirtyAttributes = [])
    {
        $event = new EntityEvent(['dirtyAttributes' => $dirtyAttributes]);
        $entity->trigger($insert ? EntityInterface::EVENT_BEFORE_INSERT : EntityInterface::EVENT_BEFORE_UPDATE, $event);

        return $event->isValid;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function beforeDelete(EntityInterface $entity)
    {
        $event = new EntityEvent();
        $entity->trigger(EntityInterface::EVENT_BEFORE_DELETE, $event);

        return $event->isValid;
    }

    /**
     * @param EntityInterface $entity
     * @param bool $skipEmptyAttributes
     * @return array
     */
    protected function createFindConditionByEntityKeys(EntityInterface $entity, $skipEmptyAttributes = false): array
    {
        $keys = is_array($entity->getPrimaryKey()) ? $entity->getPrimaryKey() : [$entity->getPrimaryKey()];

        $condition = [];

        array_walk($keys, function ($key) use (&$condition, $entity) {
            $condition[$key] = $entity->{$key};
        });

        if ($skipEmptyAttributes) {
            $condition = array_filter($condition);
        }

        return $condition;
    }

    /**
     * @param EntityInterface $entity
     * @return array|ActiveRecordInterface|null
     */
    protected function getActiveRecordByEntity(EntityInterface $entity)
    {
        return $this->find()->andWhere($this->createFindConditionByEntityKeys($entity))->one();
    }
}
