<?php

namespace albertborsos\ddd\rbac\interfaces;

use albertborsos\ddd\rbac\entities\AuthAssignment;
use albertborsos\ddd\interfaces\RepositoryInterface;

interface AuthAssignmentRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string|array $name
     * @return array|AuthAssignment[]
     */
    public function findAllByName($name);

    /**
     * @param string $name
     * @return array|AuthAssignment[]
     */
    public function findAllByItemName($name);

    /**
     * @param $userId
     * @return array|AuthAssignment[]
     */
    public function findAllByUserId($userId);

    /**
     * @param $itemName
     * @param $userId
     * @return AuthAssignment|null
     */
    public function findItemByUser($itemName, $userId);

    /**
     * @return array|AuthAssignment[]
     */
    public function findAll();
}
