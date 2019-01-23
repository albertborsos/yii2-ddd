<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace albertborsos\ddd\gii\generators\crud;

use Yii;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;

/**
 * Generates CRUD
 *
 * @property array $columnNames Model column names. This property is read-only.
 * @property string $controllerID The controller ID (without the module ID prefix). This property is
 * read-only.
 * @property array $searchAttributes Searchable attributes. This property is read-only.
 * @property boolean|\yii\db\TableSchema $tableSchema This property is read-only.
 * @property string $viewPath The controller view path. This property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\generators\crud\Generator
{
    public $generateTests = false;
    public $testPath = '@app/tests';

    /**
     * @var boolean whether to wrap the `GridView` or `ListView` widget with the `yii\widgets\Pjax` widget
     * @since 2.0.5
     */
    public $enablePjax = true;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['generateTests'], 'boolean'],
            ['testPath', 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'testPath' => 'Test Path',
            'generateTests' => 'Generate test files',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'testPath' => 'Specify the directory for storing the test scripts for the controller. You may use path alias here, e.g.,
                <code>@app/tests/codeception</code>.',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['testPath']);
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $controllerFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php');
        $createServiceFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->getCreateServiceClass(), '\\')) . '.php');
        $createFormFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->getCreateFormClass(), '\\')) . '.php');
        $updateServiceFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->getUpdateServiceClass(), '\\')) . '.php');
        $updateFormFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->getUpdateFormClass(), '\\')) . '.php');
        $deleteServiceFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->getDeleteServiceClass(), '\\')) . '.php');
        $deleteFormFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->getDeleteFormClass(), '\\')) . '.php');
        $toggleStatusServiceFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->getToggleStatusServiceClass(), '\\')) . '.php');
        $toggleStatusFormFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->getToggleStatusFormClass(), '\\')) . '.php');

        $fixtureDataFile = $this->getUnitTestFixtureDataFile() . '.php';
        $fixtureClassFile = $this->getUnitTestFixtureClass() . '.php';
        $createServiceTestFile = ltrim($this->getTestFilePath($this->getCreateServiceClass()), '\\') . '.php';
        $createFormTestFile = ltrim($this->getTestFilePath($this->getCreateFormClass()), '\\') . '.php';
        $updateServiceTestFile = ltrim($this->getTestFilePath($this->getUpdateServiceClass()), '\\') . '.php';
        $updateFormTestFile = ltrim($this->getTestFilePath($this->getUpdateFormClass()), '\\') . '.php';
        $deleteServiceTestFile = ltrim($this->getTestFilePath($this->getDeleteServiceClass()), '\\') . '.php';
        $deleteFormTestFile = ltrim($this->getTestFilePath($this->getDeleteFormClass()), '\\') . '.php';
        $toggleStatusServiceTestFile = ltrim($this->getTestFilePath($this->getToggleStatusServiceClass()), '\\') . '.php';

        $files = [
            new CodeFile($controllerFile, $this->render('controller.php')),
        ];

        if (!empty($this->searchModelClass)) {
            $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
            $files[] = new CodeFile($searchModel, $this->render('search.php'));
        }

        $files = array_merge($files, [
            new CodeFile($createFormFile, $this->render('domains/create.form.php')),
            new CodeFile($updateFormFile, $this->render('domains/update.form.php')),
            new CodeFile($deleteFormFile, $this->render('domains/delete.form.php')),
            new CodeFile($createServiceFile, $this->render('domains/create.service.php')),
            new CodeFile($updateServiceFile, $this->render('domains/update.service.php')),
            new CodeFile($deleteServiceFile, $this->render('domains/delete.service.php')),
        ]);

        if ($this->generateTests) {
            $files = array_merge($files, [
                //tests
                new CodeFile($fixtureClassFile, $this->render('tests/unit/fixture.class.php')),
                new CodeFile($fixtureDataFile, $this->render('tests/unit/fixture.php')),
                new CodeFile($createFormTestFile, $this->render('tests/unit/create.form.test.php')),
                new CodeFile($updateFormTestFile, $this->render('tests/unit/update.form.test.php')),
                new CodeFile($deleteFormTestFile, $this->render('tests/unit/delete.form.test.php')),
                new CodeFile($createServiceTestFile, $this->render('tests/unit/create.service.test.php')),
                new CodeFile($updateServiceTestFile, $this->render('tests/unit/update.service.test.php')),
                new CodeFile($deleteServiceTestFile, $this->render('tests/unit/delete.service.test.php')),
            ]);
        }

        if (in_array('status', $this->getColumnNames())) {
            $files = array_merge($files, [
                new CodeFile($toggleStatusServiceFile, $this->render('domains/toggle-status.service.php')),
                new CodeFile($toggleStatusFormFile, $this->render('domains/toggle-status.form.php')),
            ]);

            if ($this->generateTests) {
                $files = array_merge($files, [
                    new CodeFile($toggleStatusServiceTestFile, $this->render('tests//unit/toggle-status.service.test.php')),
                ]);
            }
        }

        $viewPath = $this->getViewPath();
        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $file) {
            if (empty($this->searchModelClass) && $file === '_search.php') {
                continue;
            }
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }

        return $files;
    }

    /**
     * @return string the controller ID (without the module ID prefix)
     */
    public function getControllerID()
    {
        $pos = strrpos($this->controllerClass, '\\');
        $class = substr(substr($this->controllerClass, $pos + 1), 0, -10);

        return Inflector::camel2id($class);
    }

    public function getCreateServiceClass($namespaceOnly = false)
    {
        $parts = StringHelper::explode($this->modelClass, '\\');
        // replace `resources` to `domains`
        $parts = $this->replacePart($parts, 'domains', 'services');
        $parts = $this->replacePart($parts, 'business', null);
        // replace className with domain name
        array_pop($parts);
        $parts[] = 'Create' . StringHelper::basename($this->modelClass) . 'Service';

        if ($namespaceOnly) {
            array_pop($parts);
        }

        return implode('\\', $parts);
    }

    public function getCreateFormClass($namespaceOnly = false)
    {
        $parts = StringHelper::explode($this->modelClass, '\\');
        $parts = $this->replacePart($parts, 'domains', 'services');
        $parts = $this->replacePart($parts, 'business', 'forms');
        // replace className with forms name
        array_pop($parts);
        $parts[] = 'Create' . StringHelper::basename($this->modelClass) . 'Form';

        if ($namespaceOnly) {
            array_pop($parts);
        }

        return implode('\\', $parts);
    }
    public function getToggleStatusServiceClass($namespaceOnly = false)
    {
        $parts = StringHelper::explode($this->modelClass, '\\');
        $parts = $this->replacePart($parts, 'domains', 'services');
        $parts = $this->replacePart($parts, 'business', null);
        // replace className with domain name
        array_pop($parts);
        $parts[] = 'Toggle' . StringHelper::basename($this->modelClass) . 'StatusService';

        if ($namespaceOnly) {
            array_pop($parts);
        }

        return implode('\\', $parts);
    }

    public function getToggleStatusFormClass($namespaceOnly = false)
    {
        $parts = StringHelper::explode($this->modelClass, '\\');
        $parts = $this->replacePart($parts, 'domains', 'services');
        $parts = $this->replacePart($parts, 'business', 'forms');
        // replace className with forms name
        array_pop($parts);
        $parts[] = 'Toggle' . StringHelper::basename($this->modelClass) . 'StatusForm';

        if ($namespaceOnly) {
            array_pop($parts);
        }

        return implode('\\', $parts);
    }

    public function getUpdateServiceClass($namespaceOnly = false)
    {
        $parts = StringHelper::explode($this->modelClass, '\\');
        $parts = $this->replacePart($parts, 'domains', 'services');
        $parts = $this->replacePart($parts, 'business', null);
        // replace className with domain name
        array_pop($parts);
        $parts[] = 'Update' . StringHelper::basename($this->modelClass) . 'Service';

        if ($namespaceOnly) {
            array_pop($parts);
        }

        return implode('\\', $parts);
    }

    public function getUpdateFormClass($namespaceOnly = false)
    {
        $parts = StringHelper::explode($this->modelClass, '\\');
        $parts = $this->replacePart($parts, 'domains', 'services');
        $parts = $this->replacePart($parts, 'business', 'forms');
        // replace className with forms name
        array_pop($parts);
        $parts[] = 'Update' . StringHelper::basename($this->modelClass) . 'Form';

        if ($namespaceOnly) {
            array_pop($parts);
        }

        return implode('\\', $parts);
    }

    public function getDeleteServiceClass($namespaceOnly = false)
    {
        $parts = StringHelper::explode($this->modelClass, '\\');
        $parts = $this->replacePart($parts, 'domains', 'services');
        $parts = $this->replacePart($parts, 'business', null);
        // replace className with domain name
        array_pop($parts);
        $parts[] = 'Delete' . StringHelper::basename($this->modelClass) . 'Service';

        if ($namespaceOnly) {
            array_pop($parts);
        }

        return implode('\\', $parts);
    }

    public function getDeleteFormClass($namespaceOnly = false)
    {
        $parts = StringHelper::explode($this->modelClass, '\\');
        $parts = $this->replacePart($parts, 'domains', 'services');
        $parts = $this->replacePart($parts, 'business', 'forms');
        // replace className with forms name
        array_pop($parts);
        $parts[] = 'Delete' . StringHelper::basename($this->modelClass) . 'Form';

        if ($namespaceOnly) {
            array_pop($parts);
        }

        return implode('\\', $parts);
    }

    public function getResourceClass($namespaceOnly = false)
    {
        $parts = StringHelper::explode($this->modelClass, '\\');
        $parts = $this->replacePart($parts, 'business', null);
        // replace className with resource name
        array_pop($parts);
        $parts[] = StringHelper::basename($this->modelClass) . 'Resource';

        if ($namespaceOnly) {
            array_pop($parts);
        }

        return implode('\\', $parts);
    }

    private function getUnitTestFixtureDataFile()
    {
        $parts[] = rtrim(Yii::getAlias($this->testPath), '/');
        $parts[] = 'unit/fixtures/data';
        $parts[] = strtolower(StringHelper::basename($this->modelClass));

        return implode('/', $parts);
    }

    private function getUnitTestFixtureClass()
    {
        $parts[] = rtrim(Yii::getAlias($this->testPath), '/');
        $parts[] = 'unit/fixtures';
        $parts[] = StringHelper::basename($this->modelClass) . 'Fixture';

        return implode('/', $parts);
    }

    public function getTestFilePath($mainClassName)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $mainClassName));
        $testNs = trim($this->testPath, '@/');
        $parts = StringHelper::explode($path, '/');
        $parts = $this->replacePart($parts, 'app', $testNs . '/unit');
        array_pop($parts);
        $parts[] = StringHelper::basename($mainClassName) . 'Test';

        return implode('/', $parts);
    }

    /**
     * @param $parts
     * @return mixed
     */
    private function replacePart($parts, $keyFrom, $keyTo)
    {
        $keyFromId = array_search($keyFrom, $parts);
        if ($keyFromId !== false) {
            $parts[$keyFromId] = $keyTo;
        }

        if (empty($parts[$keyFromId])) {
            unset($parts[$keyFromId]);
        }

        return $parts;
    }
}
