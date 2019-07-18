<?php

namespace albertborsos\ddd\hydrators;

use albertborsos\ddd\interfaces\HydratorInterface;
use yii\base\Component;

class Hydrator extends Component implements HydratorInterface
{
    protected $hydrator;

    public function __construct($map, $config = [])
    {
        parent::__construct($config);
        $this->hydrator = new \samdark\hydrator\Hydrator($map);
    }

    public function hydrate($className, $data)
    {
        return $this->hydrator->hydrate($data, $className);
    }

    public function hydrateAll($className, $data)
    {
        return array_map(function ($data) use ($className) {
            return $this->hydrate($className, $data);
        }, $data);
    }

    public function extract($object): array
    {
        return $this->hydrator->extract($object);
    }
}
