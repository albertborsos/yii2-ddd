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
 * @since 2.0.0
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
        $entity = $this->findEntity($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $entity);
        }

        /** @var Model|FormObject $form */
        $form = \Yii::createObject($this->formClass, [$entity]);
        $form->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($form->validate()) {
            /** @var AbstractService $service */
            $service = Yii::createObject($this->serviceClass, [$form, $entity]);
            if ($service->execute()) {
                return $this->findEntity($service->getId());
            }
        }

        if (!$form->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $form;
    }
}
