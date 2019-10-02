<?php

namespace albertborsos\ddd\rbac\interfaces;

use albertborsos\ddd\rbac\entities\AuthItemChild;
use albertborsos\ddd\interfaces\RepositoryInterface;

interface AuthItemChildRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string|array $name
     * @return array|AuthItemChild[]
     */
    public function findAllRelativesByName($name);

    /**
     * @param string|array $name
     * @return array|AuthItemChild[]
     */
    public function findAllByParentName($name);

    /**
     * @param string|array $name
     * @return array|AuthItemChild[]
     */
    public function findAllByChildName($name);

    /**
     * @return array|AuthItemChild[]
     */
    public function findAll();

    /**
     * @param string $parentName
     * @param string $childName
     * @return AuthItemChild
     */
    public function findByParentNameAndChildName($parentName, $childName);

    /**
     * @param $parentName
     * @param $childName
     * @return bool
     */
    public function hasChild($parentName, $childName);
}
