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
 * @since 2.0.0
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
        $this->validateEntityClass();
        $this->initHydrator();
    }

    public function hydrate($data): EntityInterface
    {
        return $this->hydrator->hydrate($this->entityClass, $data);
    }

    public function hydrateInto(EntityInterface $entity, $data): EntityInterface
    {
        return $this->hydrator->hydrateInto($entity, $data);
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
        $this->entityClass = $className;
        $this->validateEntityClass($className);
    }

    /**
     * @throws InvalidConfigException
     */
    protected function validateEntityClass(): void
    {
        if (empty($this->entityClass) || !\Yii::createObject($this->entityClass) instanceof EntityInterface) {
            throw new InvalidConfigException(get_called_class() . '::dataModelClass() must implements `' . EntityInterface::class . '`');
        }
    }

    protected function initHydrator(): void
    {
        $entity = \Yii::createObject($this->entityClass);
        $this->hydrator = \Yii::createObject($this->hydrator, [$entity->fieldMapping()]);
        if (!$this->hydrator instanceof HydratorInterface) {
            throw new InvalidConfigException(get_called_class() . '::$hydrator must implements `' . HydratorInterface::class . '`');
        }
    }
}
