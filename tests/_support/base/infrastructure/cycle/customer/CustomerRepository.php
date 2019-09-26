<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cycle\customer;

use albertborsos\cycle\Factory;
use albertborsos\ddd\repositories\AbstractCycleRepository;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use Cycle\ORM\Relation;
use yii\data\ArrayDataProvider;
use yii\data\BaseDataProvider;
use yii\helpers\ArrayHelper;

class CustomerRepository extends AbstractCycleRepository implements CustomerRepositoryInterface
{
    protected $entityClass = Customer::class;

    public static function tableName(): string
    {
        return 'customer';
    }

    public static function columns(): array
    {
        return ['id', 'name'];
    }

    public static function schema(): array
    {
        return Factory::schema(Customer::class, static::tableName(), 'id', static::columns(), ['id' => 'int'], [
            'customerAddresses' => Factory::relation(Relation::HAS_MANY, 'customer_address', 'id', 'customer_id'),
        ]);
    }

    public function getVipCustomers()
    {
        return $this->find()->andWhere(['id' => [1, 2, 3]])->fetchAll();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param $params
     * @param null $formName
     * @return BaseDataProvider
     */
    public function search($params, $formName = null): BaseDataProvider
    {
        $select = $this->find();

        if ($params['expand'] ?? false) {
            $select->with(explode(',', $params['expand']));
        }

        // add conditions that should always apply here
        $models = $select->fetchAll();
        $keys = ArrayHelper::getColumn($models, 'id');

        return new ArrayDataProvider([
            'allModels' => array_combine($keys, $models),
        ]);
    }
}
