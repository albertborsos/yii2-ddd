<?php

namespace albertborsos\ddd\tests\support\base;

use yii\base\Model;

class InvalidHydrator extends Model
{
    public function __construct($columns, $config = [])
    {
        parent::__construct($config);
    }
}
