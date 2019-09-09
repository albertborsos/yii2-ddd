<?php

namespace albertborsos\ddd\validators;

use albertborsos\ddd\traits\RepositoryPropertyTrait;

class UniqueValidator extends \yii\validators\UniqueValidator
{
    use RepositoryPropertyTrait;
}
