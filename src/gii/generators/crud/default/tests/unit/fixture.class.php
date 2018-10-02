<?php
/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>
namespace tests\codeception\unit\fixtures;

class <?= $modelClassBaseName ?>Fixture extends \yii\test\ActiveFixture
{
    public $modelClass = <?= $generator->modelClass ?>::class;

    public $dataFile = '@tests/codeception/unit/fixtures/data/<?= lcfirst($modelClassBaseName) ?>.php';
}
