<?php

namespace albertborsos\ddd\hydrators;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\HydratorInterface;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use Zend\Hydrator\Reflection;
use Zend\Hydrator\ReflectionHydrator;

class ZendHydrator extends Component implements HydratorInterface
{
    /** @var ReflectionHydrator */
    protected $hydrator;

    public function __construct($config = [])
    {
        parent::__construct($config);
        if (class_exists('Zend\Hydrator\ReflectionHydrator')) {
            $this->hydrator = new ReflectionHydrator();
        } else {
            $this->hydrator = new Reflection();
        }
    }

    public function hydrate($className, $data)
    {
        return $this->hydrator->hydrate(is_object($data) ? $this->extract($data) : $data, \Yii::createObject($className));
    }

    public function hydrateAll($className, array $data): array
    {
        return array_map(function ($object) use ($className) {
            return $this->hydrate($className, $object);
        }, $data);
    }

    public function hydrateInto($object, array $data)
    {
        return $this->hydrator->hydrate($data, $object);
    }

    public function extract($object): array
    {
        $extracted = $this->hydrator->extract($object);

        if ($object instanceof EntityInterface) {
            return $this->hydrator->extract($object);
        }

        return ArrayHelper::getValue($extracted, '_attributes', $extracted);
    }
}
