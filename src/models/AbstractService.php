<?php

namespace albertborsos\ddd\models;

use yii\helpers\ArrayHelper;
use yii\web\Link;
use yii\web\Linkable;

/**
 * Class AbstractDomain
 * @package albertborsos\ddd\models
 */
abstract class AbstractService extends AbstractModel
{
    /**
     * @return boolean
     */
    abstract public function execute();
}
