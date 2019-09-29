<?php

namespace albertborsos\ddd\base;

use yii\base\Event;

class AfterSaveEvent extends Event
{
    /**
     * @var array The attribute values that had changed and were saved.
     */
    public $changedAttributes;
}
