<?php

namespace frontend\controllers;

use common\models\Event;
use common\models\Registration;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class EventController extends Controller
{
    public function actionView($id)
    {
        $model = Event::find()
            ->where(['id' => $id])
            ->andWhere(['status' => ['open', 'running']])
            ->with(['sessions', 'ticketTypes'])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('O evento solicitado não foi encontrado ou não está disponível.');
        }

        $userRegistration = null;
        if (!Yii::$app->user->isGuest) {
            $userRegistration = Registration::findOne([
                'event_id' => $id,
                'user_id' => Yii::$app->user->id,
            ]);
        }

        return $this->render('view', [
            'model' => $model,
            'userRegistration' => $userRegistration,
        ]);
    }
}