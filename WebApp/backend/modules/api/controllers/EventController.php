<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use common\models\Event;
use common\models\UserSessionFavorite; // <--- IMPORTANTE: Importar o modelo de favoritos
use yii\filters\auth\HttpBearerAuth;

class EventController extends Controller
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

    public function actionIndex()
    {
        return Event::find()
            ->select(['id', 'name', 'start_date', 'end_date', 'status', 'description'])
            ->all();
    }

    /**
     * GET /api/events/{id}
     */
    public function actionView($id)
    {
        $event = Event::findOne($id);

        if (!$event) {
            throw new \yii\web\NotFoundHttpException("Evento não encontrado.");
        }

        $userId = Yii::$app->user->id;

        $myFavoriteSessionIds = UserSessionFavorite::find()
            ->select('session_id')
            ->where(['user_id' => $userId])
            ->column();

        $sessionsData = [];
        $sessions = $event->getSessions()->orderBy(['start_time' => SORT_ASC])->all();

        foreach ($sessions as $session) {

            $isFav = in_array($session->id, $myFavoriteSessionIds);

            $sessionsData[] = [
                'id' => $session->id,
                'title' => $session->title,
                'start_time' => $session->start_time,
                'end_time' => $session->end_time,
                'location' => $session->venue ? $session->venue->name : 'Local a definir',
                'capacity' => $session->venue ? $session->venue->capacity : null,
                'is_favorite' => $isFav
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