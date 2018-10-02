<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

use albertborsos\ddd\tests\support\base\AbstractFormTest;

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getUpdateFormClass())) ?> extends AbstractFormTest
{
    protected $formClass = <?= $generator->getUpdateFormClass() ?>::class;
    protected $modelClass = <?= $generator->modelClass ?>::class;

    public function fixtures()
    {
        return [
            '<?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>' => \tests\codeception\unit\fixtures\<?= $modelClassBaseName ?>Fixture::class,
        ];
    }

    public function invalidDataProvider()
    {
        return [
            'invalid name' => ['name', '<?= lcfirst($modelClassBaseName) ?>1', '<?= $modelClassBaseName ?> name'],
        ];
    }

    /**
     * @skip
     * @dataProvider invalidDataProvider
     */
    public function testShouldNotPassFormWithInvalidData($expectedErrorAttribute, $<?= lcfirst($modelClassBaseName) ?>Alias, $name)
    {
        /** @var <?= $generator->getUpdateFormClass() ?> $form */
        $form = $this->mockForm([
            'name' => $name,
        ], $this->getModel($this->tester->grabFixture('<?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>', $<?= lcfirst($modelClassBaseName) ?>Alias)['id']));

        $this->assertFalse($form->validate());
        $this->assertCount(1, $form->getErrors(), \yii\helpers\Json::errorSummary($form));
        $this->assertArrayHasKey($expectedErrorAttribute, $form->getErrors());
    }

    public function validDataProvider()
    {
        return [
            'valid name' => ['<?= lcfirst($modelClassBaseName) ?>1', '<?= $modelClassBaseName ?> name'],
        ];
    }

    /**
     * @skip
     * @dataProvider validDataProvider
     */
    public function testShouldPassFormWithValidData($<?= lcfirst($modelClassBaseName) ?>Alias, $name)
    {
        /** @var <?= $generator->getUpdateFormClass() ?> $form */
        $form = $this->mockForm([
            'name' => $name,
        ], $this->getModel($this->tester->grabFixture('<?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>', $<?= lcfirst($modelClassBaseName) ?>Alias)['id']));

        $this->assertTrue($form->validate(), \yii\helpers\Json::errorSummary($form));
        $this->assertEmpty($form->getErrors());
    }
}
