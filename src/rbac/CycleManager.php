<?php

namespace albertborsos\ddd\rbac;

use albertborsos\cycle\Connection;
use albertborsos\ddd\rbac\repositories\cycle\AuthAssignmentRepository;
use albertborsos\ddd\rbac\repositories\cycle\AuthItemChildRepository;
use albertborsos\ddd\rbac\repositories\cycle\AuthItemRepository;
use albertborsos\ddd\rbac\repositories\cycle\AuthRuleRepository;
use yii\di\Instance;

class CycleManager extends DbManager
{
    /**
     * @var \albertborsos\cycle\Connection|array|string the DB connection object or the application component ID of the DB connection.
     * After the DbManager object is created, if you want to change this property, you should only assign it
     * with a DB connection object.
     */
    public $db = 'cycle';

    /**
     * Initializes the application component.
     * This method overrides the parent implementation by establishing the database connection.
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->db = Instance::ensure($this->db, Connection::class);
        if ($this->cache !== null) {
            $this->cache = Instance::ensure($this->cache, \yii\caching\CacheInterface::class);
        }
        $this->db->schema = array_merge($this->db->schema, [
            'authAssignment' => AuthAssignmentRepository::schema(),
            'authItem' => AuthItemRepository::schema(),
            'authItemChild' => AuthItemChildRepository::schema(),
            'authRule' => AuthRuleRepository::schema(),
        ]);
    }
}
