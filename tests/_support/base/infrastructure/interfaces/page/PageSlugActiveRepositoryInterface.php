<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\interfaces\page;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;

interface PageSlugActiveRepositoryInterface extends ActiveRepositoryInterface
{
    public function findAllByPage($page): array;

    public function findAllByPageId($pageId): array;
}
