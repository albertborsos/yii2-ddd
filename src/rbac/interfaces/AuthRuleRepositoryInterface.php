<?php

namespace albertborsos\ddd\rbac\interfaces;

use albertborsos\ddd\rbac\entities\AuthRule;
use albertborsos\ddd\interfaces\RepositoryInterface;

interface AuthRuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     * @return object|AuthRule|null
     */
    public function findByName(string $name);

    /**
     * @return AuthRule[]
     */
    public function findAll();
}
