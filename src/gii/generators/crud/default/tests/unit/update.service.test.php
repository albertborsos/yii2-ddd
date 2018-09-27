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

use albertborsos\ddd\tests\support\base\AbstractServiceTest;
use tests\codeception\unit\fixtures\<?= $modelClassBaseName ?>Fixture;

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getUpdateServiceClass())) ?> extends AbstractServiceTest
{
    protected $formClass = '<?= $generator->getUpdateFormClass()?>';
    protected $serviceClass = '<?= $generator->getUpdateServiceClass()?>';
    protected $modelClass = '<?= $generator->modelClass?>';

    protected function tearDown()
    {
        $models = <?= $generator->modelClass?>::find()->all();
        foreach ($models as $model) {
            $model->delete();
        }
        parent::tearDown();
    }

    public function fixtures()
    {
        return [
            '<?= $pluralizedModelName ?>' => <?= $modelClassBaseName ?>Fixture::className(),
        ];
    }

    /**
     * Test only valid events here.
     * Test invalid events in `<?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getUpdateFormClass())) ?>`,
     * because invalid events should not reach the execute method of service classes.
     */
    public function update<?= $modelClassBaseName ?>Provider()
    {
        return [
            'update active <?= $modelClassBaseName ?>' => ['<?= lcfirst($modelClassBaseName) ?>1', 'new name'],
            'update inactive <?= $modelClassBaseName ?>' => ['<?= lcfirst($modelClassBaseName) ?>2', 'new name'],
        ];
    }

    /**
     * @skip
     * @dataProvider update<?= $modelClassBaseName ?>Provider
     */
    public function testUpdate<?= $modelClassBaseName ?>($fixtureAlias, $name)
    {
        $expectedId = $this->tester->grabFixture('<?= $pluralizedModelName ?>', $fixtureAlias)['id'];
        $expectedStatus = $this->tester->grabFixture('<?= $pluralizedModelName ?>', $fixtureAlias)['status'];

        $model = $this->getModel($expectedId);

        $form = $this->mockForm([
            'name' => $name,
        ], $model);

        $service = $this->mockService($form, $model);
        $this->assertTrue($service->execute());
        $this->assertEquals($expectedId, $service->getId());

        $model = $this->getModel($expectedId);
        $this->assertEquals($name, $model->name);
        $this->assertEquals($expectedStatus, $model->status);
    }
}
