<?php

namespace albertborsos\ddd\rest\cache;

use albertborsos\ddd\interfaces\CacheRepositoryInterface;
use yii\base\InvalidConfigException;

/**
 * Class Controller
 * @package albertborsos\ddd\rest\cache
 * @since 2.0.0
 */
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
