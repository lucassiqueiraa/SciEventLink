<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use common\models\User;

/**
 * Controlador responsável pela Autenticação da App
 */
class AuthController extends Controller
{
    /**
     * Login via API
     * URL: POST /api/login
     */
    public function actionLogin()
    {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');

        $user = User::findByUsername($username);

        if ($user && $user->validatePassword($password)) {

            return [
                'token' => $user->auth_key,
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => Yii::$app->authManager->getRolesByUser($user->id) ? array_keys(Yii::$app->authManager->getRolesByUser($user->id))[0] : 'participant'
            ];
        }
        Yii::$app->response->statusCode = 401;
        return ['error' => 'Username ou password incorretos.'];
    }
}