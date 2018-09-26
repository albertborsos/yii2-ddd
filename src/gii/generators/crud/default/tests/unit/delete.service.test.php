<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);
$pluralizedModelName = lcfirst(\yii\helpers\Inflector::pluralize($modelClassBaseName));

echo "<?php\n";
?>

use tests\codeception\unit\fixtures\<?= $modelClassBaseName ?>Fixture;

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getDeleteServiceClass())) ?> extends AbstractServiceTest
{
    protected $formClass = '<?= $generator->getDeleteFormClass()?>';
    protected $serviceClass = '<?= $generator->getDeleteServiceClass()?>';
    protected $modelClass = '<?= $generator->modelClass?>';

    public function fixtures()
    {
        return [
            '<?= $pluralizedModelName ?>' => <?= $modelClassBaseName ?>Fixture::className(),
        ];
    }

    /**
     * Test only valid events here.
     * Test invalid events in `<?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getDeleteFormClass())) ?>`,
     * because invalid events should not reach the execute method of service classes.
     */
    public function delete<?= $modelClassBaseName ?>Provider()
    {
        return [
            'delete active <?= $modelClassBaseName ?>' => ['<?= lcfirst($modelClassBaseName) ?>1'],
            'delete inactive <?= $modelClassBaseName ?>' => ['<?= lcfirst($modelClassBaseName) ?>2'],
        ];
    }

    /**
     * @skip
     * @dataProvider delete<?= $modelClassBaseName ?>Provider
     */
    public function testDelete<?= $modelClassBaseName ?>($fixtureAlias)
    {
        $expectedId = $this-><?= $pluralizedModelName ?>[$fixtureAlias]['id'];
        $model = $this->getModel($expectedId);

        $form = $this->mockForm([], $model);

        $service = $this->mockService($form, $model);
        $this->assertTrue($service->execute());
        $this->assertEquals($expectedId, $service->getId());

        $model = $this->getModel($expectedId);
        $this->assertNull($model);
    }
}
