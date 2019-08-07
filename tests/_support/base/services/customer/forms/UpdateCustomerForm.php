<?php

namespace albertborsos\ddd\tests\support\base\services\customer\forms;

/**
 * @OA\Schema(
 *     schema="UpdateCustomerFormProperties",
 *     @OA\Property(property="name", ref="#/components/schemas/Customer/properties/name"),
 * ),
 *
 * @OA\RequestBody(
 *     request="UpdateCustomerForm",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(ref="#/components/schemas/UpdateCustomerFormProperties"),
 *     ),
 *     @OA\MediaType(
 *         mediaType="multipart/form-data",
 *         @OA\Schema(ref="#/components/schemas/UpdateCustomerFormProperties"),
 *     ),
 * ),
 */
class UpdateCustomerForm extends AbstractCustomerForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            // [['name'], 'required'],
        ]);
    }
}
