<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator app\gii\generators\crud\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getUpdateFormClass())) ?> extends AbstractFormTest
{
    protected $formClass = '<?= $generator->getUpdateFormClass() ?>';
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
            'invalid name' => ['name', '<?= lcfirst($modelClassBaseName) ?>1', '<?= $modelClassBaseName ?> name'],
        ];
    }

    /**
     * @skip
     * @dataProvider invalidDataProvider
     */
    public function testNotPassFormWithInvalidData($expectedErrorAttribute, $<?= lcfirst($modelClassBaseName) ?>Alias, $name)
    {
        /** @var <?= $generator->getUpdateFormClass() ?> $form */
        $form = $this->mockForm([
            'name' => $name,
        ], $this->getModel($this-><?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>[$<?= lcfirst($modelClassBaseName) ?>Alias]['id']));

        $this->assertFalse($form->validate());
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
    public function testPassFormWithValidData($<?= lcfirst($modelClassBaseName) ?>Alias, $name)
    {
        /** @var <?= $generator->getUpdateFormClass() ?> $form */
        $form = $this->mockForm([
            'name' => $name,
        ], $this->getModel($this-><?= \yii\helpers\Inflector::pluralize(lcfirst($modelClassBaseName)) ?>[$<?= lcfirst($modelClassBaseName) ?>Alias]['id']));

        $this->assertTrue($form->validate(), \yii\helpers\Html::errorSummary($form));
        $this->assertEmpty($form->getErrors());
    }
}
