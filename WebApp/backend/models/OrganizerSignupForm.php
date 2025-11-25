<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserProfile;

/**
 * Organizer Signup form (Used by Admin)
 */
class OrganizerSignupForm extends Model
{
    public $username;
    public $email;
    public $password;

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
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Este username já está em uso.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Este email já está em uso.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength'] ?? 6],

            ['name', 'required'],
            ['name', 'string', 'max' => 255],

            ['nif', 'string', 'max' => 9],
            ['phone', 'string', 'max' => 20],
        ];
    }

    /**
     * Signs organizer up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->status = 10; // Active

            if (!$user->save()) {
                throw new \Exception("Erro ao salvar User.");
            }

            $profile = new UserProfile();
            $profile->user_id = $user->id;
            $profile->name = $this->name;
            $profile->nif = $this->nif;
            $profile->phone = $this->phone;
            $profile->role = 'ORG';

            if (!$profile->save()) {
                throw new \Exception("Erro ao salvar Perfil.");
            }

            $transaction->commit();
            return $user;

        } catch (\Exception $e) {
            $transaction->rollBack();
            return null;
        }
    }
}