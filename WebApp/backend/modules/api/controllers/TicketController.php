<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use common\models\Registration;
use yii\web\Response;

class TicketController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }

    /**
     * GET /api/my-tickets
     */
    public function actionMyTickets()
    {
        $userId = Yii::$app->user->id;

        $registrations = Registration::find()
            ->where(['user_id' => $userId])
            ->andWhere(['payment_status' => 'paid'])
            ->with(['event'])
            ->orderBy(['registration_date' => SORT_DESC])
            ->all();

        $data = [];
        foreach ($registrations as $reg) {
            if ($reg->event) {
                $isUsed = $reg->checkin_at !== null;

                $data[] = [
                    'id' => $reg->id,
                    'event_name' => $reg->event->name,
                    'event_date' => $reg->event->start_date,

                    'location' => $reg->event->location ?? 'Online/Não definido',
                    'qr_data' => (string)$reg->id,

                    'status' => $isUsed ? 'used' : 'valid',

                    'ticket_type' => $reg->ticketType ? $reg->ticketType->name : 'Geral',
                    'price' => $reg->ticketType ? $reg->ticketType->price : '0.00',
                ];
            }
        }

        return $data;
    }
}