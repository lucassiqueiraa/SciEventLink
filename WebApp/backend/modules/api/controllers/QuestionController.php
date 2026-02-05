<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use common\models\SessionQuestion;
use common\models\Session;

class QuestionController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = \yii\web\Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }

    /**
     * GET /api/questions?session_id=7
     * Lista APENAS as perguntas APROVADAS de uma sessão
     */
    public function actionIndex()
    {
        $sessionId = Yii::$app->request->get('session_id');

        if (!$sessionId) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'O parametro session_id é obrigatório.'];
        }

        // Busca perguntas ordenadas
        $questions = SessionQuestion::find()
            ->where(['session_id' => $sessionId])
            ->andWhere(['status' => SessionQuestion::STATUS_APPROVED]) // <--- SÓ MOSTRA APROVADAS
            ->orderBy(['created_at' => SORT_DESC])
            ->with('user')
            ->all();

        $data = [];
        foreach ($questions as $q) {
            $data[] = [
                'id' => $q->id,
                'question_text' => $q->question_text, // <-- Nome correto da coluna
                'created_at' => $q->created_at,
                'user_name' => $q->user ? $q->user->username : 'Anónimo',
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
        $text = Yii::$app->request->post('question_text'); // <-- Nome correto da coluna

        if (!$sessionId || !$text) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Dados incompletos (session_id e question_text são obrigatórios).'];
        }

        if (!Session::findOne($sessionId)) {
            throw new \yii\web\NotFoundHttpException("Sessão não encontrada.");
        }

        $model = new SessionQuestion();
        $model->user_id = $userId;
        $model->session_id = $sessionId;
        $model->question_text = $text;
        // O status já fica 'pending' automaticamente pelas regras do teu model

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