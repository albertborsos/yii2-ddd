<?php

namespace albertborsos\ddd\tests\fixtures;

use albertborsos\ddd\tests\support\base\domains\page\mysql\PageSlug;
use yii\test\ActiveFixture;

class PageSlugFixture extends ActiveFixture
{
    public $modelClass = PageSlug::class;

    public $dataFile = '@tests/fixtures/data/page-slug.php';
}
