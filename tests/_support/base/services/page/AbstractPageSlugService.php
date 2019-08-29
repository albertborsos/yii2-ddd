<?php

namespace albertborsos\ddd\tests\support\base\services\page;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\models\AbstractActiveService;
use albertborsos\ddd\tests\support\base\domains\page\entities\PageSlug;
use albertborsos\ddd\tests\support\base\domains\page\interfaces\PageSlugActiveRepositoryInterface;
use albertborsos\ddd\tests\support\base\services\page\forms\AbstractPageSlugForm;

abstract class AbstractPageSlugService extends AbstractActiveService
{
    /** @var string|ActiveRepositoryInterface */
    protected $repository = PageSlugActiveRepositoryInterface::class;

    public function __construct(AbstractPageSlugForm $form = null, PageSlug $entity = null, $config = [])
    {
        parent::__construct($form, $entity, $config);
    }
}