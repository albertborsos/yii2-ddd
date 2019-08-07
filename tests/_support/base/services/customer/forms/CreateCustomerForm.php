<?php

namespace albertborsos\ddd\tests\support\base\services\customer\forms;

/**
 * @OA\Schema(
 *     schema="CreateCustomerFormProperties",
 *     @OA\Property(property="name", ref="#/components/schemas/Customer/properties/name"),
 * ),
 *
 * @OA\RequestBody(
 *     request="CreateCustomerForm",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(ref="#/components/schemas/CreateCustomerFormProperties"),
 *     ),
 *     @OA\MediaType(
 *         mediaType="multipart/form-data",
 *         @OA\Schema(ref="#/components/schemas/CreateCustomerFormProperties"),
 *     ),
 * ),
 */
class CreateCustomerForm extends AbstractCustomerForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            // [['name'], 'required'],
        ]);
    }
}
