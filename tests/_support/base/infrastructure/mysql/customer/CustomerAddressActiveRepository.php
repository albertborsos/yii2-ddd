<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\mysql\customer;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\data\ActiveEntityDataProvider;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressActiveRepositoryInterface;
use yii\data\BaseDataProvider;

class CustomerAddressActiveRepository extends AbstractActiveRepository implements CustomerAddressActiveRepositoryInterface
{
    protected $dataModelClass = CustomerAddress::class;

    protected $entityClass = \albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerAddress::class;

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
            'customer_id' => $model->customer_id,
            'zip_code' => $model->zip_code,
        ]);

        $query->andFilterWhere(['like', 'city', $model->city])
            ->andFilterWhere(['like', 'street', $model->street]);

        return $dataProvider;
    }
}
