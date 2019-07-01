<?php

namespace albertborsos\ddd\interfaces;

/**
 * Interface EntityInterface
 * @package albertborsos\ddd\interfaces
 * @since 1.1.0
 */
interface EntityInterface extends BusinessObject
{
    public function primaryKey();
}
