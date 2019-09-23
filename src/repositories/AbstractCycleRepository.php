<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\base\EntityEvent;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\RepositoryInterface;
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
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
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
        return true;
    }

    /**
     * @param EntityInterface $entity
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

        $dirtyAttributes = $entity->attributes; // @TODO: hack to update entity, should pass only dirty attributes
        if (!$this->beforeSave(false, $entity, $dirtyAttributes)) {
            return false;
        }

        $transaction = new Transaction($this->orm);
        $transaction->persist($entity, Transaction::MODE_ENTITY_ONLY);
        $transaction->run();
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
        return true;
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
     * @return Transaction
     */
    public function beginTransaction()
    {
        return new Transaction($this->orm);
    }
}
