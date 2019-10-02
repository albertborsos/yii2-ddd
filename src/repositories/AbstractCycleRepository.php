<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\RepositoryInterface;
use albertborsos\ddd\models\AbstractEntity;
use Cycle\ORM\Select;
use Cycle\ORM\Transaction;
use yii\base\InvalidArgumentException;
use yii\di\Instance;

abstract class AbstractCycleRepository extends AbstractRepository implements RepositoryInterface
{
    /** @var string|\Cycle\ORM\ORM */
    protected $orm = 'cycle';

    /** @var Select */
    protected $select;

    public function init()
    {
        parent::init();
        $this->orm = Instance::ensure($this->orm, \albertborsos\cycle\Connection::class)->orm();
        $this->select = new Select($this->orm, $this->orm->resolveRole($this->entityClass));
    }

    abstract public static function tableName(): string;

    abstract public static function schema(): array;

    protected function find(): Select
    {
        return $this->select;
    }

    /**
     * @param $id
     * @return EntityInterface|null
     */
    public function findById($id): ?EntityInterface
    {
        return $this->orm->getRepository($this->entityClass)->findByPK($id);
    }

    /**
     * @param EntityInterface $entity
     * @param array $attributes
     * @param array $filter
     * @return bool
     */
    public function exists(EntityInterface $entity, $attributes = [], $filter = []): bool
    {
        $select = $this->find();

        if (empty($attributes)) {
            $attributes = is_array($entity->getPrimaryKey()) ? $entity->getPrimaryKey() : [$entity->getPrimaryKey()];
        }
        foreach ($attributes as $attribute) {
            $select->andWhere([$attribute => $entity->{$attribute}]);
        }

        foreach ($filter as $condition) {
            call_user_func_array([$select, 'andWhere'], $condition);
        }

        return $select->count() !== 0;
    }

    /**
     * @param EntityInterface|AbstractEntity $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @param bool $checkIsNewRecord
     * @return bool
     * @throws \Throwable
     */
    public function insert(EntityInterface $entity, $runValidation = true, $attributeNames = null, $checkIsNewRecord = true): bool
    {
        if ($checkIsNewRecord && $this->exists($entity)) {
            throw new InvalidArgumentException('Entity already exists, but `insert` method is called');
        }

        if (!$this->beforeSave(true, $entity)) {
            return false;
        }

        $transaction = new Transaction($this->orm);
        $transaction->persist($entity, Transaction::MODE_ENTITY_ONLY);
        $transaction->run();

        $changedAttributes = array_fill_keys(array_keys($entity->attributes), null);
        $entity->setOldAttributes($entity->attributes);
        $this->afterSave(true, $entity, $changedAttributes);

        return true;
    }

    /**
     * @param EntityInterface|AbstractEntity $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws \Throwable
     */
    public function update(EntityInterface $entity, $runValidation = true, $attributeNames = null): bool
    {
        if (!$this->exists($entity)) {
            throw new InvalidArgumentException('Entity is not stored yet, but `update` method is called');
        }

        if (!$this->beforeSave(false, $entity)) {
            return false;
        }

        $transaction = new Transaction($this->orm);
        $transaction->persist($entity, Transaction::MODE_ENTITY_ONLY);
        $transaction->run();

        $values = $entity->getDirtyAttributes($attributeNames);
        $changedAttributes = [];
        foreach ($values as $name => $value) {
            $changedAttributes[$name] = $entity->getOldAttribute($name);
            $entity->setOldAttribute($name, $value);
        }

        $this->afterSave(true, $entity, $changedAttributes);

        return true;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     * @throws \Throwable
     */
    public function delete(EntityInterface $entity): bool
    {
        if (!$this->exists($entity)) {
            return false;
        }

        if (!$this->beforeDelete($entity)) {
            return false;
        }

        $transaction = new Transaction($this->orm);
        $transaction->delete($entity, Transaction::MODE_CASCADE);
        $transaction->run();

        $this->afterDelete($entity);

        return true;
    }

    /**
     * @return Transaction
     */
    public function beginTransaction()
    {
        return new Transaction($this->orm);
    }

    protected function andFilterWhere(Select $select, $attribute, $operator, $value): void
    {
        if ($this->isEmpty($value, $operator)) {
            return;
        }

        $select->andWhere($attribute, $operator, $value);
    }

    protected function isEmpty($value, $operator)
    {
        if ($operator === 'like') {
            $value = trim($value, '%');
        }

        if ($value === '' || $value === null || $value === [] || is_string($value) && trim($value) === '') {
            return true;
        }

        return false;
    }
}
