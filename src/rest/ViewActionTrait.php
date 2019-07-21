<?php

namespace albertborsos\ddd\rest;

/**
 * Trait ViewActionTrait
 * @package albertborsos\ddd\rest
 * @since 1.1.0
 */
trait ViewActionTrait
{
    /**
     * Displays a model.
     * @param string $id the primary key of the model.
     * @return \albertborsos\ddd\interfaces\EntityInterface|null the model being displayed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        return $model;
    }
}
