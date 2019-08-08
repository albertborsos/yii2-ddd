<?php

// ensure we get report on all possible php errors
error_reporting(-1);

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/main.php');

$app = new \yii\console\Application($config);

Yii::setAlias('@tests', dirname(__DIR__));
