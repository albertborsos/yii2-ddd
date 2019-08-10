<?php

namespace albertborsos\ddd\tests\unit\traits;

use albertborsos\ddd\tests\support\base\domains\customer\mysql\CustomerActiveRepository;
use albertborsos\ddd\tests\support\base\domains\customer\mysql\CustomerAddressActiveRepository;
use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerForm;
use albertborsos\ddd\tests\support\base\services\customer\forms\InvalidCreateCustomerForm;
use Codeception\PHPUnit\TestCase;

class ActiveFormTraitTest extends TestCase
{
    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessageRegExp /\$repository must implements `albertborsos\\ddd\\interfaces\\ActiveRepositoryInterface`$/
     */
    public function testInvalidRepositoryClass()
    {
        $form = new InvalidCreateCustomerForm();
    }

    public function testGetRepository()
    {
        $form = new CreateCustomerForm();
        $this->assertInstanceOf(CustomerActiveRepository::class, $form->getRepository());

        $this->assertInstanceOf(CustomerAddressActiveRepository::class, $form->getRepository(CustomerAddressActiveRepository::class));
    }
}
