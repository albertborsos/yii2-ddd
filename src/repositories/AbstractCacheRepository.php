<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\interfaces\CacheRepositoryInterface;

/**
 * Class AbstractRepository
 * @package albertborsos\ddd\repositories
 * @since 2.0.0
 */
abstract class AbstractCacheRepository extends AbstractRepository implements CacheRepositoryInterface
{
    /**
     * @param string $key
     * @return string
     */
    protected function postfixedKey(string $key): string
    {
        return implode('-', [$this->getEntityClass(), $key]);
    }
}
