<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\interfaces\page;

use albertborsos\ddd\interfaces\RepositoryInterface;
use albertborsos\ddd\tests\support\base\domains\page\entities\Page;

interface PageSlugRepositoryInterface extends RepositoryInterface
{
    public function findAllByPage(Page $page): array;

    public function findAllByPageId($pageId): array;
}
