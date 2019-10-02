<?php

namespace albertborsos\ddd\rbac\repositories\cycle;

use albertborsos\ddd\rbac\entities\AuthItem;
use albertborsos\ddd\rbac\entities\AuthRule;
use albertborsos\ddd\rbac\interfaces\AuthRuleRepositoryInterface;
use albertborsos\cycle\SchemaInterface;
use albertborsos\ddd\repositories\AbstractCycleRepository;
use mito\cms\core\data\CycleDataProvider;
use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;

class AuthRuleRepository extends AbstractCycleRepository implements AuthRuleRepositoryInterface, SchemaInterface
{
    protected $entityClass = \albertborsos\ddd\rbac\entities\AuthRule::class;

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
            'data',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    public static function tableName(): string
    {
        return 'auth_rule';
    }

    public static function schema(): array
    {
        return \albertborsos\cycle\Factory::schema(
            \albertborsos\ddd\rbac\entities\AuthRule::class,
            static::tableName(),
            'name',
            static::columns(),
            ['id' => 'int'],
            [
                'authItems' => \albertborsos\cycle\Factory::relation(\Cycle\ORM\Relation::HAS_MANY, AuthItem::class, 'name', 'rule_name'),
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
     * @param string $name
     * @return object|AuthRule|null
     */
    public function findByName(string $name)
    {
        return $this->find()->andWhere('name', $name)->fetchOne();
    }

    /**
     * @return AuthRule[]
     */
    public function findAll()
    {
        return $this->find()->fetchAll();
    }
}
