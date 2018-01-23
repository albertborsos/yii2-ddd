<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator app\gii\generators\crud\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);
$pluralizedModelName = lcfirst(\yii\helpers\Inflector::pluralize($modelClassBaseName));

echo "<?php\n";
?>

use tests\codeception\unit\fixtures\<?= $modelClassBaseName ?>Fixture;

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getToggleStatusDomainClass())) ?> extends AbstractDomainTest
{
    protected $formClass = '<?= $generator->getToggleStatusFormClass()?>';
    protected $domainClass = '<?= $generator->getToggleStatusDomainClass()?>';
    protected $modelClass = '<?= $generator->modelClass?>';

    protected function tearDown()
    {
        $models = <?= $generator->modelClass?>::find()->all();
        foreach ($models as $model) {
            $resource = new <?= $generator->getResourceClass()?>(null, $model);
            $resource->delete();
        }
        parent::tearDown();
    }

    public function fixtures()
    {
        return [
            '<?= $pluralizedModelName ?>' => <?= $modelClassBaseName ?>Fixture::className(),
        ];
    }

    public function <?= lcfirst($modelClassBaseName) ?>Provider()
    {
        return [
            'update active <?= lcfirst($modelClassBaseName) ?> to inactive' => ['<?= lcfirst($modelClassBaseName) ?>1', 0],
            'update inactive <?= lcfirst($modelClassBaseName) ?> to active' => ['<?= lcfirst($modelClassBaseName) ?>2', 1],
            'update deleted <?= lcfirst($modelClassBaseName) ?>, stays deleted' => ['<?= lcfirst($modelClassBaseName) ?>3', 2],
        ];
    }

    /**
     * @skip
     * @dataProvider <?= lcfirst($modelClassBaseName) ?>Provider
     */
    public function testToToggle<?= $modelClassBaseName ?>Status($fixtureAlias, $expectedStatus)
    {
        $model = $this->getModel($this-><?= $pluralizedModelName ?>[$fixtureAlias]['id']);
        $form = $this->mockForm([], $model);

        $domain = $this->mockDomain($form, $model);
        $this->assertTrue($domain->process());
        $this->assertEquals($model->id, $domain->getId());

        $model = $this->getModel($domain->getId());

        $this->assertNotNull($model);
        $this->assertEquals($expectedStatus, $model->status);
    }
}
