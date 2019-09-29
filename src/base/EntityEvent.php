<?php

namespace albertborsos\ddd\base;

/**
 * EntityEvent represents the parameter needed by [[Entity]] events.
 *
 * @since 2.0.0
 */
class EntityEvent extends \yii\base\Event
{
    /**
     * @var bool whether the entity is in valid status. Defaults to true.
     * A entity is in valid status if it passes validations or certain checks.
     */
    public $isValid = true;
}
