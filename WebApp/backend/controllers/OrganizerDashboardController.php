<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\Event;
use common\models\Session;
use common\models\Venue;
use common\models\Registration;

class OrganizerDashboardController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['organizer', 'admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        $myRevenue = Registration::find()
            ->alias('r')
            ->joinWith(['ticketType t', 'event e'])
            ->where(['r.payment_status' => ['paid', 'confirmed']])
            ->andWhere(['e.created_by' => $userId]) // Só meus eventos
            ->sum('t.price') ?? 0;

        $myAttendees = Registration::find()
            ->joinWith('event e')
            ->where(['e.created_by' => $userId])
            ->count();

        $myEvents = Event::find()->where(['created_by' => $userId])->count();
        $mySessions = Session::find()->joinWith('event')->where(['event.created_by' => $userId])->count();
        $myVenues = Venue::find()->joinWith('event')->where(['event.created_by' => $userId])->count();

        return $this->render('index', [
            'myRevenue' => $myRevenue,
            'myAttendees' => $myAttendees,
            'myEvents' => $myEvents,
            'mySessions' => $mySessions,
            'myVenues' => $myVenues,
        ]);
    }
}