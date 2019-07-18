<?php

namespace albertborsos\ddd\interfaces;

interface HydratorInterface
{
    public function hydrate($className, $data);

    public function hydrateAll($className, $data);

    public function extract($object): array;
}
