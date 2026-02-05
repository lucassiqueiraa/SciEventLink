<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use common\models\SessionFeedback;
use common\models\Session;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class FeedbackController extends Controller
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
     * POST /api/feedback
     * Enviar feedback para uma sessão
     * Body: { "session_id": 7, "rating": 5, "comment": "Gostei muito!" }
     */
    public function actionCreate()
    {
        $userId = Yii::$app->user->id;

        $sessionId = Yii::$app->request->post('session_id');
        $rating = Yii::$app->request->post('rating');
        $comment = Yii::$app->request->post('comment');

        if (!Session::findOne($sessionId)) {
            throw new NotFoundHttpException("Sessão não encontrada.");
        }

        $model = new SessionFeedback();
        $model->user_id = $userId;
        $model->session_id = $sessionId;
        $model->rating = $rating;
        $model->comment = $comment;

        if ($model->save()) {
            return [
                'message' => 'Feedback enviado com sucesso!',
                'data' => [
                    'id' => $model->id,
                    'rating' => $model->rating,
                    'created_at' => $model->created_at
                ]
            ];
        }

        Yii::$app->response->statusCode = 422;
        return [
            'message' => 'Erro ao guardar feedback.',
            'errors' => $model->errors
        ];
    }
}