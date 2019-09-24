<?php

namespace albertborsos\ddd\hydrators;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\HydratorInterface;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use Zend\Hydrator\NamingStrategy\ArrayMapNamingStrategy;
use Zend\Hydrator\NamingStrategy\MapNamingStrategy;
use Zend\Hydrator\Reflection;
use Zend\Hydrator\ReflectionHydrator;

class ZendHydrator extends Component implements HydratorInterface
{
    /** @var ReflectionHydrator */
    protected $hydrator;

    public function __construct($columns = [], $config = [])
    {
        parent::__construct($config);
        if (class_exists('Zend\Hydrator\ReflectionHydrator')) {
            $this->hydrator = new ReflectionHydrator();
            $strategy = MapNamingStrategy::createFromExtractionMap(self::columnsToMapping($columns));
        } else {
            $this->hydrator = new Reflection();
            $strategy = new ArrayMapNamingStrategy(self::columnsToMapping($columns));
        }

        if (!empty($columns)) {
            $this->hydrator->setNamingStrategy($strategy);
        }
    }

    private static function columnsToMapping($columns)
    {
        $mapping = [];
        foreach ($columns as $internal => $external) {
            if (is_numeric($internal)) {
                $mapping[$external] = $external;
            } else {
                $mapping[$internal] = $external;
            }
        }
        return $mapping;
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
