<?php

namespace albertborsos\ddd\interfaces;

/**
 * Interface HydratorInterface
 * @package albertborsos\ddd\interfaces
 * @since 2.0.0
 */
interface HydratorInterface
{
    public function hydrate($className, $data);

    public function hydrateAll($className, array $data);

    public function hydrateInto($object, array $data);

    public function extract($object): array;
}
