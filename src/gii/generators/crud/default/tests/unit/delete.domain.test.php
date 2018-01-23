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

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getDeleteDomainClass())) ?> extends AbstractDomainTest
{
    protected $formClass = '<?= $generator->getDeleteFormClass()?>';
    protected $domainClass = '<?= $generator->getDeleteDomainClass()?>';
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
     * because invalid events should not reach the process method of domain classes.
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

        $domain = $this->mockDomain($form, $model);
        $this->assertTrue($domain->process());
        $this->assertEquals($expectedId, $domain->getId());

        $model = $this->getModel($expectedId);
        $this->assertNull($model);
    }
}
