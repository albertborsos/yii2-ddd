<?php

namespace albertborsos\ddd\interfaces;

use yii\db\Transaction;

/**
 * Interface ActiveRepositoryInterface
 * @package albertborsos\ddd\interfaces
 * @since 2.0.0
 */
interface ActiveRepositoryInterface extends RepositoryInterface
{
    /**
     * @return string
     */
    public function getDataModelClass(): string;

    /**
     * @param $className
     */
    public function setDataModelClass($className): void;

    /**
     * @param null $isolationLevel
     * @return Transaction
     */
    public function beginTransaction($isolationLevel = null): Transaction;
}
