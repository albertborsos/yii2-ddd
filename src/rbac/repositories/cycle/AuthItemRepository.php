<?php

namespace albertborsos\ddd\rbac\repositories\cycle;

use albertborsos\ddd\rbac\entities\AuthAssignment;
use albertborsos\ddd\rbac\entities\AuthItem;
use albertborsos\ddd\rbac\entities\AuthItemChild;
use albertborsos\ddd\rbac\entities\AuthRule;
use albertborsos\ddd\rbac\interfaces\AuthItemRepositoryInterface;
use albertborsos\cycle\SchemaInterface;
use albertborsos\ddd\repositories\AbstractCycleRepository;
use mito\cms\core\data\CycleDataProvider;
use Spiral\Database\Injection\Expression;
use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;
use yii\rbac\Item;

class AuthItemRepository extends AbstractCycleRepository implements AuthItemRepositoryInterface, SchemaInterface
{
    protected $entityClass = \albertborsos\ddd\rbac\entities\AuthItem::class;

    /**
     * Mapping between the the properties of the entity and the attributes of the data model.
     * Required to hydrate, extract and serialize the entity.
     * Keys are the entity properties, values are the data attributes.
     *
     * @return array
     */
    public static function columns(): array
    {
        return [
            'name',
            'type',
            'description',
            'ruleName' => 'rule_name',
            'data',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    public static function tableName(): string
    {
        return 'auth_item';
    }

    public static function schema(): array
    {
        return \albertborsos\cycle\Factory::schema(
            \albertborsos\ddd\rbac\entities\AuthItem::class,
            static::tableName(),
            'name',
            static::columns(),
            ['id' => 'int'],
            [
                'authAssignments' => \albertborsos\cycle\Factory::relation(\Cycle\ORM\Relation::HAS_MANY, AuthAssignment::class, 'name', 'item_name'),
                'ruleName' => \albertborsos\cycle\Factory::relation(\Cycle\ORM\Relation::HAS_ONE, AuthRule::class, 'rule_name', 'name'),
                'children' => \albertborsos\cycle\Factory::relation(\Cycle\ORM\Relation::HAS_MANY, AuthItemChild::class, 'name', 'parent'),
                'parents' => \albertborsos\cycle\Factory::relation(\Cycle\ORM\Relation::HAS_MANY, AuthItemChild::class, 'name', 'child'),
            ],
        );
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @param string $formName
     * @return CycleDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params, $formName = null): BaseDataProvider
    {
        throw new InvalidConfigException('Search is not implemented!');
    }

    /**
     * @param $name
     * @return AuthItem|null
     */
    public function findByName($name)
    {
        return $this->find()->andWhere('name', $name)->fetchOne();
    }

    /**
     * @param int $type
     * @return array|AuthItem[]
     */
    public function findAllByType($type)
    {
        return $this->find()->andWhere('type', $type)->fetchAll();
    }

    /**
     * @param string $name
     * @return array|AuthItem[]
     */
    public function findAllByName($name)
    {
        return $this->find()->andWhere('name', $name)->fetchAll();
    }

    /**
     * @param $ruleName
     * @return array|AuthItem[]
     */
    public function findAllByRuleName($ruleName)
    {
        return $this->find()->andWhere('rule_name', $ruleName)->fetchAll();
    }

    /**
     * @param $userId
     * @return AuthItem[]
     */
    public function findRolesByUser($userId)
    {
        return $this->findItemTypesByUser($userId, Item::TYPE_ROLE);
    }

    /**
     * @param $userId
     * @return AuthItem[]
     */
    public function findPermissionsByUser($userId)
    {
        return $this->findItemTypesByUser($userId, Item::TYPE_PERMISSION);
    }

    /**
     * @param array $names
     * @return array|AuthItem[]
     */
    public function findAllPermissionsByNames(array $names)
    {
        return $this->find()->andWhere('type', Item::TYPE_PERMISSION)->andWhere('name', $names)->fetchAll();
    }

    /**
     * @param $userId
     * @param $type
     * @return AuthItem[]
     */
    protected function findItemTypesByUser($userId, $type)
    {
        return $this->find()
            ->with('authAssignments')
            ->where('authAssignments.item_name', new Expression('name'))
            ->andWhere('authAssignments.user_id', strval($userId))
            ->andWhere(static::tableName() . '.type', $type)
            ->fetchAll();
    }

    /**
     * @param $name
     * @return AuthItem[]
     */
    public function findChildrenByParent($name)
    {
        return $this->find()
            ->with('children')
            ->andWhere('parent', $name)
            ->andWhere('name', new Expression('[[child]]'))
            ->fetchAll();
    }

    /**
     * @param $name
     * @return AuthItem[]
     */
    public function findAll()
    {
        return $this->find()->fetchAll();
    }
}
