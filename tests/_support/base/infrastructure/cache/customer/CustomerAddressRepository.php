<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cache\customer;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\repositories\AbstractCacheRepository;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerAddress;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressCacheUpdaterInterface;
use yii\data\BaseDataProvider;

class CustomerAddressRepository extends AbstractCacheRepository implements CustomerAddressCacheUpdaterInterface
{
    protected $entityClass = CustomerAddress::class;

    /**
     * Creates data provider instance with search query applied
     *
     * @param $params
     * @param null $formName
     * @return BaseDataProvider
     */
    public function search($params, $formName = null): BaseDataProvider
    {
        // TODO: Implement search() method.
    }

    public static function columns(): array
    {
        return ['id', 'customerId', 'zipCode', 'city', 'street'];
    }

    /**
     * Public interface for tests.
     *
     * @param EntityInterface $entity
     * @return mixed
     */
    public function getSerializedAttributes(EntityInterface $entity)
    {
        return $this->serialize($entity);
    }
}
