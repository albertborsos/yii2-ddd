<?php

namespace albertborsos\ddd\tests\support\base\services\page;

use albertborsos\ddd\tests\support\base\domains\page\entities\PageSlug;

class DeletePageSlugService extends AbstractPageSlugService
{
    public function __construct(PageSlug $entity, $config = [])
    {
        parent::__construct(null, $entity, $config);
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        return $this->getRepository()->delete($this->getEntity());
    }
}
