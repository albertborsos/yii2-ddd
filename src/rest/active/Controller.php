<?php

namespace albertborsos\ddd\rest\active;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use yii\base\InvalidConfigException;

/**
 * Class Controller
 * @package albertborsos\ddd\rest\active
 * @since 2.0.0
 */
class Controller extends \albertborsos\ddd\rest\Controller
{
    /**
     * @return ActiveRepositoryInterface
     * @throws InvalidConfigException
     */
    public function getRepository(): ActiveRepositoryInterface
    {
        return \Yii::createObject($this->repositoryInterface);
    }
}
