<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}
$createDomainClass = StringHelper::basename($generator->getCreateDomainClass());
$createFormClass = StringHelper::basename($generator->getCreateFormClass());
$updateDomainClass = StringHelper::basename($generator->getUpdateDomainClass());
$updateFormClass = StringHelper::basename($generator->getUpdateFormClass());
$toggleStatusDomainClass = StringHelper::basename($generator->getToggleStatusDomainClass());
$toggleStatusFormClass = StringHelper::basename($generator->getToggleStatusFormClass());
$resourceClass = StringHelper::basename($generator->getResourceClass());

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use app\components\AlertWidget;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\filters\AccessControl;
use <?= ltrim($generator->getCreateDomainClass(), '\\') ?>;
use <?= ltrim($generator->getCreateFormClass(), '\\') ?>;
use <?= ltrim($generator->getUpdateDomainClass(), '\\') ?>;
use <?= ltrim($generator->getUpdateFormClass(), '\\') ?>;
use <?= ltrim($generator->getToggleStatusDomainClass(), '\\') ?>;
use <?= ltrim($generator->getToggleStatusFormClass(), '\\') ?>;
use <?= ltrim($generator->getResourceClass(), '\\') ?>;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['view<?= StringHelper::basename($generator->modelClass) ?>'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['create<?= StringHelper::basename($generator->modelClass) ?>'],
                    ],
                    [
                        'actions' => ['update'<?= in_array('status', $generator->getColumnNames()) ? ", 'status'" : '' ?>],
                        'allow' => true,
                        'roles' => ['update<?= StringHelper::basename($generator->modelClass) ?>'],
                        'roleParams' => ['<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>Id' => Yii::$app->request->get('id')],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['delete<?= StringHelper::basename($generator->modelClass) ?>'],
                        'roleParams' => ['<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>Id' => Yii::$app->request->get('id')],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['view<?= StringHelper::basename($generator->modelClass) ?>'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
<?php if(in_array('status', $generator->getColumnNames())): ?>
                    'status' => ['POST'],
<?php endif; ?>
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->setTitle($action);
        return parent::beforeAction($action);
    }

    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex()
    {
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $dataProvider = $searchModel->search(Yii::$app->request->post());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
<?php else: ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
<?php endif; ?>
    }

    /**
     * Displays a single <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionView(<?= $actionParams ?>)
    {
        return $this->render('view', [
            'model' => $this->findModel(<?= $actionParams ?>),
        ]);
    }

    /**
     * Creates a new <?= $modelClass ?> model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $form = new <?= $createFormClass ?>();

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $domain = new <?= $createDomainClass ?>($form);
            if ($domain->process()) {
                AlertWidget::addSuccess('<?= $modelClass ?> created successfully!');
                return $this->redirect(['view', 'id' => $domain->getId()]);
            }

            $form->addErrors($domain->getErrors());
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);
        $form = new <?= $updateFormClass ?>($model);

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $domain = new <?= $updateDomainClass ?>($form, $model);
            if ($domain->process()) {
                AlertWidget::addSuccess('<?= $modelClass ?> updated successfully!');
                return $this->redirect(['update', 'id' => $domain->getId()]);
            }

            $form->addErrors($domain->getErrors());
        }

        return $this->render('update', [
            'model' => $form,
        ]);
    }

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionDelete(<?= $actionParams ?>)
    {
        $model = $this->findModel($id);
        $form = new <?= StringHelper::basename($generator->getDeleteFormClass())?>($model);
        if ($form->validate()) {
            $domain = new <?= StringHelper::basename($generator->getDeleteDomainClass())?>(null, $model);
            if ($domain->process()) {
                AlertWidget::addSuccess('<?= $modelClass ?> removed successfully!');
                return $this->redirect(['index']);
            }

            AlertWidget::addError(Html::errorSummary($domain));
            return $this->redirect(['index']);
        }

        AlertWidget::addError(Html::errorSummary($form));
        return $this->redirect(['index']);
    }

<?php if(in_array('status', $generator->getColumnNames())): ?>
    /**
     * Toggles the status of a <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionStatus(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);

        $form = new <?= $toggleStatusFormClass ?>($model);
        if ($form->validate()) {
            $domain = new <?= $toggleStatusDomainClass ?>($form, $model);
            if ($domain->process()) {
                AlertWidget::addSuccess('<?= $modelClass ?> updated successfully!');
                return $this->redirect(['index']);
            }
            $form->addErrors($domain->getErrors());
        }
        AlertWidget::addError(\yii\bootstrap\Html::errorSummary($form));
        return $this->redirect(['index']);
    }
<?php endif; ?>
    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?=                   $modelClass ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function setTitle(yii\base\Action $action)
    {
        switch ($action->id) {
            case 'index':
                $title = Yii::t('<?= $generator->messageCategory?>', 'List <?= Inflector::pluralize($modelClass) ?>');
                break;
            case 'create':
                $title = Yii::t('<?= $generator->messageCategory?>', 'Create new <?= strtolower($modelClass) ?>');
                break;
            case 'update':
                $title = Yii::t('<?= $generator->messageCategory?>', 'Update <?= strtolower($modelClass) ?>');
                break;
            case 'view':
                $title = Yii::t('<?= $generator->messageCategory?>', 'View <?= strtolower($modelClass) ?>');
                break;
            default:
                $title = Yii::t('<?= $generator->messageCategory?>', '<?= strtolower($modelClass) ?>');
                break;
        }

        $this->view->title = $title;
    }
}
