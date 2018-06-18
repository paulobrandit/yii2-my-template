<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m180618_141952_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'email' => $this->string(255)->unique()->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' =>  $this->string(255)->unique(),
            'active' => $this->tinyInteger()->notNull()->defaultValue(1),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
    }
}
