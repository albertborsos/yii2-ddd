<?php

namespace albertborsos\ddd\interfaces;

/**
 * Interface EntityFactoryInterface
 * @package albertborsos\ddd\interfaces
 * @since 1.1.0
 */
interface EntityFactoryInterface
{
    /**
     * @param string $className
     * @param array $data
     * @return mixed
     */
    public static function create(string $className, array $data);

    public static function createAll($className, array $models): array;
}
