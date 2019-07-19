<?php

namespace albertborsos\ddd\rest\active;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\rest\ViewActionTrait;
use yii\web\NotFoundHttpException;

class ViewAction extends Action
{
    use ViewActionTrait;

    public function findModel($id): ?EntityInterface
    {
        $expand = \Yii::$app->request->getQueryParam('expand');

        if (empty($expand)) {
            return parent::findModel($id);
        }

        $relations = explode(',', $expand);

        $model = $this->getRepository()->find()->with($relations)->where($this->getPrimaryKeyCondition($id))->one();

        if (isset($model)) {
            return $this->getRepository()->hydrate($model);
        }

        throw new NotFoundHttpException("Object not found: $id");
    }
}
