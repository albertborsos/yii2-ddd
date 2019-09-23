<?php

namespace albertborsos\ddd\interfaces;

/**
 * Interface HydratorInterface
 * @package albertborsos\ddd\interfaces
 * @since 2.0.0
 */
interface HydratorInterface
{
    /**
     * @param $className
     * @param $data
     * @return mixed
     */
    public function hydrate($className, $data);

    /**
     * @param $className
     * @param array $data
     * @return array
     */
    public function hydrateAll($className, array $data): array;

    /**
     * @param object $object
     * @param array $data
     * @return mixed
     */
    public function hydrateInto($object, array $data);

    /**
     * @param object $object
     * @return array
     */
    public function extract($object): array;
}
