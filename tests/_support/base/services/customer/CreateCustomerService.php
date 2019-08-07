<?php

namespace albertborsos\ddd\tests\support\base\services\customer;

use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerForm;

/**
 * @OA\Post(
 *     path="/module/submodule/version/customers",
 *     tags={"module/submodule/version/customers"},
 *     summary="Create Customer",
 *     @OA\RequestBody(ref="#/components/requestBodies/CreateCustomerForm"),
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
class CreateCustomerService extends AbstractCustomerService
{
    public function __construct(CreateCustomerForm $form, $config = [])
    {
        parent::__construct($form, null, $config);
    }
}
