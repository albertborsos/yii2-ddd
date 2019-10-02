<?php

namespace albertborsos\ddd\rbac;

use albertborsos\cycle\Connection;
use albertborsos\ddd\rbac\entities\AuthAssignment;
use albertborsos\ddd\rbac\entities\AuthItem;
use albertborsos\ddd\rbac\entities\AuthItemChild;
use albertborsos\ddd\rbac\entities\AuthRule;
use albertborsos\ddd\rbac\interfaces\AuthAssignmentRepositoryInterface;
use albertborsos\ddd\rbac\interfaces\AuthItemChildRepositoryInterface;
use albertborsos\ddd\rbac\interfaces\AuthItemRepositoryInterface;
use albertborsos\ddd\rbac\interfaces\AuthRuleRepositoryInterface;
use albertborsos\ddd\rbac\repositories\cycle\AuthAssignmentRepository;
use albertborsos\ddd\rbac\repositories\cycle\AuthItemChildRepository;
use albertborsos\ddd\rbac\repositories\cycle\AuthItemRepository;
use albertborsos\ddd\rbac\repositories\cycle\AuthRuleRepository;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidCallException;
use yii\caching\CacheInterface;
use yii\db\Query;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\rbac\Assignment;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\rbac\Rule;

abstract class DbManager extends \yii\rbac\DbManager
{
    /**
     * @var string the name of the table storing authorization items. Defaults to "auth_item".
     */
    public $itemRepository = AuthItemRepositoryInterface::class;
    /**
     * @var string the name of the table storing authorization item hierarchy. Defaults to "auth_item_child".
     */
    public $itemChildRepository = AuthItemChildRepositoryInterface::class;
    /**
     * @var string the name of the table storing authorization item assignments. Defaults to "auth_assignment".
     */
    public $assignmentRepository = AuthAssignmentRepositoryInterface::class;
    /**
     * @var string the name of the table storing rules. Defaults to "auth_rule".
     */
    public $ruleRepository = AuthRuleRepositoryInterface::class;
    /**
     * @var CacheInterface|array|string the cache used to improve RBAC performance. This can be one of the following:
     *
     * - an application component ID (e.g. `cache`)
     * - a configuration array
     * - a [[\yii\caching\Cache]] object
     *
     * When this is not set, it means caching is not enabled.
     *
     * Note that by enabling RBAC cache, all auth items, rules and auth item parent-child relationships will
     * be cached and loaded into memory. This will improve the performance of RBAC permission check. However,
     * it does require extra memory and as a result may not be appropriate if your RBAC system contains too many
     * auth items. You should seek other RBAC implementations (e.g. RBAC based on Redis storage) in this case.
     *
     * Also note that if you modify RBAC items, rules or parent-child relationships from outside of this component,
     * you have to manually call [[invalidateCache()]] to ensure data consistency.
     *
     * @since 2.0.3
     */
    public $cache;
    /**
     * @var string the key used to store RBAC data in cache
     * @see cache
     * @since 2.0.3
     */
    public $cacheKey = 'rbac';

    /**
     * @var Item[] all auth items (name => Item)
     */
    protected $items;
    /**
     * @var Rule[] all auth rules (name => Rule)
     */
    protected $rules;
    /**
     * @var array auth item parent-child relationships (childName => list of parents)
     */
    protected $parents;

    private $_checkAccessAssignments = [];

