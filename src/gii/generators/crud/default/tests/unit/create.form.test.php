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
    public function testShouldNotPassFormWithInvalidData($expectedErrorAttribute, $name)
    {
        /** @var <?= $generator->getCreateFormClass() ?> $form */
        $form = $this->mockForm([
            'name' => $name,
        ]);

        $this->assertFalse($form->validate());
        $this->assertCount(1, $form->getErrors(), \yii\helpers\Json::encode($form->getErrors()));
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
    public function testShouldPassFormWithValidData($name)
    {
        /** @var <?= $generator->getCreateFormClass() ?> $form */
        $form = $this->mockForm([
            'name' => $name,
        ]);

        $this->assertTrue($form->validate(), \yii\helpers\Json::encode($form->getErrors()));
        $this->assertEmpty($form->getErrors());
    }
}
