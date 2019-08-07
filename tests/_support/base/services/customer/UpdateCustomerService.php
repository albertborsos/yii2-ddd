<?php

namespace albertborsos\ddd\tests\support\base\services\customer;

use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\services\customer\forms\UpdateCustomerForm;

/**
 * @OA\Put(
 *     path="/module/submodule/version/customers/{id}",
 *     tags={"module/submodule/version/customers"},
 *     summary="Update Customer",
 *     @OA\Parameter(
 *         in="path",
 *         name="id",
 *         description="ID of the Customer",
 *         required=true,
 *         example=1,
 *         @OA\Schema(
 *             type="integer",
 *         ),
 *     ),
 *     @OA\RequestBody(ref="#/components/requestBodies/UpdateCustomerForm"),
 *     @OA\Response(
 *         response = 200,
 *         description = "Successful Response",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Items(ref="#/components/schemas/Customer"),
 *         ),
 *     ),
 * ),
 */
class UpdateCustomerService extends AbstractCustomerService
{
    public function __construct(UpdateCustomerForm $form, Customer $model, $config = [])
    {
        parent::__construct($form, $model, $config);
    }
}
