<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getDeleteFormClass())) ?> extends AbstractFormTest
{
    protected $formClass = '<?= $generator->getDeleteFormClass() ?>';
    protected $modelClass = '<?= $generator->modelClass ?>';

    public function fixtures()
    {
        return [
            '<?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>' => \tests\codeception\unit\fixtures\<?= $modelClassBaseName ?>Fixture::className(),
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
    public function testNotPassFormWithInvalidData($expectedErrorAttribute, $<?= lcfirst($modelClassBaseName) ?>Alias)
    {
        /** @var <?= $generator->getDeleteFormClass() ?> $form */
        $form = $this->mockForm([], $this->getModel($this-><?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>[$<?= lcfirst($modelClassBaseName) ?>Alias]['id']));

        $this->assertFalse($form->validate());
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
    public function testPassFormWithValidData($<?= lcfirst($modelClassBaseName) ?>Alias)
    {
        /** @var <?= $generator->getDeleteFormClass() ?> $form */
        $form = $this->mockForm([], $this->getModel($this-><?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>[$<?= lcfirst($modelClassBaseName) ?>Alias]['id']));

        $this->assertTrue($form->validate(), \yii\helpers\Html::errorSummary($form));
        $this->assertEmpty($form->getErrors());
    }
}
