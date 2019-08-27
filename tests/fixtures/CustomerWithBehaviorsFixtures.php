<?php

namespace albertborsos\ddd\tests\fixtures;

use albertborsos\ddd\tests\support\base\domains\customer\mysql\Customer;
use yii\test\ActiveFixture;

class CustomerWithBehaviorsFixtures extends ActiveFixture
{
    public $modelClass = Customer::class;

    public $dataFile = '@tests/fixtures/data/customer-with-behaviors.php';
}
