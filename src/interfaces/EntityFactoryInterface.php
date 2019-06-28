<?php

namespace albertborsos\ddd\interfaces;

interface EntityFactoryInterface
{
    /**
     * @param string $className
     * @param array $data
     * @return mixed
     */
    public static function create(string $className, array $data);

    public static function createCollection($className, array $models): array;
}
