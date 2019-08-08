<?php

namespace albertborsos\ddd\tests\support\base;

use Mockery\MockInterface;

/**
 * Trait MockTrait
 * @package albertborsos\ddd\tests\support\base
 *
 * Usage sample:
 *
 * ```php
 *  $websiteConfig = MockConfig::create(\app\domains\website\business\Website::class, [], [
 *      'isAllowedToUpdateWebsiteService' => $websiteHasOnlyOneUser,
 *  ]);
 *
 *  return MockConfig::create(\app\domains\website\business\WebsiteService::class, [
 *      'profile_id' => $profileId,
 *      'created_by' => $createdBy,
 *      'website' => $websiteConfig,
 *  ]);
 * ```
 *
 * to return multiple mocked objects as a mocked query result
 *
 * ```php
 *  return \albertborsos\ddd\tests\support\base\MockConfig::create(\app\domains\User::class, [
 *      'id' => 1,
 *      'invoices' => [
 *           MockConfig::create(\app\domains\Invoice::class, ['user_id' => 1, 'id' => 1]),
 *           MockConfig::create(\app\domains\Invoice::class, ['user_id' => 1, 'id' => 2]),
 *      ],
 *  ]);
 * ```
 */
trait MockTrait
{
    private function mockObject(array $config): MockInterface
    {
        $model = \Mockery::mock($config['class'])->makePartial()->shouldAllowMockingProtectedMethods();

        foreach ($config['attributes'] ?? [] as $attribute => $value) {
            if (!is_array($value)) {
                $model->$attribute = $value;
                continue;
            }

            if (isset($value['class'])) {
                $model->shouldReceive('get' . ucfirst($attribute))->andReturn($this->mockObject($value))->atLeast()->once();
                continue;
            }

            $returnObjects = [];
            foreach ($value as $objectConfig) {
                $returnObjects[] = $this->mockObject($objectConfig);
            }
            $model->shouldReceive('get' . ucfirst($attribute))->andReturn($returnObjects)->atLeast()->once();
        }

        foreach ($config['settings'] ?? [] as $method => $returnValue) {
            $model->shouldReceive($method)->andReturn($returnValue)->atLeast()->once();
        }

        return $model;
    }
}
