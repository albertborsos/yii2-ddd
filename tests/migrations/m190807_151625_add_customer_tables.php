<?php

use yii\db\Schema;

class m190807_151625_add_customer_tables extends \yii\db\Migration
{
    const TABLE_NAME_CUSTOMER = 'customer';
    const TABLE_NAME_CUSTOMER_ADDRESS = 'customer_address';
    const FK_CUSTOMER_ADDRESS_CUSTOMER_ID_CUSTOMER_ID = 'fk_customer_address_customer_id__customer_id';

    public function up()
    {
        $this->createTable(self::TABLE_NAME_CUSTOMER, [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(),
            'slug' => $this->string(),
            'created_at' => $this->bigInteger(),
            'created_by' => $this->bigInteger(),
            'updated_at' => $this->bigInteger(),
            'updated_by' => $this->bigInteger(),
            'status' => $this->integer(),
        ]);

        $this->createTable(self::TABLE_NAME_CUSTOMER_ADDRESS, [
            'id' => $this->bigPrimaryKey(),
            'customer_id' => $this->bigInteger(),
            'zip_code' => $this->integer(),
            'city' => $this->string(),
            'street' => $this->string(),
            'created_at' => $this->bigInteger(),
            'created_by' => $this->bigInteger(),
            'updated_at' => $this->bigInteger(),
            'updated_by' => $this->bigInteger(),
        ]);

        $this->addForeignKey(self::FK_CUSTOMER_ADDRESS_CUSTOMER_ID_CUSTOMER_ID, self::TABLE_NAME_CUSTOMER_ADDRESS, 'customer_id', self::TABLE_NAME_CUSTOMER, 'id');
    }

    public function down()
    {
        $this->dropForeignKey(self::FK_CUSTOMER_ADDRESS_CUSTOMER_ID_CUSTOMER_ID, self::TABLE_NAME_CUSTOMER_ADDRESS);

        $this->dropTable(self::TABLE_NAME_CUSTOMER_ADDRESS);
        $this->dropTable(self::TABLE_NAME_CUSTOMER);
    }
}
