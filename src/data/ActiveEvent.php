<?php

namespace albertborsos\ddd\data;

use yii\base\Event;
use yii\db\ActiveRecordInterface;

/**
 * Class ActiveEvent
 *
 * ActiveRepository instances fires this event with an `ActiveRecord` model instance as the value of the `sender` property.
 *
 * @package albertborsos\ddd\data
 * @since 1.1.0
 */
class ActiveEvent extends Event
{
    /**
     * @var ActiveRecordInterface
     */
    public $sender;
}
