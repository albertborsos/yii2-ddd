<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\interfaces\page;

use albertborsos\ddd\interfaces\RepositoryInterface;

interface PageSlugRepositoryInterface extends RepositoryInterface
{
    public function findAllByPage($page): array;

    public function findAllByPageId($pageId): array;
}
