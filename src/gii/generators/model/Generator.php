<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace albertborsos\ddd\gii\generators\model;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;
use yii\helpers\StringHelper;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\generators\model\Generator
{
    public $ns = 'app\modules\moduleName\domains\domainName\activerecords';
    public $generateRelations = self::RELATIONS_ALL_INVERSE;

    public function formView()
    {
        return Yii::getAlias('@vendor/yiisoft/yii2-gii/src/generators/model/form.php');
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            // model :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/Abstract' . $modelClassName . '.php',
                $this->render('model.php', $params)
            );

            // query :
            if ($queryClassName) {
                $params['className'] = $queryClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php',
                    $this->render('query.php', $params)
                );
            }

            $businessFile = Yii::getAlias('@' . str_replace('\\', '/', $this->getBusinessClass())) . '.php';

            $files = array_merge($files, [
                new CodeFile($businessFile, $this->render('business.php', $params)),
            ]);
        }

        return $files;
    }

    /**
     * @param bool $namespaceOnly
     * @return string
     */
    public function getBusinessClass($namespaceOnly = false)
    {
        $parts = StringHelper::explode($this->ns, '\\');
        // unset `activerecords`
        $activeRecordsKey = array_search('activerecords', $parts);
        $parts[$activeRecordsKey] = 'business';
        $parts[] = $this->modelClass;

        if ($namespaceOnly) {
            array_pop($parts);
        }

        return implode('\\', $parts);
    }
}
