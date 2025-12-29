<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\User;
use common\models\Event;
use common\models\Registration; // <--- ADICIONAR ISTO

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
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $totalRevenue = Registration::find()
            ->joinWith('ticketType')
            ->where(['payment_status' => ['paid', 'confirmed']])
            ->sum('ticket_type.price') ?? 0;

        $totalTicketsSold = Registration::find()->count();

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

        return $this->render('index', [
            'totalRevenue' => $totalRevenue,
            'totalTicketsSold' => $totalTicketsSold,
            'totalParticipants' => $totalParticipants,
            'totalOrganizers' => $totalOrganizers,
            'totalEvents' => $totalEvents,
            'suspendedUsers' => $suspendedUsers,
        ]);
    }
}