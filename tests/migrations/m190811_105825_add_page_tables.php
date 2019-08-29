<?php

class m190811_105825_add_page_tables extends \yii\db\Migration
{
    const TABLE_PAGE = '{{%page}}';
    const TABLE_PAGE_SLUG = '{{%page_slug}}';

    public function safeUp()
    {
        $this->createTable(self::TABLE_PAGE, [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(),
            'category' => $this->string(),
            'title' => $this->string(),
            'description' => $this->text(),
            'date' => $this->date(),
            'slug' => $this->string(),
            'sort_order' => $this->bigInteger(),
            'created_at' => $this->bigInteger(),
            'created_by' => $this->bigInteger(),
            'updated_at' => $this->bigInteger(),
            'updated_by' => $this->bigInteger(),
            'status' => $this->boolean(),
        ]);

        $this->createTable(self::TABLE_PAGE_SLUG, [
            'id' => $this->bigPrimaryKey(),
            'page_id' => $this->bigInteger(),
            'slug' => $this->string(),
            'created_at' => $this->bigInteger(),
            'created_by' => $this->bigInteger(),
            'updated_at' => $this->bigInteger(),
            'updated_by' => $this->bigInteger(),
            'status' => $this->boolean(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable(self::TABLE_PAGE_SLUG);
        $this->dropTable(self::TABLE_PAGE);
    }
}
