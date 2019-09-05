<?php

namespace albertborsos\ddd\traits;

trait PostfixedKeyTrait
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
