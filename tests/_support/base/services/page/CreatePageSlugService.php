<?php

namespace albertborsos\ddd\tests\support\base\services\page;

use albertborsos\ddd\tests\support\base\services\page\forms\CreatePageSlugForm;

class CreatePageSlugService extends AbstractPageSlugService
{
    public function __construct(CreatePageSlugForm $form, $config = [])
    {
        parent::__construct($form, null, $config);
    }
}
