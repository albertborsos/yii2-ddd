[![Build Status](https://travis-ci.org/albertborsos/yii2-ddd.svg?branch=master)](https://travis-ci.org/albertborsos/yii2-ddd)

DDD Classes for Yii 2.0
=======================
Classes for a Domain-Driven Design inspired workflow with Yii 2.0 Framework

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Run

```
php composer.phar require --prefer-dist albertborsos/yii2-ddd
```

to the require section of your `composer.json` file.

Usage
-----

Lets see an example with a standard `App` model which is an implementation of `\yii\db\ActiveRecord` and generated via `gii`.
I recommend to do not make any modification with this class, but make it `abstract` to prevent direct usages.

Then create a business model which extends our abstract active record class and implements `BusinessObject` interface.
Every business logic will be implemented in this class.

```php
<?php

namespace application\domains\app\business;

use application\domains\app\activerecords\AbstractApp;
use albertborsos\ddd\interfaces\BusinessObject;

class App extends AbstractApp implements BusinessObject
{
    // business logic
}
```

#### Lets create a new record!

We will need a new `FormObject` which will be responsible for the data validation.
And we will need a `service` model, which handles the business logic with the related models too.


A simple example for a `FormObject`:

```php
<?php

namespace application\services\app\forms;

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

And a simple example for a `service`. Services are expecting that the values in the `FormObject` are valid values.
That is why it is just store the values. The validation will be handled in the controller.

```php
<?php

namespace application\services\app;

use albertborsos\ddd\models\AbstractService;
use application\services\app\forms\CreateAppLanguageForm;
use application\domains\app\business\App;
use yii\base\Exception;

class CreateAppService extends AbstractService
{
    /**
     * Business logic to store data for multiple resources.
     *
     * @return mixed
     */
    public function execute()
    {
        try {
            $model = new App();
            $model->load($this->getForm()->attributes, '');

            if ($model->save()) {
                $this->assignLanguages($model->id, $this->getForm()->languages);
                $this->setId($model->id);

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

            $service = new CreateAppLanguageService($form);
            if ($service->execute() === false) {
                throw new Exception('Unable to save language for this app');
            }
        }
    }
}

```

And this is how you can use it in the controller

```php
<?php

namespace application\controllers;

use Yii;
use application\services\app\forms\CreateAppForm;
use application\services\app\CreateAppService;

class AppController extends \yii\web\Controller
{
    public function actionCreate()
    {
        $form = new CreateAppForm();
        
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $service = new CreateAppService($form);
            if ($service->execute()) {
                AlertWidget::addSuccess('App created successfully!');
                return $this->redirect(['view', 'id' => $service->getId()]);
            }
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }
}

```
