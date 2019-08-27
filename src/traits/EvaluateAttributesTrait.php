<?php

namespace albertborsos\ddd\traits;

use albertborsos\ddd\base\EntityEvent;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\InvalidConfigException;

trait EvaluateAttributesTrait
{
    /**
     * Evaluates the attribute value and assigns it to the current attributes.
     * @param EntityEvent $event
     * @throws InvalidConfigException
     */
    public function evaluateAttributes($event)
    {
        if (!$event instanceof EntityEvent) {
            throw new InvalidConfigException(get_class($this) . ' must be used with `' . EntityEvent::class . '`');
        }

        if (
            $this->skipUpdateOnClean
            && $event->name == EntityInterface::EVENT_BEFORE_UPDATE
            && empty($event->dirtyAttributes)
        ) {
            return;
        }

        if (empty($this->attributes[$event->name])) {
            return;
        }

        $attributes = (array)$this->attributes[$event->name];
        $value = $this->getValue($event);
        foreach ($attributes as $attribute) {
            // ignore attribute names which are not string (e.g. when set by TimestampBehavior::updatedAtAttribute)
            if (!is_string($attribute)) {
                continue;
            }
            if ($this->preserveNonEmptyValues && !empty($this->owner->$attribute)) {
                continue;
            }
            $this->owner->$attribute = $value;
        }
    }
}
