<?php

namespace albertborsos\ddd\rest;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\models\AbstractService;
use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

/**
 * Trait CreateActionTrait
 * @package albertborsos\ddd\rest
 * @since 1.1.0
 */
trait CreateActionTrait
{
    /**
     * @var string the name of the view action. This property is needed to create the URL when the model is successfully created.
     */
    public $viewAction = 'view';

    /**
     * Creates a new model.
     * @return Model|EntityInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        /* @var $form Model */
        $form = Yii::createObject($this->formClass);
        $form->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($form->validate()) {
            /** @var AbstractService $service */
            $service = Yii::createObject($this->serviceClass, [$form]);
            if ($service->execute()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
                $id = is_array($service->getId()) ? implode(',', array_values($service->getId())) : $service->getId();
                $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));

                return $this->findModel($service->getId());
            } elseif (!$form->hasErrors()) {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }
        }

        return $form;
    }
}