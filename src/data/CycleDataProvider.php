<?php

namespace albertborsos\ddd\data;

use albertborsos\ddd\interfaces\EntityInterface;
use Cycle\ORM\Select;
use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;
use yii\db\Connection;
use yii\di\Instance;

class CycleDataProvider extends BaseDataProvider
{
    /**
     * @var \Cycle\ORM\Select the query that is used to fetch data models and [[totalCount]]
     * if it is not explicitly set.
     */
    public $select;
    /**
     * @var string|callable the column that is used as the key of the data models.
     * This can be either a column name, or a callable that returns the key value of a given data model.
     *
     * If this is not set, the following rules will be used to determine the keys of the data models:
     *
     * - If [[query]] is an [[\Cycle\ORM\Select]] instance, the primary keys of [[AbstractCycleRepository::entityClass]] will be used.
     * - Otherwise, the keys of the [[models]] array will be used.
     *
     * @see getKeys()
     */
    public $key;
    /**
     * @var \albertborsos\cycle\Connection|array|string the DB connection object or the application component ID of the DB connection.
     * If not set, the default DB connection will be used.
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     */
    public $cycle;
    /**
     * @var string
     */
    public $entityClass;

    /**
     * Initializes the DB connection component.
     * This method will initialize the [[cycle]] property to make sure it refers to a valid DB connection.
     * @throws InvalidConfigException if [[cycle]] is invalid.
     */
    public function init()
    {
        parent::init();
        if (is_string($this->cycle)) {
            $this->cycle = Instance::ensure($this->cycle, \albertborsos\cycle\Connection::class);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareModels()
    {
        if (!$this->select instanceof Select) {
            throw new InvalidConfigException('The "select" property must be an instance of a class that implements the \Cycle\ORM\Select or its subclasses.');
        }
        $select = clone $this->select;
        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
            if ($pagination->totalCount === 0) {
                return [];
            }
            $select->limit($pagination->getLimit())->offset($pagination->getOffset());
        }
        if (($sort = $this->getSort()) !== false) {
            $select->orderBy($sort->getOrders());
        }

        return $select->fetchAll();
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareKeys($models)
    {
        $keys = [];
        if ($this->key !== null) {
            foreach ($models as $model) {
                if (is_string($this->key)) {
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }

            return $keys;
        } elseif ($this->select instanceof Select) {
            $pks = call_user_func([\Yii::createObject($this->entityClass), 'getPrimaryKey']);
            if (count($pks) === 1) {
                $pk = $pks[0];
                foreach ($models as $model) {
                    $keys[] = $model[$pk];
                }
            } else {
                foreach ($models as $model) {
                    $kk = [];
                    foreach ($pks as $pk) {
                        $kk[$pk] = $model[$pk];
                    }
                    $keys[] = $kk;
                }
            }

            return $keys;
        }

        return array_keys($models);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTotalCount()
    {
        if (!$this->select instanceof Select) {
            throw new InvalidConfigException('The "select" property must be an instance of a class that implements the \Cycle\ORM\Select or its subclasses.');
        }
        $select = clone $this->select;
        return (int)$select->limit(0)->offset(0)->orderBy([])->count('*');
    }

    /**
     * {@inheritdoc}
     */
    public function setSort($value)
    {
        parent::setSort($value);
        if (($sort = $this->getSort()) !== false && $this->select instanceof Select) {
            $entity = \Yii::createObject($this->entityClass);
            if (empty($sort->attributes)) {
                foreach ($entity->attributes() as $attribute) {
                    $sort->attributes[$attribute] = [
                        'asc' => [$attribute => SORT_ASC],
                        'desc' => [$attribute => SORT_DESC],
                        'label' => $entity->getAttributeLabel($attribute),
                    ];
                }
            } else {
                foreach ($sort->attributes as $attribute => $config) {
                    if (!isset($config['label'])) {
                        $sort->attributes[$attribute]['label'] = $entity->getAttributeLabel($attribute);
                    }
                }
            }
        }
    }

    public function __clone()
    {
        if (is_object($this->select)) {
            $this->select = clone $this->select;
        }

        parent::__clone();
    }
}
