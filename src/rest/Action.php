<?php

namespace albertborsos\ddd\rest;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Class Action
 * @package mito\cms\core\rest\admin
 *
 * @property \mito\cms\core\rest\Controller $controller
 * @since 1.1.0
 */
abstract class Action extends \yii\base\Action
{
    /**
     * @var string
     */
    public $repositoryInterface;

    /**
     * @var callable a PHP callable that will be called to return the model corresponding
     * to the specified primary key value. If not set, [[findModel()]] will be used instead.
     * The signature of the callable should be:
     *
     * ```php
     * function ($id, $action) {
     *     // $id is the primary key value. If composite primary key, the key values
     *     // will be separated by comma.
     *     // $action is the action object currently running
     * }
     * ```
     *
     * The callable should return the model found, or throw an exception if not found.
     */
    public $findModel;

    /**
     * @var callable a PHP callable that will be called when running an action to determine
     * if the current user has the permission to execute the action. If not set, the access
     * check will not be performed. The signature of the callable should be as follows,
     *
     * ```php
     * function ($action, $model = null) {
     *     // $model is the requested model instance.
     *     // If null, it means no specific model (e.g. IndexAction)
     * }
     * ```
     */
    public $checkAccess;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (!$this->controller instanceof Controller && empty($this->repositoryInterface)) {
            throw new InvalidConfigException(get_class($this) . '::$repositoryInterface must be set.');
        }

        if (empty($this->repositoryInterface)) {
            $this->repositoryInterface = $this->controller->repositoryInterface;
        }
    }

    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     * @param string $id the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     * @return EntityInterface the model found
     * @throws NotFoundHttpException if the model cannot be found
     * @throws InvalidConfigException
     */
    abstract public function findModel($id): ?EntityInterface;

    /**
     * @return RepositoryInterface
     * @throws InvalidConfigException
     */
    abstract public function getRepository();
}
