<?php

namespace albertborsos\ddd\rbac\repositories\cycle;

use albertborsos\ddd\rbac\entities\AuthAssignment;
use albertborsos\ddd\rbac\entities\AuthItem;
use albertborsos\ddd\rbac\interfaces\AuthAssignmentRepositoryInterface;
use albertborsos\cycle\SchemaInterface;
use albertborsos\ddd\repositories\AbstractCycleRepository;
use mito\cms\core\data\CycleDataProvider;
use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;

class AuthAssignmentRepository extends AbstractCycleRepository implements AuthAssignmentRepositoryInterface, SchemaInterface
{
    protected $entityClass = \albertborsos\ddd\rbac\entities\AuthAssignment::class;

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
            'itemName' => 'item_name',
            'userId' => 'user_id',
            'createdAt' => 'created_at',
        ];
    }

    public static function tableName(): string
    {
        return 'auth_assignment';
    }

    public static function schema(): array
    {
        return \albertborsos\cycle\Factory::schema(
            \albertborsos\ddd\rbac\entities\AuthAssignment::class,
            static::tableName(),
            ['itemName', 'userId'],
            static::columns(),
            ['id' => 'int'],
            [
                'itemName' => \albertborsos\cycle\Factory::relation(\Cycle\ORM\Relation::HAS_ONE, AuthItem::class, 'item_name', 'name'),
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
     * @return array|AuthAssignment[]
     */
    public function findAllByName($name)
    {
        return $this->find()->andWhere('item_name', $name)->fetchAll();
    }


    /**
     * @param string $name
     * @return array|AuthAssignment[]
     */
    public function findAllByItemName($name)
    {
        return $this->find()->where('item_name', $name)->fetchAll();
    }

    /**
     * @param $userId
     * @return array|AuthAssignment[]
     */
    public function findAllByUserId($userId)
    {
        return $this->find()->where('user_id', $userId)->fetchAll();
    }

    /**
     * @param $itemName
     * @param $userId
     * @return AuthAssignment|null
     */
    public function findItemByUser($itemName, $userId)
    {
        return $this->find()->where('user_id', $userId)->andWhere('item_name', $itemName)->fetchOne();
    }

    /**
     * @return array|AuthAssignment[]
     */
    public function findAll()
    {
        return $this->find()->fetchAll();
    }
}
