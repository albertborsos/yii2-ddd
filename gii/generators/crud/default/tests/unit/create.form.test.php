<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator app\gii\generators\crud\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getCreateFormClass())) ?> extends AbstractFormTest
{
    protected $formClass = '<?= $generator->getCreateFormClass() ?>';

    public function invalidDataProvider()
    {
        return [
            'invalid name' => ['name', '<?= $modelClassBaseName ?> name'],
        ];
    }

    /**
     * @skip
     * @dataProvider invalidDataProvider
     */
    public function testNotPassFormWithInvalidData($expectedErrorAttribute, $name)
    {
        /** @var <?= $generator->getCreateFormClass() ?> $form */
        $form = $this->mockForm([
            'name' => $name,
        ]);

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey($expectedErrorAttribute, $form->getErrors());
    }

    public function validDataProvider()
    {
        return [
            'valid name' => ['<?= $modelClassBaseName ?> name'],
        ];
    }

    /**
     * @skip
     * @dataProvider validDataProvider
     */
    public function testPassFormWithValidData($name)
    {
        /** @var <?= $generator->getCreateFormClass() ?> $form */
        $form = $this->mockForm([
            'name' => $name,
        ]);

        $this->assertTrue($form->validate(), \yii\helpers\Html::errorSummary($form));
        $this->assertEmpty($form->getErrors());
    }
}
