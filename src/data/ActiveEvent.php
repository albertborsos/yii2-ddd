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
 * @since 2.0.0
 */
class ActiveEvent extends Event
{
    const SCENARIO_INSERT = 'insert';
    const SCENARIO_UPDATE = 'update';

    /**
     * @var ActiveRecordInterface
     */
    public $sender;

    /**
     * @var string
     */
    public $scenario;
}
