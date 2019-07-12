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
     * Creates data provider instance with search query applied
     *
     * @param $params
     * @param null $formName
     * @return BaseDataProvider
     */
    public function search($params, $formName = null): BaseDataProvider;
}
