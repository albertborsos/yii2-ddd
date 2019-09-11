<?php

namespace albertborsos\ddd\tests\support\base\services\page;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\models\AbstractActiveService;
use albertborsos\ddd\tests\support\base\domains\page\entities\Page;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageRepositoryInterface;
use albertborsos\ddd\tests\support\base\services\page\forms\AbstractPageForm;

abstract class AbstractPageService extends AbstractActiveService
{
    /** @var string|ActiveRepositoryInterface */
    protected $repository = PageRepositoryInterface::class;

    public function __construct(AbstractPageForm $form = null, Page $entity = null, $config = [])
    {
        parent::__construct($form, $entity, $config);
    }
}
