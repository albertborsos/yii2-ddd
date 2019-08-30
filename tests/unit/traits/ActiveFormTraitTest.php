<?php

namespace albertborsos\ddd\tests\unit\traits;

use albertborsos\ddd\tests\support\base\infrastructure\mysql\customer\CustomerActiveRepository;
use albertborsos\ddd\tests\support\base\infrastructure\mysql\customer\CustomerAddressActiveRepository;
use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerForm;
use albertborsos\ddd\tests\support\base\services\customer\forms\InvalidCreateCustomerForm;
use Codeception\PHPUnit\TestCase;

class ActiveFormTraitTest extends TestCase
{
    /**
     * @expectedException TypeError
     */
    public function testInvalidRepositoryClass()
    {
        $form = new InvalidCreateCustomerForm();
        $form->getRepository();
    }

    public function testGetRepository()
    {
        $form = new CreateCustomerForm();
        $this->assertInstanceOf(CustomerActiveRepository::class, $form->getRepository());

        $this->assertInstanceOf(CustomerAddressActiveRepository::class, $form->getRepository(CustomerAddressActiveRepository::class));
    }
}
