<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%apartment}}`.
 */
class m201018_231237_create_apartment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%apartment}}', [
            'id' => $this->primaryKey(),
            'number' => $this->integer()->notNull(),
            'floor' => $this->integer()->notNull(),
            'room' => $this->integer()->notNull(),
            'square' => $this->float()->notNull(),
            'price' => $this->integer()->notNull(),
            'cost' => $this->integer()->notNull(),
            'status' => $this->boolean()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apartment}}');
    }
}
