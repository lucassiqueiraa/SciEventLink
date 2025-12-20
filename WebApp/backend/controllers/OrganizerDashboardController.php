<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\Event;
use common\models\Session; // Se existir
use common\models\Venue;   // Se existir

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
                        // Permitimos admin aqui também para debug, se quiser
                        'roles' => ['organizer', 'admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        // Lógica exclusiva do Organizador (Meus Dados)
        $myEvents = Event::find()->where(['created_by' => $userId])->count();

        // Ajuste os joins conforme seus models reais
        $mySessions = Session::find()->joinWith('event')->where(['event.created_by' => $userId])->count();
        $myVenues = Venue::find()->joinWith('event')->where(['event.created_by' => $userId])->count();

        // Renderiza views/organizer-dashboard/index.php
        return $this->render('index', [
            'myEvents' => $myEvents,
            'mySessions' => $mySessions,
            'myVenues' => $myVenues,
        ]);
    }
}