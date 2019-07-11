<?php

namespace albertborsos\ddd\rest;

use albertborsos\ddd\models\AbstractService;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\ServerErrorHttpException;

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
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $form = \Yii::createObject($this->formClass, [$model]);
        if ($form->validate()) {
            /** @var AbstractService $service */
            $service = \Yii::createObject($this->serviceClass, [$form, $model, $this->getRepository()]);
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
