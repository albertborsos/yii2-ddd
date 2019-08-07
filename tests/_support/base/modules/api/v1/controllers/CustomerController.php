<?php

namespace albertborsos\ddd\tests\support\base\modules\api\v1\controllers;

use albertborsos\ddd\rest\active\CreateAction;
use albertborsos\ddd\rest\active\DeleteAction;
use albertborsos\ddd\rest\active\IndexAction;
use albertborsos\ddd\rest\active\UpdateAction;
use albertborsos\ddd\rest\active\ViewAction;
use yii\rest\OptionsAction;
use mito\cms\core\rest\api\Controller;
use albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerActiveRepositoryInterface;
use albertborsos\ddd\tests\support\base\modules\api\v1\services\customer\forms\CreateCustomerForm;
use albertborsos\ddd\tests\support\base\modules\api\v1\services\customer\forms\UpdateCustomerForm;
use albertborsos\ddd\tests\support\base\modules\api\v1\services\customer\forms\DeleteCustomerForm;
use albertborsos\ddd\tests\support\base\modules\api\v1\services\customer\CreateCustomerService;
use albertborsos\ddd\tests\support\base\modules\api\v1\services\customer\UpdateCustomerService;
use albertborsos\ddd\tests\support\base\modules\api\v1\services\customer\DeleteCustomerService;

class CustomerController extends Controller
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

/**
 * @OA\Get(
 *     path="/api/v1/customers",
 *     tags={"api/v1/customers"},
 *     summary="List of available Customers",
 *     @OA\Parameter(
 *         in="query",
 *         name="expand",
 *         required=false,
 *         @OA\Items(
 *             type="string",
 *             enum={"customerAddresses"},
 *         ),
 *     ),
 *     @OA\Parameter(ref="#/components/parameters/pageSize"),
 *     @OA\Parameter(ref="#/components/parameters/page"),
 *     @OA\Response(
 *         response = 200,
 *         description = "Successful Response",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                      property="items",
 *                      type="array",
 *                      @OA\Items(ref="#/components/schemas/Customer"),
 *                 ),
 *                 @OA\Property(
 *                      property="_meta",
 *                      ref="#/components/schemas/MetaFields",
 *                 ),
 *             ),
 *         ),
 *     ),
 * ),
 */

/**
 * @OA\Get(
 *     path="/api/v1/customers/{id}",
 *     tags={"api/v1/customers"},
 *     summary="Find Customer by ID",
 *     @OA\Parameter(
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Items(
 *             type="integer",
 *             default=1,
 *         ),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="expand",
 *         required=false,
 *         @OA\Items(
 *             type="string",
 *             enum={"customerAddresses"},
 *         ),
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "Successful Response",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/Customer"),
 *         ),
 *     ),
 * ),
 */
