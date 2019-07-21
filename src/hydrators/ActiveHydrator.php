<?php

namespace albertborsos\ddd\hydrators;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\HydratorInterface;
use albertborsos\ddd\models\AbstractEntity;
use yii\base\Component;
use yii\base\Model;

/**
 * Class ActiveHydrator
 *
 * Hydrates an instance from an array or from a `\yii\base\Model` instance.
 * If the hydrated object is an instance of `EntityInterface` then it tries to hydrate the relations too.
 *
 * @package albertborsos\ddd\hydrators
 * @since 1.1.0
 */
class ActiveHydrator extends Component implements HydratorInterface
{
    /** @var \samdark\hydrator\Hydrator */
    protected $hydrator;

    public function __construct($map, $config = [])
    {
        parent::__construct($config);
        $this->hydrator = new \samdark\hydrator\Hydrator($map);
    }

    /**
     * @param $className
     * @param array|Model $data
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function hydrate($className, $data)
    {
        $model = $this->hydrator->hydrate($data, $className);

        if (!$model instanceof EntityInterface) {
            return $model;
        }

        $relationsMap = \Yii::createObject([$className, 'relationsMap']);
        if (empty($relationsMap)) {
            return $model;
        }

        foreach ($relationsMap as $relationName => $entityClass) {
            $relationHydrator = \Yii::createObject(static::class, [\Yii::createObject([$entityClass, 'fieldMap'])]);

            if (!isset($data->$relationName)) {
                continue;
            }

            $relationData = is_array($data->$relationName)
                ? $relationHydrator->hydrateAll($entityClass, $data->$relationName)
                : $relationHydrator->hydrate($entityClass, $data->$relationName->attributes);

            $this->hydrator->hydrateInto([$relationName => $relationData], $model);
        }

        return $model;
    }

    /**
     * @param $className
     * @param array|Model[] $data
     * @return array
     */
    public function hydrateAll($className, array $data)
    {
        return array_map(function ($activeRecord) use ($className) {
            return $this->hydrate($className, $activeRecord);
        }, $data);
    }

    /**
     * @param $object
     * @param array $data
     * @return object
     */
    public function hydrateInto($object, array $data)
    {
        return $this->hydrator->hydrateInto($data, $model);
    }

    /**
     * @param $object
     * @return array
     */
    public function extract($object): array
    {
        return $this->hydrator->extract($object);
    }
}
