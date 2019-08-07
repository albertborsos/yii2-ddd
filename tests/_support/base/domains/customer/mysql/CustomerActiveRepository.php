<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\mysql;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use mito\cms\core\data\ActiveEntityDataProvider;
use albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerActiveRepositoryInterface;
use yii\data\BaseDataProvider;

class CustomerActiveRepository extends AbstractActiveRepository implements CustomerActiveRepositoryInterface
{
    protected $dataModelClass = Customer::class;

    protected $entityClass = \albertborsos\ddd\tests\support\base\domains\customer\entities\Customer::class;

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @param string $formName
     * @return ActiveEntityDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params, $formName = null): BaseDataProvider
    {
        $query = $this->find();

        if ($params['expand'] ?? false) {
            $query->with(explode(',', $params['expand']));
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveEntityDataProvider([
            'entityClass' => $this->entityClass,
            'hydrator' => $this->hydrator,
            'query' => $query,
            'pagination' => [
                'params' => $params,
            ],
            'sort' => [
                'params' => $params,
            ],
        ]);

        $model = \Yii::createObject($this->dataModelClass);

        $model->load($params, $formName);

        if (!$model->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $model->id,
        ]);

        $query->andFilterWhere(['like', 'name', $model->name]);

        return $dataProvider;
    }
}
