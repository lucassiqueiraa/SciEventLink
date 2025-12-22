<?php

namespace frontend\controllers;

use common\models\Event;
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

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}