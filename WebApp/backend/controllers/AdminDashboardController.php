<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\User;
use common\models\Event;

class AdminDashboardController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'], // BLINDADO: Só admin entra aqui
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        // Lógica exclusiva do Admin (Contagem Global)

        $totalParticipants = User::find()
            ->joinWith('userProfile')
            ->where(['user.status' => 10, 'user_profile.role' => 'PART'])
            ->count();

        $totalOrganizers = User::find()
            ->joinWith('userProfile')
            ->where(['user.status' => 10, 'user_profile.role' => 'ORG'])
            ->count();

        $totalEvents = Event::find()->count();

        $suspendedUsers = User::find()->where(['!=', 'status', 10])->count();

        // Renderiza views/admin-dashboard/index.php
        return $this->render('index', [
            'totalParticipants' => $totalParticipants,
            'totalOrganizers' => $totalOrganizers,
            'totalEvents' => $totalEvents,
            'suspendedUsers' => $suspendedUsers,
        ]);
    }
}