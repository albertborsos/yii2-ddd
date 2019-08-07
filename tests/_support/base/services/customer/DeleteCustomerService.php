<?php

namespace albertborsos\ddd\tests\support\base\services\customer;

use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\services\customer\forms\DeleteCustomerForm;

/**
 * @OA\Delete(
 *     path="/module/submodule/version/customers/{id}",
 *     tags={"module/submodule/version/customers"},
 *     summary="Delete Customer",
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
 *     @OA\Response(
 *         response = 204,
 *         description = "The resource was deleted successfully.",
 *     ),
 * ),
 */
class DeleteCustomerService extends AbstractCustomerService
{
    public function __construct(DeleteCustomerForm $form, Customer $model, $config = [])
    {
        parent::__construct($form, $model, $config);
    }

    /**
     * @return bool
     */
    public function execute()
    {
        return $this->getRepository()->delete($this->getEntity());
    }
}
