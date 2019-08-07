<?php

namespace albertborsos\ddd\rest;

use albertborsos\ddd\models\AbstractService;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\ServerErrorHttpException;

/**
 * Trait DeleteActionTrait
 * @package albertborsos\ddd\rest
 * @since 1.1.0
 */
trait DeleteActionTrait
{
    /**
     * Deletes a model.
     * @param mixed $id id of the model to be deleted.
     * @return object
     * @throws InvalidConfigException
     * @throws ServerErrorHttpException on failure.
     * @throws \yii\base\ExitException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        $entity = $this->findEntity($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $entity);
        }

        $form = \Yii::createObject($this->formClass, [$entity]);
        if ($form->validate()) {
            /** @var AbstractService $service */
            $service = \Yii::createObject($this->serviceClass, [$form, $entity, $this->getRepository()]);
            if ($service->execute()) {
                Yii::$app->getResponse()->setStatusCode(204);
                Yii::$app->end();
            }
        }

        if (!$form->hasErrors()) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        return $form;
    }
}
