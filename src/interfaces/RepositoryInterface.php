<?php

namespace albertborsos\ddd\interfaces;

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
}
