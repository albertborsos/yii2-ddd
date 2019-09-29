<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\base\AfterSaveEvent;
use albertborsos\ddd\base\EntityEvent;
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

    /**
     * @return EntityInterface
     */
    public function newEntity(): EntityInterface
    {
        return $this->hydrator->hydrate($this->entityClass, []);
    }

    /**
     * @param $data
     * @return EntityInterface
     */
    public function hydrate($data): EntityInterface
    {
        return $this->hydrator->hydrate($this->entityClass, $data);
    }

    /**
     * @param EntityInterface $entity
     * @param $data
     * @return EntityInterface
     */
    public function hydrateInto(EntityInterface $entity, $data): EntityInterface
    {
        return $this->hydrator->hydrateInto($entity, $data);
    }

    /**
     * @param $models
     * @return array
     */
    public function hydrateAll($models): array
    {
        if (empty($models)) {
            return [];
        }

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
     * @param bool $insert
     * @param EntityInterface $entity
     * @return bool
     */
    public function beforeSave(bool $insert, EntityInterface $entity)
    {
        $event = new EntityEvent();
        $entity->trigger($insert ? EntityInterface::EVENT_BEFORE_INSERT : EntityInterface::EVENT_BEFORE_UPDATE, $event);

        return $event->isValid;
    }

    /**
     * @param bool $insert
     * @param EntityInterface $entity
     * @param array $changedAttributes
     */
    public function afterSave(bool $insert, EntityInterface $entity, $changedAttributes = [])
    {
        $entity->trigger($insert ? EntityInterface::EVENT_AFTER_INSERT : EntityInterface::EVENT_AFTER_UPDATE, new AfterSaveEvent([
            'changedAttributes' => $changedAttributes,
        ]));
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
     */
    public function afterDelete(EntityInterface $entity)
    {
        $entity->trigger(EntityInterface::EVENT_AFTER_DELETE);
    }

    /**
     * @throws InvalidConfigException
     */
    protected function validateEntityClass(): void
    {
        if (empty($this->entityClass) || !\Yii::createObject($this->entityClass) instanceof EntityInterface) {
            throw new InvalidConfigException(get_called_class() . '::$entityClass must implements `' . EntityInterface::class . '`');
        }
    }

    protected function initHydrator(): void
    {
        $this->hydrator = \Yii::createObject($this->hydrator, [static::columns()]);
        if (!$this->hydrator instanceof HydratorInterface) {
            throw new InvalidConfigException(get_called_class() . '::$hydrator must implements `' . HydratorInterface::class . '`');
        }
    }
}
