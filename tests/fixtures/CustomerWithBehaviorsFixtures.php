<?php

namespace albertborsos\ddd\tests\fixtures;

use albertborsos\ddd\tests\support\base\infrastructure\db\customer\Customer;
use yii\test\ActiveFixture;

class CustomerWithBehaviorsFixtures extends ActiveFixture
{
    public $modelClass = Customer::class;

    public $dataFile = '@tests/fixtures/data/customer-with-behaviors.php';
}
