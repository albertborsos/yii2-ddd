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
class Generator extends \yii\gii\Generator
{
    public $modelClass;
    public $controllerClass;
    public $viewPath;
    public $baseControllerClass = 'yii\web\Controller';
    public $indexWidgetType = 'grid';
    public $searchModelClass = '';
    public $generateTests = false;
    public $testPath = '@app/tests';

    /**
     * @var boolean whether to wrap the `GridView` or `ListView` widget with the `yii\widgets\Pjax` widget
     * @since 2.0.5
     */
    public $enablePjax = false;


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'CRUD Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates a controller and views that implement CRUD (Create, Read, Update, Delete)
            operations for the specified data model.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['controllerClass', 'modelClass', 'searchModelClass', 'baseControllerClass'], 'filter', 'filter' => 'trim'],
            [['modelClass', 'controllerClass', 'baseControllerClass', 'indexWidgetType', 'testPath'], 'required'],
            [['searchModelClass'], 'compare', 'compareAttribute' => 'modelClass', 'operator' => '!==', 'message' => 'Search Model Class must not be equal to Model Class.'],
            [['modelClass', 'controllerClass', 'baseControllerClass', 'searchModelClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['modelClass'], 'validateClass', 'params' => ['extends' => BaseActiveRecord::className()]],
            [['baseControllerClass'], 'validateClass', 'params' => ['extends' => Controller::className()]],
            [['controllerClass'], 'match', 'pattern' => '/Controller$/', 'message' => 'Controller class name must be suffixed with "Controller".'],
            [['controllerClass'], 'match', 'pattern' => '/(^|\\\\)[A-Z][^\\\\]+Controller$/', 'message' => 'Controller class name must start with an uppercase letter.'],
            [['controllerClass', 'searchModelClass'], 'validateNewClass'],
            [['indexWidgetType'], 'in', 'range' => ['grid', 'list']],
            [['modelClass'], 'validateModelClass'],
            [['enableI18N', 'enablePjax'], 'boolean'],
            [['generateTests'], 'boolean'],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
            ['viewPath', 'safe'],
            ['testPath', 'safe'],
        ]);
    }

    public function validateClass($attribute, $params)
    {
        if (parent::validateClass($attribute, $params)) {
            $parts = StringHelper::explode($this->modelClass, '\\');
            $resourceKey = array_search('resources', $parts);
            if ($resourceKey === false) {
                $this->addError($attribute, 'Missing `resources` part in model namespace!');
            }
            // unset `business`
            $businessKey = array_search('business', $parts);
            if ($businessKey === false) {
                $this->addError($attribute, 'Missing `business` part in model namespace!');
            }
            return true;
        }

        return false;
    }

    /**
     * An inline validator that checks if the attribute value refers to a valid namespaced class name.
     * The validator will check if the directory containing the new class file exist or not.
     * @param string $attribute the attribute being validated
     * @param array $params the validation options
     * @throws \yii\base\Exception
     */
    public function validateNewClass($attribute, $params)
    {
        $class = ltrim($this->$attribute, '\\');
        if (($pos = strrpos($class, '\\')) !== false) {
            $ns = substr($class, 0, $pos);
            $path = Yii::getAlias('@' . str_replace('\\', '/', $ns), false);
            if ($path && !is_dir($path)) {
                FileHelper::createDirectory($path);
            }
        }
        parent::validateNewClass($attribute, $params);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'modelClass' => 'Model Class',
            'controllerClass' => 'Controller Class',
            'viewPath' => 'View Path',
            'testPath' => 'Test Path',
            'baseControllerClass' => 'Base Controller Class',
            'indexWidgetType' => 'Widget Used in Index Page',
            'searchModelClass' => 'Search Model Class',
            'enablePjax' => 'Enable Pjax',
            'generateTests' => 'Generate test files',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'modelClass' => 'This is the ActiveRecord class associated with the table that CRUD will be built upon.
                You should provide a fully qualified class name, e.g., <code>app\models\Post</code>.',
            'controllerClass' => 'This is the name of the controller class to be generated. You should
                provide a fully qualified namespaced class (e.g. <code>app\controllers\PostController</code>),
                and class name should be in CamelCase with an uppercase first letter. Make sure the class
                is using the same namespace as specified by your application\'s controllerNamespace property.',
            'viewPath' => 'Specify the directory for storing the view scripts for the controller. You may use path alias here, e.g.,
                <code>/var/www/basic/controllers/views/post</code>, <code>@app/views/post</code>. If not set, it will default
                to <code>@app/views/ControllerID</code>',
            'testPath' => 'Specify the directory for storing the test scripts for the controller. You may use path alias here, e.g.,
                <code>@app/tests/codeception</code>.',
            'baseControllerClass' => 'This is the class that the new CRUD controller class will extend from.
                You should provide a fully qualified class name, e.g., <code>yii\web\Controller</code>.',
            'indexWidgetType' => 'This is the widget type to be used in the index page to display list of the models.
                You may choose either <code>GridView</code> or <code>ListView</code>',
            'searchModelClass' => 'This is the name of the search model class to be generated. You should provide a fully
                qualified namespaced class name, e.g., <code>app\models\PostSearch</code>.',
            'enablePjax' => 'This indicates whether the generator should wrap the <code>GridView</code> or <code>ListView</code>
                widget on the index page with <code>yii\widgets\Pjax</code> widget. Set this to <code>true</code> if you want to get
                sorting, filtering and pagination without page refreshing.',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['controller.php'];
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['baseControllerClass', 'indexWidgetType', 'testPath']);
    }

    /**
     * Checks if model class is valid
     */
    public function validateModelClass()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pk = $class::primaryKey();
        if (empty($pk)) {
            $this->addError('modelClass', "The table associated with $class must have primary key(s).");
        }
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

    /**
     * @return string the controller view path
     */
    public function getViewPath()
    {
        if (empty($this->viewPath)) {
            return Yii::getAlias('@app/views/' . $this->getControllerID());
        } else {
            return Yii::getAlias($this->viewPath);
        }
    }

    public function getNameAttribute()
    {
        foreach ($this->getColumnNames() as $name) {
            if (!strcasecmp($name, 'name') || !strcasecmp($name, 'title')) {
                return $name;
            }
        }
        /* @var $class \yii\db\ActiveRecord */
        $class = $this->modelClass;
        $pk = $class::primaryKey();

        return $pk[0];
    }

    /**
     * Generates code for active field
     * @param string $attribute
     * @return string
     */
    public function generateActiveField($attribute)
    {
        $tableSchema = $this->getTableSchema();
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\$form->field(\$model, '$attribute')->passwordInput()";
            } else {
                return "\$form->field(\$model, '$attribute')";
            }
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->phpType === 'boolean') {
            return "\$form->field(\$model, '$attribute')->checkbox()";
        } elseif ($column->type === 'text') {
            return "\$form->field(\$model, '$attribute')->textarea(['rows' => 6])";
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'passwordInput';
            } else {
                $input = 'textInput';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return "\$form->field(\$model, '$attribute')->dropDownList("
                    . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)).", ['prompt' => ''])";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return "\$form->field(\$model, '$attribute')->$input()";
            } else {
                return "\$form->field(\$model, '$attribute')->$input(['maxlength' => true])";
            }
        }
    }

    /**
     * Generates code for active search field
     * @param string $attribute
     * @return string
     */
    public function generateActiveSearchField($attribute)
    {
        $tableSchema = $this->getTableSchema();
        $placeholder = $this->enableI18N ? "Yii::t('" . $this->messageCategory . "', '" . Inflector::camel2words($attribute) . "')" : false;
        $textInput = $placeholder ? "->textInput(['placeholder' => $placeholder])" : null;
        if ($tableSchema === false) {
            return "\$form->field(\$model, '$attribute')"  . $textInput;
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->phpType === 'boolean') {
            return "\$form->field(\$model, '$attribute')->checkbox()";
        } else {
            return "\$form->field(\$model, '$attribute')" . $textInput;
        }
    }

    /**
     * Generates column format
     * @param \yii\db\ColumnSchema $column
     * @return string
     */
    public function generateColumnFormat($column)
    {
        if ($column->phpType === 'boolean') {
            return 'boolean';
        } elseif ($column->type === 'text') {
            return 'ntext';
        } elseif (stripos($column->name, 'time') !== false && $column->phpType === 'integer') {
            return 'datetime';
        } elseif (stripos($column->name, 'email') !== false) {
            return 'email';
        } elseif (stripos($column->name, 'url') !== false) {
            return 'url';
        } else {
            return 'text';
        }
    }

    /**
     * Generates validation rules for the search model.
     * @return array the generated validation rules
     */
    public function generateSearchRules()
    {
        if (($table = $this->getTableSchema()) === false) {
            return ["[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"];
        }
        $types = [];
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                default:
                    $types['safe'][] = $column->name;
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }

        return $rules;
    }

    /**
     * @return array searchable attributes
     */
    public function getSearchAttributes()
    {
        return $this->getColumnNames();
    }

    /**
     * Generates the attribute labels for the search model.
     * @return array the generated attribute labels (name => label)
     */
    public function generateSearchLabels()
    {
        /* @var $model \yii\base\Model */
        $model = new $this->modelClass();
        $attributeLabels = $model->attributeLabels();
        $labels = [];
        foreach ($this->getColumnNames() as $name) {
            if (isset($attributeLabels[$name])) {
                $labels[$name] = $attributeLabels[$name];
            } else {
                if (!strcasecmp($name, 'id')) {
                    $labels[$name] = 'ID';
                } else {
                    $label = Inflector::camel2words($name);
                    if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                        $label = substr($label, 0, -3) . ' ID';
                    }
                    $labels[$name] = $label;
                }
            }
        }

        return $labels;
    }

    /**
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions()
    {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->modelClass;
            /* @var $model \yii\base\Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "'{$column}' => \$this->{$column},";
                    break;
                default:
                    $likeConditions[] = "->andFilterWhere(['like', '{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

    /**
     * Generates URL parameters
     * @return string
     */
    public function generateUrlParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                return "'id' => (string)\$model->{$pks[0]}";
            } else {
                return "'id' => \$model->{$pks[0]}";
            }
        } else {
            $params = [];
            foreach ($pks as $pk) {
                if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                    $params[] = "'$pk' => (string)\$model->$pk";
                } else {
                    $params[] = "'$pk' => \$model->$pk";
                }
            }

            return implode(', ', $params);
        }
    }

    /**
     * Generates action parameters
     * @return string
     */
    public function generateActionParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            return '$id';
        } else {
            return '$' . implode(', $', $pks);
        }
    }

    /**
     * Generates parameter tags for phpdoc
     * @return array parameter tags for phpdoc
     */
    public function generateActionParamComments()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (($table = $this->getTableSchema()) === false) {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . (substr(strtolower($pk), -2) == 'id' ? 'integer' : 'string') . ' $' . $pk;
            }

            return $params;
        }
        if (count($pks) === 1) {
            return ['@param ' . $table->columns[$pks[0]]->phpType . ' $id'];
        } else {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . $table->columns[$pk]->phpType . ' $' . $pk;
            }

            return $params;
        }
    }

    /**
     * Returns table schema for current model class or false if it is not an active record
     * @return boolean|\yii\db\TableSchema
     */
    public function getTableSchema()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema();
        } else {
            return false;
        }
    }

    /**
     * @return array model column names
     */
    public function getColumnNames()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema()->getColumnNames();
        } else {
            /* @var $model \yii\base\Model */
            $model = new $class();

            return $model->attributes();
        }
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
