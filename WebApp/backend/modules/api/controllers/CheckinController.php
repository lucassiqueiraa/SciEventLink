<?php

namespace backend\modules\api\controllers;

use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\web\Response;
use common\models\Registration;

class CheckinController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['validate'],
                    'roles' => ['?', '@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionValidate($code)
    {
        //TODO: Trocar de ID para qr_hash
        $ticket = Registration::findOne($code);

        if (!$ticket) {
            return [
                'success' => false,
                'message' => 'Bilhete não encontrado (ID inválido).'
            ];
        }

        if ($ticket->payment_status !== 'paid') {
            return [
                'success' => false,
                'message' => 'Bilhete não está pago!'
            ];
        }

        if ($ticket->checkin_at !== null) {
            return [
                'success' => false,
                'message' => 'Este bilhete JÁ FOI UTILIZADO!',
                'checkin_time' => $ticket->checkin_at,
                'participant' => $ticket->user->username ?? 'Desconhecido'
            ];
        }

        $ticket->checkin_at = date('Y-m-d H:i:s');

        // Se tivermos um staff logado, guarda o ID dele (opcional)
        // if (!Yii::$app->user->isGuest) {
        //    $ticket->checkin_by = Yii::$app->user->id;
        // }

        if ($ticket->save()) {
            return [
                'success' => true,
                'message' => 'Entrada Validada! Bem-vindo.',
                'participant' => $ticket->user->username ?? 'Participante',
                'event' => $ticket->event->name
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erro ao registar entrada na base de dados.',
                'errors' => $ticket->errors
            ];
        }
    }
}