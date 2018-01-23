<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\model\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

class <?= \yii\helpers\StringHelper::basename($generator->getResourceUnitTestClass()) ?> extends AbstractBaseResourceTest
{
    protected $modelClass = '<?= $generator->getBusinessClass() ?>';
    protected $resourceClass = '<?= $generator->getResourceClass() ?>';

    public function fixtures()
    {
        return [
            '<?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>' => \tests\codeception\unit\fixtures\<?= $modelClassBaseName?>Fixture::className(),
        ];
    }

    public function deleteProvider()
    {
        return [
            'delete active <?= lcfirst($modelClassBaseName) ?>' => ['<?= lcfirst($modelClassBaseName) ?>1'],
            'delete inactive <?= lcfirst($modelClassBaseName) ?>' => ['<?= lcfirst($modelClassBaseName) ?>2'],
            'delete soft deleted <?= lcfirst($modelClassBaseName) ?>' => ['<?= lcfirst($modelClassBaseName) ?>3'],
        ];
    }

    /**
     * @skip
     * @dataProvider deleteProvider
     */
    public function testDelete($fixtureAlias)
    {
        $model = $this->getModel($this-><?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>[$fixtureAlias]['id']);

        $resource = $this->mockResource(null, $model);
        $resource->delete();

        $model = $this->getModel($this-><?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>[$fixtureAlias]['id']);
        $this->assertNull($model);
    }
}
