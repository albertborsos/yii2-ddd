<?php

namespace albertborsos\ddd\interfaces;

use yii\data\BaseDataProvider;

/**
 * Interface RepositoryInterface
 * @package albertborsos\ddd\interfaces
 * @since 1.1.0
 */
interface RepositoryInterface
{
    /**
     * @return string
     */
    public static function entityModelClass(): string;

    /**
     * @param $data
     * @return EntityInterface
     */
    public function hydrate($data): EntityInterface;

    /**
     * @param $model
     * @param $data
     * @return EntityInterface
     */
    public function hydrateInto(EntityInterface $model, $data): EntityInterface;

    /**
     * @param $models
     * @return mixed
     */
    public function hydrateAll($models);

    /**
     * Creates data provider instance with search query applied
     *
     * @param $params
     * @param null $formName
     * @return BaseDataProvider
     */
    public function search($params, $formName = null): BaseDataProvider;
}