    /**
     * Performs access check for the specified user.
     * This method is internally called by [[checkAccess()]].
     * @param string|int $user the user ID. This should can be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param string $itemName the name of the operation that need access check
     * @param array $params name-value pairs that would be passed to rules associated
     * with the tasks and roles assigned to the user. A param with name 'user' is added to this array,
     * which holds the value of `$userId`.
     * @param Assignment[] $assignments the assignments to the specified user
     * @return bool whether the operations can be performed by the user.
     * @throws \yii\base\InvalidConfigException
     */
    protected function checkAccessRecursive($user, $itemName, $params, $assignments)
    {
        if (($item = $this->getItem($itemName)) === null) {
            return false;
        }

        Yii::debug($item instanceof Role ? "Checking role: $itemName" : "Checking permission: $itemName", __METHOD__);

        if (!$this->executeRule($user, $item, $params)) {
            return false;
        }

        if (isset($assignments[$itemName]) || in_array($itemName, $this->defaultRoles)) {
            return true;
        }

        /** @var AuthItemChildRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemChildRepository);
        $parents = ArrayHelper::getColumn($repository->findAllByChildName($itemName), 'parent');
        foreach ($parents as $parent) {
            if ($this->checkAccessRecursive($user, $parent, $params, $assignments)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the named auth item.
     * @param string $name the auth item name.
     * @return Item the auth item corresponding to the specified name. Null is returned if no such item.
     * @throws \yii\base\InvalidConfigException
     */
    protected function getItem($name)
    {
        if (empty($name)) {
            return null;
        }

        if (!empty($this->items[$name])) {
            return $this->items[$name];
        }

        /** @var AuthItemRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemRepository);
        $entity = $repository->findByName($name);

        if (empty($entity)) {
            return null;
        }

        return $this->populateItem($entity);
    }

    /**
     * Populates an auth item with the data fetched from database.
     * @param AuthItem $row
     * @return Item the populated auth item instance (either Role or Permission)
     */
    protected function populateItem($row)
    {
        $class = $row->type == Item::TYPE_PERMISSION ? Permission::class : Role::class;

        if (!isset($row->data) || ($data = @unserialize(is_resource($row->data) ? stream_get_contents($row->data) : $row->data)) === false) {
            $data = null;
        }

        return new $class([
            'name' => $row->name,
            'type' => $row->type,
            'description' => $row->description,
            'ruleName' => $row->ruleName,
            'data' => $data,
            'createdAt' => $row->createdAt,
            'updatedAt' => $row->updatedAt,
        ]);
    }

    /**
     * Returns the items of the specified type.
     * @param int $type the auth item type (either [[Item::TYPE_ROLE]] or [[Item::TYPE_PERMISSION]]
     * @return Item[] the auth items of the specified type.
     * @throws \yii\base\InvalidConfigException
     */
    protected function getItems($type)
    {
        /** @var AuthItemRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemRepository);
        $entities = $repository->findAllByType($type);

        $items = [];
        foreach ($entities as $entity) {
            $items[$entity->name] = $this->populateItem($entity);
        }

        return $items;
    }

    /**
     * Adds an auth item to the RBAC system.
     * @param Item $item the item to add
     * @return bool whether the auth item is successfully added to the system
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     * @throws \Throwable
     */
    protected function addItem($item)
    {
        /** @var AuthItemRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemRepository);
        $repository->insert(new AuthItem([
            'name' => $item->name,
            'type' => $item->type,
            'description' => $item->description,
            'ruleName' => $item->ruleName,
            'data' => $item->data === null ? null : serialize($item->data),
        ]));

        $this->invalidateCache();

        return true;
    }

    /**
     * Adds a rule to the RBAC system.
     * @param Rule $rule the rule to add
     * @return bool whether the rule is successfully added to the system
     * @throws \Exception if data validation or saving fails (such as the name of the rule is not unique)
     * @throws \Throwable
     */
    protected function addRule($rule)
    {
        /** @var AuthItemRepositoryInterface $repository */
        $repository = Yii::createObject($this->ruleRepository);
        $repository->insert(new AuthRule([
            'name' => $rule->name,
            'data' => serialize($rule),
        ]));

        $this->invalidateCache();

        return true;
    }

    /**
     * Removes an auth item from the RBAC system.
     * @param Item $item the item to remove
     * @return bool whether the role or permission is successfully removed
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     * @throws \Throwable
     */
    protected function removeItem($item)
    {
        if (!$this->supportsCascadeUpdate()) {
            /** @var AuthItemChildRepositoryInterface $itemChildRepository */
            $itemChildRepository = Yii::createObject($this->itemChildRepository);
            /** @var AuthAssignmentRepositoryInterface $assignmentRepository */
            $assignmentRepository = Yii::createObject($this->assignmentRepository);
            $relatives = $itemChildRepository->findAllRelativesByName($item->name);
            foreach ($relatives as $relative) {
                $itemChildRepository->delete($relative);
            }

            $assignments = $assignmentRepository->findAllByName($item->name);
            foreach ($assignments as $assignment) {
                $assignmentRepository->delete($assignment);
            }
        }

        /** @var AuthItemRepositoryInterface $itemRepository */
        $itemRepository = Yii::createObject($this->itemRepository);
        $items = $itemRepository->findAllByName($item->name);

        foreach ($items as $item) {
            $itemRepository->delete($item);
        }

        $this->invalidateCache();

        return true;
    }

    /**
     * Removes a rule from the RBAC system.
     * @param Rule $rule the rule to remove
     * @return bool whether the rule is successfully removed
     * @throws \Exception if data validation or saving fails (such as the name of the rule is not unique)
     * @throws \Throwable
     */
    protected function removeRule($rule)
    {
        if (!$this->supportsCascadeUpdate()) {
            /** @var AuthItemRepositoryInterface $itemRepository */
            $itemRepository = Yii::createObject($this->itemRepository);
            $items = $itemRepository->findAllByRuleName($rule->name);
            foreach ($items as $item) {
                $itemRepository->delete($item);
            }
        }

        /** @var AuthRuleRepositoryInterface $ruleRepository */
        $ruleRepository = Yii::createObject($this->ruleRepository);
        $rule = $ruleRepository->findByName($rule->name);
        $ruleRepository->delete($rule);

        $this->invalidateCache();

        return true;
    }

    /**
     * Updates an auth item in the RBAC system.
     * @param string $name the name of the item being updated
     * @param Item $item the updated item
     * @return bool whether the auth item is successfully updated
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     * @throws \Throwable
     */
    protected function updateItem($name, $item)
    {
        if ($item->name !== $name && !$this->supportsCascadeUpdate()) {
            /** @var AuthItemChildRepositoryInterface $itemChildRepository */
            $itemChildRepository = Yii::createObject($this->itemChildRepository);
            $itemChildren = $itemChildRepository->findAllByParentName($name);
            foreach ($itemChildren as $itemChild) {
                $itemChild->parent = $item->name;
                $itemChildRepository->update($itemChild);
            }
            $itemChildren = $itemChildRepository->findAllByChildName($name);
            foreach ($itemChildren as $itemChild) {
                $itemChild->child = $item->name;
                $itemChildRepository->update($itemChild);
            }
            /** @var AuthAssignmentRepositoryInterface $assignmentRepository */
            $assignmentRepository = Yii::createObject($this->assignmentRepository);
            $itemChildren = $assignmentRepository->findAllByItemName($name);
            foreach ($itemChildren as $itemChild) {
                $itemChild->itemName = $item->name;
                $assignmentRepository->update($itemChild);
            }
        }

        /** @var AuthItemRepositoryInterface $itemRepository */
        $itemRepository = Yii::createObject($this->itemRepository);

        $entity = $itemRepository->findByName($name);
        $entity->name = $item->name;
        $entity->description = $item->description;
        $entity->ruleName = $item->ruleName;
        $entity->data = $item->data === null ? null : serialize($item->data);

        $itemRepository->update($entity);

        $this->invalidateCache();

        return true;
    }

    /**
     * Updates a rule to the RBAC system.
     * @param string $name the name of the rule being updated
     * @param Rule $rule the updated rule
     * @return bool whether the rule is successfully updated
     * @throws \Exception if data validation or saving fails (such as the name of the rule is not unique)
     * @throws \Throwable
     */
    protected function updateRule($name, $rule)
    {
        if ($rule->name !== $name && !$this->supportsCascadeUpdate()) {
            $itemRepository = Yii::createObject($this->itemRepository);
            $items = $itemRepository->findAllByRuleName($name);
            foreach ($items as $item) {
                $item->ruleName = $rule->name;
                $itemRepository->update($item);
            }
        }

        /** @var AuthRuleRepositoryInterface $ruleRepository */
        $ruleRepository = Yii::createObject($this->ruleRepository);
        $entity = $ruleRepository->findByName($name);
        $entity->name = $rule->name;
        $entity->data = serialize($rule);

        $ruleRepository->update($entity);

        $this->invalidateCache();

        return true;
    }

    /**
     * Returns the roles that are assigned to the user via [[assign()]].
     * Note that child roles that are not assigned directly to the user will not be returned.
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return Role[] all roles directly assigned to the user. The array is indexed by the role names.
     * @throws \yii\base\InvalidConfigException
     */
    public function getRolesByUser($userId)
    {
        if ($this->isEmptyUserId($userId)) {
            return [];
        }

        /** @var AuthItemRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemRepository);
        $userRoles = $repository->findRolesByUser($userId);

        $roles = $this->getDefaultRoleInstances();
        foreach ($userRoles as $userRole) {
            $roles[$userRole->name] = $this->populateItem($userRole);
        }

        return $roles;
    }

    /**
     * Returns the rule of the specified name.
     * @param string $name the rule name
     * @return null|Rule the rule object, or null if the specified name does not correspond to a rule.
     * @throws \yii\base\InvalidConfigException
     */
    public function getRule($name)
    {
        if ($this->rules !== null) {
            return isset($this->rules[$name]) ? $this->rules[$name] : null;
        }

        /** @var AuthRuleRepositoryInterface $repository */
        $repository = Yii::createObject($this->ruleRepository);
        $entity = $repository->findByName($name);

        if (empty($entity)) {
            return null;
        }
        $data = $entity->data;
        if (is_resource($data)) {
            $data = stream_get_contents($data);
        }

        return unserialize($data);
    }

    /**
     * Returns all rules available in the system.
     * @return Rule[] the rules indexed by the rule names
     * @throws \yii\base\InvalidConfigException
     */
    public function getRules()
    {
        if ($this->rules !== null) {
            return $this->rules;
        }

        /** @var AuthRuleRepositoryInterface $repository */
        $repository = Yii::createObject($this->ruleRepository);
        $entities = $repository->findAll();

        $rules = [];
        foreach ($entities as $entity) {
            $data = $entity->data;
            if (is_resource($data)) {
                $data = stream_get_contents($data);
            }
            $rules[$entity->name] = unserialize($data);
        }

        return $rules;
    }

    /**
     * Adds an item as a child of another item.
     * @param Item $parent
     * @param Item $child
     * @return bool whether the child successfully added
     * @throws \yii\base\Exception if the parent-child relationship already exists or if a loop has been detected.
     */
    public function addChild($parent, $child)
    {
        if ($parent->name === $child->name) {
            throw new InvalidArgumentException("Cannot add '{$parent->name}' as a child of itself.");
        }

        if ($parent instanceof Permission && $child instanceof Role) {
            throw new InvalidArgumentException('Cannot add a role as a child of a permission.');
        }

        if ($this->detectLoop($parent, $child)) {
            throw new InvalidCallException("Cannot add '{$child->name}' as a child of '{$parent->name}'. A loop has been detected.");
        }

        /** @var AuthItemChildRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemChildRepository);
        /** @var AuthItemChild $entity */
        $entity = $repository->newEntity();
        $entity->parent = $parent->name;
        $entity->child = $child->name;

        $repository->insert($entity);

        $this->invalidateCache();

        return true;
    }

    /**
     * Removes a child from its parent.
     * Note, the child item is not deleted. Only the parent-child relationship is removed.
     * @param Item $parent
     * @param Item $child
     * @return bool whether the removal is successful
     * @throws \yii\base\InvalidConfigException
     */
    public function removeChild($parent, $child)
    {
        /** @var AuthItemChildRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemChildRepository);
        $entity = $repository->findByParentNameAndChildName($parent->name, $child->name);

        $result = $repository->delete($entity) > 0;

        $this->invalidateCache();

        return $result;
    }

    /**
     * Removed all children form their parent.
     * Note, the children items are not deleted. Only the parent-child relationships are removed.
     * @param Item $parent
     * @return bool whether the removal is successful
     * @throws \yii\base\InvalidConfigException
     */
    public function removeChildren($parent)
    {
        /** @var AuthItemChildRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemChildRepository);
        $entities = $repository->findAllByParentName($parent->name);

        $result = false;
        foreach ($entities as $entity) {
            $result = $repository->delete($entity) > 0;

            if ($result === false) {
                return false;
            }
        }

        $this->invalidateCache();

        return $result;
    }

    /**
     * Returns a value indicating whether the child already exists for the parent.
     * @param Item $parent
     * @param Item $child
     * @return bool whether `$child` is already a child of `$parent`
     * @throws \yii\base\InvalidConfigException
     */
    public function hasChild($parent, $child)
    {
        /** @var AuthItemChildRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemChildRepository);
        return $repository->hasChild($parent->name, $child->name) !== false;
    }

    /**
     * Returns the child permissions and/or roles.
     * @param string $name the parent name
     * @return Item[] the child permissions and/or roles
     * @throws \yii\base\InvalidConfigException
     */
    public function getChildren($name)
    {
        /** @var AuthItemRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemRepository);
        $itemChildren = $repository->findChildrenByParent($name);

        $children = [];
        foreach ($itemChildren as $itemChild) {
            $children[$itemChild['name']] = $this->populateItem($itemChild);
        }

        return $children;
    }

    /**
     * Assigns a role to a user.
     *
     * @param Role|Permission $role
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return Assignment the role assignment information.
     * @throws \Exception if the role has already been assigned to the user
     */
    public function assign($role, $userId)
    {
        $assignment = new Assignment([
            'userId' => $userId,
            'roleName' => $role->name,
            'createdAt' => time(),
        ]);

        /** @var AuthAssignmentRepositoryInterface $repository */
        $repository = Yii::createObject($this->assignmentRepository);
        /** @var AuthAssignment $entity */
        $entity = $repository->newEntity();
        $entity->userId = $assignment->userId;
        $entity->itemName = $assignment->roleName;
        $entity->createdAt = $assignment->createdAt;
        $repository->insert($entity);

        unset($this->_checkAccessAssignments[(string)$userId]);
        return $assignment;
    }

    /**
     * Revokes a role from a user.
     * @param Role|Permission $role
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return bool whether the revoking is successful
     * @throws \yii\base\InvalidConfigException
     */
    public function revoke($role, $userId)
    {
        if ($this->isEmptyUserId($userId)) {
            return false;
        }

        unset($this->_checkAccessAssignments[(string)$userId]);

        /** @var AuthAssignmentRepositoryInterface $repository */
        $repository = Yii::createObject($this->assignmentRepository);

        $role = $repository->findItemByUser($role->name, $userId);

        return $repository->delete($role) > 0;
    }

    /**
     * Revokes all roles from a user.
     * @param mixed $userId the user ID (see [[\yii\web\User::id]])
     * @return bool whether the revoking is successful
     * @throws \yii\base\InvalidConfigException
     */
    public function revokeAll($userId)
    {
        if ($this->isEmptyUserId($userId)) {
            return false;
        }

        unset($this->_checkAccessAssignments[(string)$userId]);
        /** @var AuthAssignmentRepositoryInterface $repository */
        $repository = Yii::createObject($this->assignmentRepository);

        $roles = $repository->findAllByUserId($userId);
        $result = false;
        foreach ($roles as $role) {
            $result = $repository->delete($role) > 0;
            if ($result === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the assignment information regarding a role and a user.
     * @param string $roleName the role name
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return null|Assignment the assignment information. Null is returned if
     * the role is not assigned to the user.
     * @throws \yii\base\InvalidConfigException
     */
    public function getAssignment($roleName, $userId)
    {
        if ($this->isEmptyUserId($userId)) {
            return null;
        }

        /** @var AuthAssignmentRepositoryInterface $repository */
        $repository = Yii::createObject($this->assignmentRepository);
        $assignment = $repository->findItemByUser($roleName, $userId);

        if (empty($assignment)) {
            return null;
        }

        return new Assignment([
            'userId' => $assignment->userId,
            'roleName' => $assignment->itemName,
            'createdAt' => $assignment->createdAt,
        ]);
    }

    /**
     * Returns all role assignment information for the specified user.
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return Assignment[] the assignments indexed by role names. An empty array will be
     * returned if there is no role assigned to the user.
     * @throws \yii\base\InvalidConfigException
     */
    public function getAssignments($userId)
    {
        if ($this->isEmptyUserId($userId)) {
            return [];
        }

        /** @var AuthAssignmentRepositoryInterface $repository */
        $repository = Yii::createObject($this->assignmentRepository);
        $userAssignments = $repository->findAllByUserId($userId);

        $assignments = [];
        foreach ($userAssignments as $userAssignment) {
            $assignments[$userAssignment->itemName] = new Assignment([
                'userId' => $userAssignment->userId,
                'roleName' => $userAssignment->itemName,
                'createdAt' => $userAssignment->createdAt,
            ]);
        }

        return $assignments;
    }

    /**
     * Returns all user IDs assigned to the role specified.
     * @param string $roleName
     * @return array array of user ID strings
     * @throws \yii\base\InvalidConfigException
     */
    public function getUserIdsByRole($roleName)
    {
        if (empty($roleName)) {
            return [];
        }

        /** @var AuthAssignmentRepositoryInterface $repository */
        $repository = Yii::createObject($this->assignmentRepository);
        return ArrayHelper::getValue($repository->findAllByItemName($roleName), 'userId');
    }

    /**
     * Removes all authorization data, including roles, permissions, rules, and assignments.
     * @throws \yii\base\InvalidConfigException
     */
    public function removeAll()
    {
        $this->removeAllAssignments();
        /** @var AuthItemChildRepositoryInterface $itemChildRepository */
        $itemChildRepository = Yii::createObject($this->itemChildRepository);
        $itemChildren = $itemChildRepository->findAll();
        foreach ($itemChildren as $itemChild) {
            $itemChildRepository->delete($itemChild);
        }

        /** @var AuthItemRepositoryInterface $itemRepository */
        $itemRepository = Yii::createObject($this->itemRepository);
        $items = $itemRepository->findAll();
        foreach ($items as $item) {
            $itemRepository->delete($item);
        }

        /** @var AuthRuleRepositoryInterface $ruleRepository */
        $ruleRepository = Yii::createObject($this->ruleRepository);
        $rules = $ruleRepository->findAll();
        foreach ($rules as $rule) {
            $ruleRepository->delete($rule);
        }

        $this->invalidateCache();
    }

    /**
     * Removes all auth items of the specified type.
     * @param int $type the auth item type (either Item::TYPE_PERMISSION or Item::TYPE_ROLE)
     * @throws \yii\base\InvalidConfigException
     */
    protected function removeAllItems($type)
    {
        /** @var AuthItemRepositoryInterface $itemRepository */
        $itemRepository = Yii::createObject($this->itemRepository);

        if (!$this->supportsCascadeUpdate()) {
            $names = ArrayHelper::getColumn($itemRepository->findAllByType($type), 'name');

            if (empty($names)) {
                return;
            }
            $key = $type == Item::TYPE_PERMISSION ? 'child' : 'parent';

            /** @var AuthItemChildRepositoryInterface $itemChildRepository */
            $itemChildRepository = Yii::createObject($this->itemChildRepository);
            if ($key === 'child') {
                $itemChildren = $itemChildRepository->findAllByChildName($names);
            } else {
                $itemChildren = $itemChildRepository->findAllByParentName($names);
            }

            foreach ($itemChildren as $itemChild) {
                $itemChildRepository->delete($itemChild);
            }

            /** @var AuthAssignmentRepositoryInterface $assignmentRepository */
            $assignmentRepository = Yii::createObject($this->assignmentRepository);
            $assignments = $assignmentRepository->findAllByName($names);

            foreach ($assignments as $assignment) {
                $assignmentRepository->delete($assignment);
            }
        }

        $items = $itemRepository->findAllByType($type);
        foreach ($items as $item) {
            $itemRepository->delete($item);
        }

        $this->invalidateCache();
    }

    /**
     * Removes all rules.
     * All roles and permissions which have rules will be adjusted accordingly.
     * @throws \yii\base\InvalidConfigException
     */
    public function removeAllRules()
    {
        if (!$this->supportsCascadeUpdate()) {
            /** @var AuthItemRepositoryInterface $itemRepository */
            $itemRepository = Yii::createObject($this->itemRepository);
            $items = $itemRepository->findAll();
            foreach ($items as $item) {
                $item->ruleName = null;
                $itemRepository->update($item);
            }
        }

        /** @var AuthRuleRepositoryInterface $ruleRepository */
        $ruleRepository = Yii::createObject($this->ruleRepository);
        $rules = $ruleRepository->findAll();

        foreach ($rules as $rule) {
            $ruleRepository->delete($rule);
        }

        $this->invalidateCache();
    }

    /**
     * Removes all role assignments.
     * @throws \yii\base\InvalidConfigException
     */
    public function removeAllAssignments()
    {
        $this->_checkAccessAssignments = [];
        /** @var AuthAssignmentRepositoryInterface $repository */
        $repository = Yii::createObject($this->assignmentRepository);
        $assignments = $repository->findAll();

        foreach ($assignments as $assignment) {
            $repository->delete($assignment);
        }
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function loadFromCache()
    {
        if ($this->items !== null || !$this->cache instanceof CacheInterface) {
            return;
        }

        $data = $this->cache->get($this->cacheKey);
        if (is_array($data) && isset($data[0], $data[1], $data[2])) {
            list($this->items, $this->rules, $this->parents) = $data;
            return;
        }

        /** @var AuthItemRepositoryInterface $itemRepository */
        $itemRepository = Yii::createObject(AuthItemRepositoryInterface::class);
        $this->items = [];
        foreach ($itemRepository->findAll() as $row) {
            $this->items[$row->name] = $this->populateItem($row);
        }

        /** @var AuthItemRepositoryInterface $ruleRepository */
        $ruleRepository = Yii::createObject(AuthRuleRepositoryInterface::class);
        $this->rules = [];
        foreach ($ruleRepository->findAll() as $row) {
            $data = $row->data;
            if (is_resource($data)) {
                $data = stream_get_contents($data);
            }
            $this->rules[$row->name] = unserialize($data);
        }

        /** @var AuthItemChildRepositoryInterface $itemChildRepository */
        $itemChildRepository = Yii::createObject(AuthItemChildRepositoryInterface::class);
        $this->parents = [];
        foreach ($itemChildRepository->findAll() as $row) {
            if (isset($this->items[$row->child])) {
                $this->parents[$row->child][] = $row->parent;
            }
        }

        $this->cache->set($this->cacheKey, [$this->items, $this->rules, $this->parents]);
    }

    /**
     * @param $userId
     * @return bool
     */
    protected function isEmptyUserId($userId)
    {
        return !isset($userId) || $userId === '';
    }

    /**
     * Returns the children for every parent.
     * @return array the children list. Each array key is a parent item name,
     * and the corresponding array value is a list of child item names.
     * @throws \yii\base\InvalidConfigException
     */
    protected function getChildrenList()
    {
        /** @var AuthItemChildRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemChildRepository);
        $children = $repository->findAll();
        $parents = [];
        foreach ($children as $child) {
            $parents[$child['parent']][] = $child['child'];
        }

        return $parents;
    }

    /**
     * Returns all permissions that are directly assigned to user.
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return Permission[] all direct permissions that the user has. The array is indexed by the permission names.
     * @throws \yii\base\InvalidConfigException
     */
    protected function getDirectPermissionsByUser($userId)
    {
        /** @var AuthItemRepositoryInterface $repository */
        $repository = Yii::createObject($this->itemRepository);
        $userPermissions = $repository->findPermissionsByUser($userId);

        $permissions = [];
        foreach ($userPermissions as $userPermission) {
            $permissions[$userPermission['name']] = $this->populateItem($userPermission);
        }

        return $permissions;
    }

    /**
     * Returns all permissions that the user inherits from the roles assigned to him.
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return Permission[] all inherited permissions that the user has. The array is indexed by the permission names.
     * @throws \yii\base\InvalidConfigException
     */
    protected function getInheritedPermissionsByUser($userId)
    {
        /** @var AuthAssignmentRepositoryInterface $assignmentRepository */
        $assignmentRepository = Yii::createObject($this->assignmentRepository);
        $userAssignments = $assignmentRepository->findAllByUserId($userId);

        $childrenList = $this->getChildrenList();
        $result = [];
        foreach ($userAssignments as $roleName) {
            $this->getChildrenRecursive($roleName, $childrenList, $result);
        }

        return $this->populateResultPermissions($result);
    }

    /**
     * @param array $result
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    protected function populateResultPermissions(array $result): array
    {
        if (empty($result)) {
            return [];
        }

        /** @var AuthItemRepositoryInterface $itemRepository */
        $itemRepository = Yii::createObject($this->itemRepository);
        $userPermissions = $itemRepository->findAllPermissionsByNames(array_keys($result));

        $permissions = [];
        foreach ($userPermissions as $userPermission) {
            $permissions[$userPermission['name']] = $this->populateItem($userPermission);
        }

        return $permissions;
    }
}
