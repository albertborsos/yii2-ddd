<?php

namespace albertborsos\ddd\rbac\interfaces;

use albertborsos\ddd\rbac\entities\AuthItem;
use albertborsos\ddd\interfaces\RepositoryInterface;

interface AuthItemRepositoryInterface extends RepositoryInterface
{
    /**
     * @param $name
     * @return AuthItem|null
     */
    public function findByName($name);

    /**
     * @param int $type
     * @return array|AuthItem[]
     */
    public function findAllByType($type);

    /**
     * @param string $name
     * @return array|AuthItem[]
     */
    public function findAllByName($name);

    /**
     * @param $ruleName
     * @return array|AuthItem[]
     */
    public function findAllByRuleName($ruleName);

    /**
     * @param $userId
     * @return AuthItem[]
     */
    public function findRolesByUser($userId);
    /**
     * @param array $names
     * @return array|AuthItem[]
     */
    public function findAllPermissionsByNames(array $names);

    /**
     * @param $userId
     * @return AuthItem[]
     */
    public function findPermissionsByUser($userId);

    /**
     * @param $name
     * @return AuthItem[]
     */
    public function findChildrenByParent($name);

    /**
     * @param $name
     * @return AuthItem[]
     */
    public function findAll();
}
