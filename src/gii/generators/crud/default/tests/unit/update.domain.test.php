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

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getUpdateDomainClass())) ?> extends AbstractDomainTest
{
    protected $formClass = '<?= $generator->getUpdateFormClass()?>';
    protected $domainClass = '<?= $generator->getUpdateDomainClass()?>';
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

    /**
     * Test only valid events here.
     * Test invalid events in `<?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getUpdateFormClass())) ?>`,
     * because invalid events should not reach the process method of domain classes.
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
        $expectedId = $this-><?= $pluralizedModelName ?>[$fixtureAlias]['id'];
        $expectedStatus = $this-><?= $pluralizedModelName ?>[$fixtureAlias]['status'];

        $model = $this->getModel($expectedId);

        $form = $this->mockForm([
            'name' => $name,
        ], $model);

        $domain = $this->mockDomain($form, $model);
        $this->assertTrue($domain->process());
        $this->assertEquals($expectedId, $domain->getId());

        $model = $this->getModel($expectedId);
        $this->assertEquals($name, $model->name);
        $this->assertEquals($expectedStatus, $model->status);
    }
}
