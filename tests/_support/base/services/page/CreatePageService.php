<?php

namespace albertborsos\ddd\tests\support\base\services\page;

use albertborsos\ddd\tests\support\base\services\page\forms\CreatePageForm;

class CreatePageService extends AbstractPageService
{
    public function __construct(CreatePageForm $form, $config = [])
    {
        parent::__construct($form, null, $config);
    }
}
