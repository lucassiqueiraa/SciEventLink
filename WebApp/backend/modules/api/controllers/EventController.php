<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use common\models\Event;
use yii\filters\auth\HttpBearerAuth; // <--- Importante para bloquear acesso sem token

class EventController extends Controller
{
    /**
     * Configurações de Comportamento (Behaviors)
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Define que a resposta é sempre JSON
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        // Adiciona Autenticação por Token (Bearer Token)
        // Se a App não enviar o token no header, dá erro 401
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        return $behaviors;
    }

    /**
     * GET /api/events
     * Lista todos os eventos (ou apenas os abertos/running)
     */
    public function actionIndex()
    {
        // Podes filtrar apenas eventos "open" ou "running" se quiseres
        $events = Event::find()
            ->select(['id', 'name', 'start_date', 'end_date', 'status', 'description'])
            ->all();

        return $events;
    }

    /**
     * GET /api/events/{id}
     * Detalhes de um evento + Sessões + Salas
     */
    public function actionView($id)
    {
        $event = Event::findOne($id);

        if (!$event) {
            throw new \yii\web\NotFoundHttpException("Evento não encontrado.");
        }
        $sessionsData = [];

        $sessions = $event->getSessions()->orderBy(['start_time' => SORT_ASC])->all();

        foreach ($sessions as $session) {
            $sessionsData[] = [
                'id' => $session->id,
                'title' => $session->title,
                'start_time' => $session->start_time,
                'end_time' => $session->end_time,

                'location' => $session->venue ? $session->venue->name : 'Local a definir',

                'capacity' => $session->venue ? $session->venue->capacity : null,
            ];
        }

        return [
            'id' => $event->id,
            'name' => $event->name,
            'description' => $event->description,
            'start_date' => $event->start_date,
            'end_date' => $event->end_date,
            'status' => $event->status,
            'sessions' => $sessionsData,
        ];
    }
}