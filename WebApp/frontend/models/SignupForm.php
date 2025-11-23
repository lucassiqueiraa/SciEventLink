<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserProfile;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

    //UserProfile fields
    public $name;
    public $nif;
    public $phone;



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],

            //UserProfile Rules
            ['name', 'required'],
            ['name', 'string', 'max' => 255],

            ['nif', 'string', 'max' => 9],
            ['phone', 'string', 'max' => 20],
        ];
    }

    /**
     * Signs user up.
     *
     * /**
     * @return User|null
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        // Start the transaction SQL
        $transaction = Yii::$app->db->beginTransaction();

        try {
            //Saving at User table
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->status = 10; // Ativo

            if (!$user->save()) {
                throw new \Exception("Falha ao criar utilizador.");
            }

            //Saving at UserProfile table
            $profile = new UserProfile();
            $profile->user_id = $user->id;
            $profile->name = $this->name;
            $profile->nif = $this->nif;
            $profile->phone = $this->phone;
            $profile->role = 'PART';

            if (!$profile->save()) {
                throw new \Exception("failed to create profile: " . print_r($profile->errors, true));
            }

            $transaction->commit();

            // Envio de Email (Opcional agora, pode comentar se não tiver SMTP)
            // $this->sendEmail($user);

            return $user;

        } catch (\Exception $e) {
            $transaction->rollBack();
            return null;
        }
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
