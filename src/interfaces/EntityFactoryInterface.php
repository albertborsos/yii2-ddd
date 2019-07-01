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
     * @param array $itemData
     * @return mixed
     */
    public static function create(string $className, array $itemData);

    public static function createAll($className, array $itemsData): array;
}
