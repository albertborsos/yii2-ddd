<?php

namespace albertborsos\ddd\tests\support\base;

class MockConfig
{
    public static function create(string $className, array $attributes = [], array $settings = []): array
    {
        return [
            'class' => $className,
            'attributes' => $attributes,
            'settings' => $settings,
        ];
    }
}
