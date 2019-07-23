<?php

namespace albertborsos\ddd\rest;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\FormObject;
use albertborsos\ddd\models\AbstractService;
use Yii;
use yii\base\Model;
use yii\web\ServerErrorHttpException;

/**
 * Trait UpdateActionTrait
 * @package albertborsos\ddd\rest
 * @since 1.1.0
 */
trait UpdateActionTrait
{
    /**
     * Updates an existing model.
     * @param string $id the primary key of the model.
     * @return Model|EntityInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        /** @var Model|FormObject $form */
        $form = \Yii::createObject($this->formClass, [$model]);
        $form->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($form->validate()) {
            /** @var AbstractService $service */
            $service = Yii::createObject($this->serviceClass, [$form, $model]);
            if ($service->execute()) {
                return $this->findModel($service->getId());
            }
        }

        if (!$form->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $form;
    }
}