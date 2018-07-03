<?php

namespace app\models;

use Yii;
use yii\base\Model;

class PasswordResetRequestForm extends Model
{
    /**
     * @var string email field for password reset
     */
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return[
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => Yii::$app->user->identityClass,
                'message' => 'User with this email is not found.',
            ],
            ['email', 'exist',
                'targetClass' => Yii::$app->user->identityClass,
                'filter' => ['active' => User::STATUS_ACTIVE],
                'message' => 'Your account has been deactivated, please contact support for details.',
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        $user = User::findOne(['active' => User::STATUS_ACTIVE, 'email' => $this->email]);
        if (!empty($user)) {
            $user->generatePasswordResetToken();
            if ($user->save()) {
                return Yii::$app->mailer->compose('password_reset_token', ['user' => $user])
                    ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                    ->setTo($this->email)
                    ->setSubject('Password Reset')
                    ->send();
            }
        }
        return false;
    }
}