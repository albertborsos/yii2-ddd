<?php

namespace albertborsos\ddd\tests\support\base\domains\page\mysql;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\data\ActiveEntityDataProvider;
use albertborsos\ddd\tests\support\base\domains\page\interfaces\PageSlugActiveRepositoryInterface;
use yii\data\BaseDataProvider;

class PageSlugActiveRepository extends AbstractActiveRepository implements PageSlugActiveRepositoryInterface
{
    protected $dataModelClass = PageSlug::class;

    protected $entityClass = \albertborsos\ddd\tests\support\base\domains\page\entities\PageSlug::class;

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
            'page_id' => $model->page_id,
            'created_at' => $model->created_at,
            'created_by' => $model->created_by,
            'updated_at' => $model->updated_at,
            'updated_by' => $model->updated_by,
            'status' => $model->status,
        ]);

        $query->andFilterWhere(['like', 'slug', $model->slug]);

        return $dataProvider;
    }
}
