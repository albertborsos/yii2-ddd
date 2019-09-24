<?php

namespace albertborsos\ddd\tests\support\base\services\page;

use albertborsos\ddd\interfaces\RepositoryInterface;
use albertborsos\ddd\models\AbstractStoreService;
use albertborsos\ddd\tests\support\base\domains\page\entities\PageSlug;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSlugRepositoryInterface;
use albertborsos\ddd\tests\support\base\services\page\forms\AbstractPageSlugForm;

abstract class AbstractPageSlugService extends AbstractStoreService
{
    /** @var string|RepositoryInterface */
    protected $repository = PageSlugRepositoryInterface::class;

    public function __construct(AbstractPageSlugForm $form = null, PageSlug $entity = null, $config = [])
    {
        parent::__construct($form, $entity, $config);
    }
}
