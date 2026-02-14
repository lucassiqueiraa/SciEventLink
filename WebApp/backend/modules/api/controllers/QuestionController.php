<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use common\models\SessionQuestion;
use common\models\Session;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class QuestionController extends Controller
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
     * GET /api/questions?session_id=7
     * Lista APENAS as perguntas APROVADAS de uma sessão
     */
    public function actionIndex($session_id)
    {
        if (!$session_id) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'O parametro session_id é obrigatório.'];
        }

        $questions = SessionQuestion::find()
            ->where(['session_id' => $session_id])
            ->andWhere(['status' => SessionQuestion::STATUS_APPROVED])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('user')
            ->all();

        $data = [];
        foreach ($questions as $question) {
            $data[] = [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'created_at' => $question->created_at,
                'user_name' => $question->user ? $question->user->username : 'Anónimo',
            ];
        }

        return $data;
    }


    /**
     * POST /api/questions
     * Enviar uma nova pergunta (Fica PENDING por defeito)
     * Body: { "session_id": 7, "question_text": "Dúvida sobre XYZ..." }
     */
    public function actionCreate()
    {
        $userId = Yii::$app->user->id;
        $sessionId = Yii::$app->request->post('session_id');
        $text = Yii::$app->request->post('question_text');

        if (!$sessionId || !$text) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Dados incompletos (session_id e question_text são obrigatórios).'];
        }

        if (!Session::findOne($sessionId)) {
            throw new NotFoundHttpException("Sessão não encontrada.");
        }

        $model = new SessionQuestion();
        $model->user_id = $userId;
        $model->session_id = $sessionId;
        $model->question_text = $text;

        if ($model->save()) {
            return [
                'message' => 'Pergunta enviada! Aguarda aprovação do moderador.',
                'data' => [
                    'id' => $model->id,
                    'question_text' => $model->question_text,
                    'status' => $model->status,
                    'created_at' => $model->created_at,
                ]
            ];
        }

        Yii::$app->response->statusCode = 422;
        return ['message' => 'Erro ao salvar', 'errors' => $model->errors];
    }
}