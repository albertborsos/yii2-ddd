<?php

namespace albertborsos\ddd\rest;

use yii\base\InvalidConfigException;

/**
 * Trait FormAndServiceActionTrait
 * @package albertborsos\ddd\rest
 * @since 1.1.0
 */
trait FormAndServiceActionTrait
{
    /**
     * Classname of the form model which validates the request.
     * @var string
     */
    public $formClass;

    /**
     * Classname of the service which executes the business logic.
     * @var string
     */
    public $serviceClass;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty($this->formClass)) {
            throw new InvalidConfigException(get_class($this) . '::$formClass must be set.');
        }
        if (empty($this->serviceClass)) {
            throw new InvalidConfigException(get_class($this) . '::$serviceClass must be set.');
        }
    }
}
