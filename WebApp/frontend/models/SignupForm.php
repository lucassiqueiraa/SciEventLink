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

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // 1. Criar User
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();

            // User::STATUS_ACTIVE (10) -> Loga direto
            $user->status = User::STATUS_ACTIVE;

            if (!$user->save()) {
                throw new \Exception("Erro ao salvar User: " . implode(", ", $user->getErrorSummary(true)));
            }

            // 2. Criar Profilem
            $profile = new UserProfile();
            $profile->user_id = $user->id;
            $profile->name = $this->name;
            $profile->nif = $this->nif;
            $profile->phone = $this->phone;

            // para relatórios rápidos, mantenha.
            $profile->role = 'PART';

            if (!$profile->save()) {
                throw new \Exception("Erro ao salvar Profile: " . implode(", ", $profile->getErrorSummary(true)));
            }

            $auth = \Yii::$app->authManager;
            $authorRole = $auth->getRole('participant');

            if ($authorRole) {
                $auth->assign($authorRole, $user->id);
            } else {
                throw new \Exception("Erro crítico: Papel 'participant' não encontrado no sistema.");
            }

            $transaction->commit();

            // $this->sendEmail($user);

            return $user;

        } catch (\Exception $e) {
            $transaction->rollBack();

            // para ver no runtime/logs/app.log
            Yii::error("Signup Falhou: " . $e->getMessage(), 'signup');

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
