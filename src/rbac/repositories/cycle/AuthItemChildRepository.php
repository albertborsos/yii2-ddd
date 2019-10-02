<?php

namespace albertborsos\ddd\rbac\repositories\cycle;

use albertborsos\ddd\rbac\entities\AuthItem;
use albertborsos\ddd\rbac\entities\AuthItemChild;
use albertborsos\ddd\rbac\interfaces\AuthItemChildRepositoryInterface;
use albertborsos\cycle\SchemaInterface;
use albertborsos\ddd\repositories\AbstractCycleRepository;
use mito\cms\core\data\CycleDataProvider;
use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;

class AuthItemChildRepository extends AbstractCycleRepository implements AuthItemChildRepositoryInterface, SchemaInterface
{
    protected $entityClass = \albertborsos\ddd\rbac\entities\AuthItemChild::class;

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
            'parent',
            'child',
        ];
    }

    public static function tableName(): string
    {
        return 'auth_item_child';
    }

    public static function schema(): array
    {
        return \albertborsos\cycle\Factory::schema(
            \albertborsos\ddd\rbac\entities\AuthItemChild::class,
            static::tableName(),
            ['parent', 'child'],
            static::columns(),
            ['id' => 'int'],
            [
                'parent' => \albertborsos\cycle\Factory::relation(\Cycle\ORM\Relation::HAS_ONE, AuthItem::class, 'parent', 'name'),
                'child' => \albertborsos\cycle\Factory::relation(\Cycle\ORM\Relation::HAS_ONE, AuthItem::class, 'child', 'name'),
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
     * @return array|AuthItemChild[]
     */
    public function findAllRelativesByName($name)
    {
        return $this->find()->where('parent', $name)->orWhere('child', $name)->fetchAll();
    }

    /**
     * @param string $name
     * @return array|AuthItemChild[]
     */
    public function findAllByParentName($name)
    {
        return $this->find()->where('parent', $name)->fetchAll();
    }

    /**
     * @param string $name
     * @return array|AuthItemChild[]
     */
    public function findAllByChildName($name)
    {
        return $this->find()->where('child', $name)->fetchAll();
    }

    /**
     * @return array|AuthItemChild[]
     */
    public function findAll()
    {
        return $this->find()->fetchAll();
    }

    /**
     * @param string $parentName
     * @param string $childName
     * @return AuthItemChild
     */
    public function findByParentNameAndChildName($parentName, $childName)
    {
        return $this->find()->andWhere('parent', $parentName)->andWhere('child', $childName)->fetchOne();
    }

    /**
     * @param $parentName
     * @param $childName
     * @return bool
     */
    public function hasChild($parentName, $childName)
    {
        return $this->find()->andWhere('parent', $parentName)->andWhere('child', $childName)->count() > 0;
    }
}
