<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property int $active
 * @property int $type
 * @property string $created
 */
class User extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const TYPE_ADMIN = 0;
    const TYPE_USER = 1;

    public static function getTypeOptions()
    {
        return [
            self::TYPE_ADMIN => 'Administrador',
            self::TYPE_USER => 'Utilizador',
        ];
    }

    public $password;
    public $password_repeat;

    public function getTypeDesc()
    {
        $options = self::getTypeOptions();
        return $options[$this->type];
    }

    public function getIsActive()
    {
        return $this->active == self::STATUS_ACTIVE;
    }

    public function getIsAdmin()
    {
        return $this->type == self::TYPE_ADMIN;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['active', 'type'], 'integer'],
            [['created'], 'safe'],
            [['email', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['password', 'password_repeat'], 'string', 'length'=>[6,30]],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password'],
            [['active'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            [['active'], 'default', 'value' => self::STATUS_ACTIVE],
            [['type'], 'in', 'range' => [self::TYPE_ADMIN, self::TYPE_USER]],
            [['type'], 'default', 'value' => self::TYPE_USER],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'active' => 'Active',
            'type' => 'Type',
            'created' => 'Created',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['email' => $username]);
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function getUsername()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password, 8);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created = new \yii\db\Expression('NOW()');
                $this->auth_key = \Yii::$app->getSecurity()->generateRandomString();
            }
            if (!empty($this->password) && !empty($this->password_repeat)) {
                $this->setPassword($this->password);
            }
            return true;
        }
        return false;
    }
}
