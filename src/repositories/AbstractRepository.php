<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\HydratorInterface;
use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Class AbstractRepository
 * @package albertborsos\ddd\repositories
 * @since 1.1.0
 */
abstract class AbstractRepository extends Component implements RepositoryInterface
{
    /**
     * The Hydrator class which creates entities from data.
     *
     * @var string|HydratorInterface
     */
    protected $hydrator = HydratorInterface::class;

    /** @var string */
    protected $entityClass;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $entity = \Yii::createObject($this->entityClass);
        if (!$entity instanceof EntityInterface) {
            throw new InvalidConfigException(get_called_class() . '::$entityClass must implements `' . EntityInterface::class . '`');
        }

        $this->hydrator = \Yii::createObject($this->hydrator, [$entity->fieldMapping()]);
        if (!$this->hydrator instanceof HydratorInterface) {
            throw new InvalidConfigException(get_called_class() . '::$hydrator must implements `' . HydratorInterface::class . '`');
        }
    }

    public function hydrate($data): EntityInterface
    {
        return $this->hydrator->hydrate($this->entityClass, $data);
    }

    public function hydrateInto(EntityInterface $model, $data): EntityInterface
    {
        return $this->hydrator->hydrateInto($model, $data);
    }

    public function hydrateAll($models)
    {
        return $this->hydrator->hydrateAll($this->entityClass, $models);
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param $className
     */
    public function setEntityClass($className): void
    {
        if (empty($className) || !\Yii::createObject($className) instanceof EntityInterface) {
            throw new InvalidConfigException(get_called_class() . '::dataModelClass() must implements `' . EntityInterface::class . '`');
        }
        $this->entityClass = $className;
    }
}
