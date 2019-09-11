<?php

namespace albertborsos\ddd\tests\fixtures;

use albertborsos\ddd\tests\support\base\infrastructure\db\page\Page;
use yii\test\ActiveFixture;

class PageFixture extends ActiveFixture
{
    public $modelClass = Page::class;

    public $dataFile = '@tests/fixtures/data/page.php';
}
