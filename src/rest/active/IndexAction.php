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

        return $this->getRepository()->search($requestParams, '');
    }
}
