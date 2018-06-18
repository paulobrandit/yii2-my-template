<?php

use yii\db\Migration;

/**
 * Class m180618_163942_insert_user_admin
 */
class m180618_163942_insert_user_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('user', [
            'email' => 'admin@branditgroup.com',
            'auth_key' => \Yii::$app->getSecurity()->generateRandomString(),
            'password_hash' => \Yii::$app->security->generatePasswordHash('a12345', 8),
            'type' => 0,
            'created' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('user', 'email = :email', [
            ':email' => 'admin@branditgroup.com',
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180618_163942_insert_user_admin cannot be reverted.\n";

        return false;
    }
    */
}
