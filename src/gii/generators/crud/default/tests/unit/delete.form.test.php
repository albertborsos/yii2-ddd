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

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getDeleteFormClass())) ?> extends AbstractFormTest
{
    protected $formClass = <?= $generator->getDeleteFormClass() ?>::class;
    protected $modelClass = <?= $generator->modelClass ?>::class;

    public function _fixtures()
    {
        return [
            '<?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>' => \tests\codeception\unit\fixtures\<?= $modelClassBaseName ?>Fixture::class,
        ];
    }

    public function invalidDataProvider()
    {
        return [
            '<?= lcfirst($modelClassBaseName) ?> has child' => ['hasChild', '<?= lcfirst($modelClassBaseName) ?>1'],
        ];
    }

    /**
     * @skip
     * @dataProvider invalidDataProvider
     */
    public function testShouldNotPassFormWithInvalidData($expectedErrorAttribute, $<?= lcfirst($modelClassBaseName) ?>Alias)
    {
        /** @var <?= $generator->getDeleteFormClass() ?> $form */
        $form = $this->mockForm([], $this->getModel($this->tester->grabFixture('<?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>', $<?= lcfirst($modelClassBaseName) ?>Alias)['id']));

        $this->assertFalse($form->validate());
        $this->assertCount(1, $form->getErrors(), \yii\helpers\Json::errorSummary($form));
        $this->assertArrayHasKey($expectedErrorAttribute, $form->getErrors());
    }

    public function validDataProvider()
    {
        return [
            '<?= lcfirst($modelClassBaseName) ?> has not got child' => ['<?= lcfirst($modelClassBaseName) ?>1'],
        ];
    }

    /**
     * @skip
     * @dataProvider validDataProvider
     */
    public function testShouldPassFormWithValidData($<?= lcfirst($modelClassBaseName) ?>Alias)
    {
        /** @var <?= $generator->getDeleteFormClass() ?> $form */
        $form = $this->mockForm([], $this->getModel($this->tester->grabFixture('<?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>', $<?= lcfirst($modelClassBaseName) ?>Alias)['id']));

        $this->assertTrue($form->validate(), \yii\helpers\Json::errorSummary($form));
        $this->assertEmpty($form->getErrors());
    }
}
