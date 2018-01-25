DDD Classes for Yii 2.0
=======================
Classes for Domain-Driven Development with Yii 2.0 Framework

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist albertborsos/yii2-ddd
```

or add

```
"albertborsos/yii2-ddd": "~0.1"
```

to the require section of your `composer.json` file.

Usage
-----

Lets see an example with a standard `App` model which is an implementation of `\yii\db\ActiveRecord` and generated via `gii`.
I recommend to do not make any modification with this class, but make it `abstract` to prevent direct usages.

Then create a business model which extends our abstract active record class and implements `BusinessObject` interface.
Every business logic will be implemented into this class.  

```php
<?php

namespace albertborsos\resources\app\business;

use albertborsos\resources\app\activerecords\App as AbstractApp;
use albertborsos\ddd\interfaces\BusinessObject;

class App extends AbstractApp implements BusinessObject
{
    // business logic
}
```

Then create a resource class which will be responsible to manage CRUD operations.


```php
<?php

namespace albertborsos\resources\app;

use albertborsos\ddd\models\AbstractResource;
use albertborsos\resources\app\business\App;
use Yii;

class AppResource extends AbstractResource
{
    /**
     * Business logic to insert data.
     *
     * @return bool
     */
    protected function insert()
    {
        $model = $this->getModel() ?? new App();
        $model->load($this->getForm()->attributes, '');

        $model = $this->generateApiKey($model);

        if ($model->save()) {
            $this->setId($model->id);
            return true;
        }

        $this->getForm()->addErrors($model->getErrors());
        return false;
    }

    /**
     * Business logic to update data.
     *
     * @return bool
     */
    protected function update()
    {
        $model = $this->getModel();
        $model->load($this->getForm()->attributes, '');

        if ($model->save()) {
            $this->setId($model->id);
            return true;
        }

        $this->getForm()->addErrors($model->getErrors());
        return false;
    }

    /**
     * Business logic to delete data.
     *
     * @return bool
     */
    public function delete()
    {
        /** @var App $model */
        $model = $this->getModel();
        $model->delete();
        return true;
    }

    private function generateApiKey(App $model)
    {
        $apiKey = Yii::$app->security->generateRandomString(32);

        if (App::findOne(['api_key' => $apiKey]) === null) {
            $newModel = clone $model;
            $newModel->api_key = $apiKey;

            return $newModel;
        }

        return $this->generateApiKey($model);
    }
}

```

#### Lets create a new record!

We will need a new `FormObject` which will be responsible for the data validation.
And we will need a `domain` model, which handles the business logic with the related models too.


A simple example to a `FormObject`:

```php
<?php

namespace albertborsos\domains\app\forms;

use yii\base\Model;
use albertborsos\ddd\interfaces\FormObject;

class CreateAppForm extends Model implements FormObject
{
    public $name;
    public $languages;

    public function rules()
    {
        return [
            [['name', 'languages'], 'required'],
            [['languages'], 'each', 'rule' => ['in', 'range' => ['en', 'de', 'hu']]],
        ];
    }
}

```

And a simple example to a `domain`. Domains are expecting that the values in the `FormObject` are valid values.
That is why it is just store the values. The validation will be handled in the controller.

```php
<?php

namespace albertborsos\domains\app;

use albertborsos\ddd\models\AbstractDomain;
use albertborsos\domains\app\forms\CreateAppLanguageForm;
use albertborsos\resources\app\AppResource;
use yii\base\Exception;

class CreateAppDomain extends AbstractDomain
{
    /**
     * Business logic to store data for multiple resources.
     *
     * @return mixed
     */
    public function process()
    {
        try {
            $resource = new AppResource($this->getForm());
            if ($resource->save()) {
                $this->assignLanguages($resource->getId(), $this->getForm()->languages);
                $this->setId($resource->getId());
    
                return true;
            }
        } catch(\yii\db\Exception $e) {
            $this->getForm()->addErrors(['exception' => $e->getMessage()]);
        }
        return false;
    }

    private function assignLanguages($appId, $languageIds)
    {
        foreach ($languageIds as $languageId) {
            $form = new CreateAppLanguageForm([
                'app_id' => $appId,
                'language_id' => $languageId,
            ]);

            if ($form->validate() === false) {
                throw new Exception('Unable to validate language for this app');
            }

            $domain = new CreateAppLanguageDomain($form);
            if ($domain->process() === false) {
                throw new Exception('Unable to save language for this app');
            }
        }
    }
}

```

And this is how you can use it in the controller

```php
<?php

namespace albertborsos\controllers;

use Yii;
use albertborsos\domains\app\forms\CreateAppForm;
use albertborsos\domains\app\CreateAppDomain;

class AppController extends \yii\web\Controller
{
    public function actionCreate()
    {
        $form = new CreateAppForm();
        
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $domain = new CreateAppDomain($form);
            if ($domain->process()) {
                AlertWidget::addSuccess('App created successfully!');
                return $this->redirect(['view', 'id' => $domain->getId()]);
            }
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }
}

```
