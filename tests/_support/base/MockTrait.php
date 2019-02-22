<?php

namespace albertborsos\ddd\tests\support\base;

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
 */
trait MockTrait
{
    private function mockObject(array $config): object
    {
        $model = \Mockery::mock($config['class'])->makePartial()->shouldAllowMockingProtectedMethods();

        foreach ($config['attributes'] as $attribute => $value) {
            if (!is_array($value)) {
                $model->$attribute = $value;
                continue;
            }
            $model->shouldReceive('get' . ucfirst($attribute))->andReturn($this->mockObject($value))->atLeast()->once();
        }

        foreach ($config['settings'] as $method => $returnValue) {
            $model->shouldReceive($method)->andReturn($returnValue)->atLeast()->once();
        }

        return $model;
    }
}
