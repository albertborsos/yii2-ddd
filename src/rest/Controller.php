<?php

namespace albertborsos\ddd\rest;

use albertborsos\ddd\interfaces\CacheRepositoryInterface;
use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\InvalidConfigException;
use yii\rest\OptionsAction;
use yii\web\ForbiddenHttpException;

/**
 * Class Controller
 * @package albertborsos\ddd\rest
 * @since 1.1.0
 */
class Controller extends \yii\rest\Controller
{
    public $repositoryInterface;

    public function init()
    {
        parent::init();
        if ($this->repositoryInterface === null) {
            throw new InvalidConfigException(get_class($this) . '::$repositoryInterface must be set.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => OptionsAction::class,
            ],
        ];
    }

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data entity.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $entity the entity to be accessed. If null, it means no specific entity is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $entity = null, $params = [])
    {
    }

    /**
     * @return RepositoryInterface|CacheRepositoryInterface
     * @throws InvalidConfigException
     */
    public function getRepository()
    {
        return \Yii::createObject($this->repositoryInterface);
    }
}
