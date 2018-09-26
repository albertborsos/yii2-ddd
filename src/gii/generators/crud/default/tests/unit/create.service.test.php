<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getCreateServiceClass())) ?> extends AbstractServiceTest
{
    protected $formClass = '<?= $generator->getCreateFormClass() ?>';
    protected $serviceClass = '<?= $generator->getCreateServiceClass()?>';

    protected function tearDown()
    {
        $models = <?= $generator->modelClass?>::find()->all();
        foreach ($models as $model) {
            $model->delete();
        }
        parent::tearDown();
    }

    /**
     * Test only valid events here.
     * Test invalid events in `<?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getCreateFormClass())) ?>`,
     * because invalid events should not reach the execute method of service classes.
     */
    public function create<?= $modelClassBaseName ?>Provider()
    {
        return [
            'create <?= $modelClassBaseName ?>' => ['<?= $modelClassBaseName ?> name']
        ];
    }

    /**
     * @skip
     * @dataProvider create<?= $modelClassBaseName?>Provider
     */
    public function testCreate<?= $modelClassBaseName?>($name)
    {
        $form = $this->mockForm([
            'name' => $name,
        ]);

        $service = $this->mockService($form);
        $this->assertTrue($service->execute());

        $model = <?= $generator->modelClass ?>::findOne($service->getId());

        $this->assertEquals($name, $model->name);
<?php if (in_array('status', $generator->getColumnNames())) : ?>
        $this->assertEquals(<?= $generator->modelClass ?>::STATUS_ACTIVE, $model->status);
<?php endif; ?>
    }
}
