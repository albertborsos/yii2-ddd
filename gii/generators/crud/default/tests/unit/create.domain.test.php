<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator app\gii\generators\crud\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

class <?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getCreateDomainClass())) ?> extends AbstractDomainTest
{
    protected $formClass = '<?= $generator->getCreateFormClass() ?>';
    protected $domainClass = '<?= $generator->getCreateDomainClass()?>';

    protected function tearDown()
    {
        $models = <?= $generator->modelClass?>::find()->all();
        foreach ($models as $model) {
            $resource = new <?= $generator->getResourceClass()?>(null, $model);
            $resource->delete();
        }
        parent::tearDown();
    }

    /**
     * Test only valid events here.
     * Test invalid events in `<?= \yii\helpers\StringHelper::basename($generator->getTestFilePath($generator->getCreateFormClass())) ?>`,
     * because invalid events should not reach the process method of domain classes.
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

        $domain = $this->mockDomain($form);
        $this->assertTrue($domain->process());

        $model = <?= $generator->modelClass ?>::findOne($domain->getId());

        $this->assertEquals($name, $model->name);
<?php if (in_array('status', $generator->getColumnNames())) : ?>
        $this->assertEquals(<?= $generator->modelClass ?>::STATUS_ACTIVE, $model->status);
<?php endif; ?>
    }
}
