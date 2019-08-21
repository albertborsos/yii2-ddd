<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\cache;

use albertborsos\ddd\repositories\CacheRepository;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerCacheRepositoryInterface;

class CustomerCacheRepository extends CacheRepository implements CustomerCacheRepositoryInterface
{
    const POSTFIX_VIP_CUSTOMERS = 'vip-customers';

    protected $entityClass = Customer::class;

    public function getVipCustomers()
    {
        return $this->get($this->postfixedKey(self::POSTFIX_VIP_CUSTOMERS));
    }

    public function updateVipCustomers($customers)
    {
        return $this->set($this->postfixedKey(self::POSTFIX_VIP_CUSTOMERS), $customers);
    }
}