<?php

namespace albertborsos\ddd\rest\active;

use albertborsos\ddd\rest\IndexActionTrait;
use Yii;
use yii\data\ActiveDataProvider;

class IndexAction extends Action
{
    use IndexActionTrait;

    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    protected function prepareDataProvider()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        if ($this->prepareDataProvider !== null) {
            return call_user_func($this->prepareDataProvider, $this, $requestParams);
        }

        if ($searchModelClass = \Yii::createObject([$this->repositoryInterface, 'searchModelClass'])) {
            $searchModel = \Yii::createObject($searchModelClass);
            return $searchModel->search($requestParams, '');
        }

        $query = $this->getRepository()->find();

        if (!empty($requestParams)) {
            $query->andWhere($requestParams);
        }

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
            'pagination' => [
                'params' => $requestParams,
            ],
            'sort' => [
                'params' => $requestParams,
            ],
        ]);
    }
}
