<?php

namespace albertborsos\ddd\interfaces;

interface HydratorInterface
{
    public function hydrate($className, $data);

    public function hydrateAll($className, array $data);

    public function hydrateInto($object, array $data);

    public function extract($object): array;
}
