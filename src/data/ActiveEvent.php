<?php

namespace albertborsos\ddd\data;

use yii\base\Event;
use yii\db\ActiveRecordInterface;

class ActiveEvent extends Event
{
    /**
     * @var ActiveRecordInterface
     */
    public $sender;
}
