<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cycle\customer;

use albertborsos\cycle\Factory;
use albertborsos\ddd\repositories\AbstractCycleRepository;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerAddress;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressRepositoryInterface;
use Cycle\ORM\Relation;
use yii\data\ArrayDataProvider;
use yii\data\BaseDataProvider;
use yii\helpers\ArrayHelper;

class CustomerAddressRepository extends AbstractCycleRepository implements CustomerAddressRepositoryInterface
{
    protected $entityClass = CustomerAddress::class;

    public static function columns(): array
    {
        return ['id', 'customerId' => 'customer_id', 'zipCode' => 'zip_code', 'city', 'street'];
    }

    public static function schema(): array
    {
        return Factory::schema(CustomerAddress::class, 'customer_address', 'id', static::columns(), ['id' => 'int', 'customerId' => 'int', 'zipCode' => 'int'], [
            'customer' => Factory::relation(Relation::HAS_MANY, 'customer', 'customer_id', 'id'),
        ]);
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
