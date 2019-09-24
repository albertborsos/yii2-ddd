<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cache\customer;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\repositories\AbstractCacheRepository;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerCacheUpdaterInterface;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use yii\data\BaseDataProvider;

class CustomerRepository extends AbstractCacheRepository implements CustomerRepositoryInterface, CustomerCacheUpdaterInterface
{
    const POSTFIX_VIP_CUSTOMERS = 'vip-customers';

    protected $entityClass = Customer::class;

    public function getVipCustomers()
    {
        return $this->cache->get($this->postfixedKey(self::POSTFIX_VIP_CUSTOMERS));
    }

    public function updateVipCustomers($customers)
    {
        return $this->cache->set($this->postfixedKey(self::POSTFIX_VIP_CUSTOMERS), $customers);
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
        // TODO: Implement search() method.
    }

    public static function columns(): array
    {
        return ['id', 'name'];
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
