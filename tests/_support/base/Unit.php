<?php

namespace albertborsos\ddd\tests\support\base;

use yii\test\FixtureTrait;

class Unit extends \Codeception\Test\Unit
{
    use FixtureTrait;

    /**
     * @var \UnitTester Tester
     */
    protected $tester;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initFixtures();
    }
}
