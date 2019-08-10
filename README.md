[![Build Status](https://travis-ci.org/albertborsos/yii2-ddd.svg?branch=master)](https://travis-ci.org/albertborsos/yii2-ddd)
[![Coverage Status](https://coveralls.io/repos/github/albertborsos/yii2-ddd/badge.svg)](https://coveralls.io/github/albertborsos/yii2-ddd)

DDD Classes for Yii 2.0
=======================
Classes for a Domain-Driven Design inspired workflow with Yii 2.0 Framework

Summary
--------
 - develop with DDD metodology, but you can use ActiveRecord classes
 - decouple business logic from ActiveRecord models into an Entity class
 - decouple database queries into a repository class
 - encapsulate business logic for different scenarios into a dedicated form and a service class

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

`TL;DR; check the tests folder for live examples`

Lets see an example with a standard `App` model which is an implementation of `\yii\db\ActiveRecord` and generated via `gii`.
I recommend to do not make any modification with this class.

Then create an entity which extends `\albertborsos\ddd\models\AbstractEntity` class and implements `\albertborsos\ddd\interfaces\EntityInterface` interface.
Every business logic will be implemented in this class.

```php
<?php

namespace application\domains\customer\entities;

use application\domains\customer\entities\CustomerLanguage;

class Customer extends AbstractEntity
{
    public $id;
    public $name;
    public $createdAt;
    public $createdBy;
    public $updatedAt;
    public $updatedBy;

    /** @var Language[] */
    public $languages;

    public function fields()
    {
        return [
            'id',
            'name',
            'createdAt',
            'createdBy',
            'updatedAt',
            'updatedBy',
        ];
    }

    public function extraFields()
    {
        return [
            'languages',
        ];
    }

    /**
     * Mapping of property keys to entity classnames.
     *
     * @return array
     */
    public function relationMapping(): array
    {
        return [
            'languages' => CustomerLanguage::class,
        ];
    }
}
```

Now this class is fully decoupled from the underlying storage.

For every entity you need to define a repository which handles the communication between the application and the storage.
For `ActiveRecord` usage you can use the `AbstractActiveRecordRepository` class, which has fully functional methods and you only need to implement the following way:

```php
<?php

namespace applcation\domains\customer\mysql;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\data\ActiveEntityDataProvider;
use application\domains\customer\interfaces\CustomerActiveRepositoryInterface;

class CustomerActiveRepository extends AbstractActiveRepository implements CustomerActiveRepositoryInterface
{
    protected $dataModelClass = \application\domains\customer\mysql\Customer::class;

    protected $entityClass = \application\domains\customer\entities\Customer::class;

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @param string $formName
     * @return ActiveEntityDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params, $formName = null): BaseDataProvider
    {
        // same as it would be a `CustomerQuery` instance
        // check `tests/_support/base/domains/customer/mysql/CustomerActiveRepository.php` for a live example 
    }
}

```

Then you have to configure the DI container in the application/module configuration:

```php
return [

    ...

    'container' => [
        'definitions' => [
            \application\domains\customer\interfaces\CustomerActiveRepositoryInterface::class => \application\domains\customer\mysql\CustomerActiveRepository::class,
            \application\domains\customer\interfaces\CustomerLanguageActiveRepositoryInterface::class => \application\domains\customer\mysql\CustomerLanguageActiveRepository::class,
        ],
    ],

    ...

];
```

#### Lets create a new record!

We will need a new `FormObject` which will be responsible for the data validation.
And we will need a `service` model, which handles the business logic with the related models too.

A simple example for a `FormObject`:

```php
<?php

namespace application\services\customer\forms;

use yii\base\Model;
use albertborsos\ddd\interfaces\FormObject;
use application\domains\customer\entities\Customer;

class CreateCustomerForm extends Customer implements FormObject
{
    public $contactLanguages;

    public function rules()
    {
        return [
            [['name'], 'trim'],
            [['name'], 'default'],
            [['name'], 'required'],
            [['contactLanguages'], 'each', 'rule' => ['in', 'range' => ['en', 'de', 'hu']]],
        ];
    }
}

```

Services are expecting that the values in the `FormObject` are valid values.
That is why it is just store the values. The validation will be handled in the controller.

```php
<?php

namespace application\services\customer;

use albertborsos\ddd\models\AbstractService;
use application\domains\customer\entities\Customer;
use application\services\customer\forms\CreateCustomerForm;
use yii\base\Exception;

class CreateCustomerService extends AbstractService
{
    /**
     * Business logic to store data for multiple resources.
     *
     * @return mixed
     */
    public function execute()
    {
        /** @var CreateCustomerForm $form */
        $form = $this->getForm();

        /** @var Customer $entity */
        $entity = $this->getRepository()->hydrate([]);
        $entity->setAttributes($form->attributes, false);

        if ($this->getRepository()->insert($entity)) {
            $this->setId($entity->id);
            return true;
        }

        $form->addErrors($entity->getErrors());

        return false;
    }

    private function assignLanguages($customerId, $languageIds)
    {
        foreach ($languageIds as $languageId) {
            $form = new CreateCustomerLanguageForm([
                'customerId' => $customerId,
                'language_id' => $languageId,
            ]);

            if ($form->validate() === false) {
                throw new Exception('Unable to validate language for this customer');
            }

            $service = new CreateCustomerLanguageService($form);
            if ($service->execute() === false) {
                throw new Exception('Unable to save language for this customer');
            }
        }
    }
}

```

And this is how you can use it in a web controller:

```php
<?php

namespace application\controllers;

use Yii;
use application\services\customer\forms\CreateCustomerForm;
use application\services\customer\CreateCustomerService;

class CustomerController extends \yii\web\Controller
{
    public function actionCreate()
    {
        $form = new CreateCustomerForm();
        
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $service = new CreateCustomerService($form);
            if ($service->execute()) {
                AlertWidget::addSuccess('Customer created successfully!');
                return $this->redirect(['view', 'id' => $service->getId()]);
            }
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }
}
```

And this is how you can use it in a REST controller:

```php
<?php

namespace application\modules\api\v1\controllers;

use Yii;
use albertborsos\rest\active\CreateAction;
use albertborsos\rest\active\DeleteAction;
use albertborsos\rest\active\IndexAction;
use albertborsos\rest\active\UpdateAction;
use albertborsos\rest\active\ViewAction;

class CustomerController extends \yii\rest\Controller
{
    public $repositoryInterface = CustomerActiveRepositoryInterface::class;

    public function actions()
    {
        return [
            'index' => IndexAction::class,
            'view' => ViewAction::class,
            'create' => [
                'class' => CreateAction::class,
                'formClass' => CreateCustomerForm::class,
                'serviceClass' => CreateCustomerService::class,
            ],
            'update' => [
                'class' => UpdateAction::class,
                'formClass' => UpdateCustomerForm::class,
                'serviceClass' => UpdateCustomerService::class,
            ],
            'delete' => [
                'class' => DeleteAction::class,
                'formClass' => DeleteCustomerForm::class,
                'serviceClass' => DeleteCustomerService::class,
            ],
            'options' => OptionsAction::class,
        ];
    }
}

```
