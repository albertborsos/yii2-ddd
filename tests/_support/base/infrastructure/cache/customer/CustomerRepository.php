<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cache\customer;

use albertborsos\ddd\repositories\CacheRepository;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerCacheUpdaterInterface;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;

class CustomerRepository extends CacheRepository implements CustomerRepositoryInterface, CustomerCacheUpdaterInterface
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
}
