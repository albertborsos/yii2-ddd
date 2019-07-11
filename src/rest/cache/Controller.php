<?php

namespace albertborsos\ddd\rest\cache;

use albertborsos\ddd\interfaces\CacheRepositoryInterface;
use yii\base\InvalidConfigException;

class Controller extends \albertborsos\ddd\rest\Controller
{
    /**
     * @return CacheRepositoryInterface
     * @throws InvalidConfigException
     */
    public function getRepository(): CacheRepositoryInterface
    {
        return \Yii::createObject($this->repositoryInterface);
    }
}
