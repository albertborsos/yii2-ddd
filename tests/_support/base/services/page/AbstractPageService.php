<?php

namespace albertborsos\ddd\tests\support\base\services\page;

use albertborsos\ddd\interfaces\RepositoryInterface;
use albertborsos\ddd\models\AbstractStoreService;
use albertborsos\ddd\tests\support\base\domains\page\entities\Page;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageRepositoryInterface;
use albertborsos\ddd\tests\support\base\services\page\forms\AbstractPageForm;

abstract class AbstractPageService extends AbstractStoreService
{
    /** @var string|RepositoryInterface */
    protected $repository = PageRepositoryInterface::class;

    public function __construct(AbstractPageForm $form = null, Page $entity = null, $config = [])
    {
        parent::__construct($form, $entity, $config);
    }
}
