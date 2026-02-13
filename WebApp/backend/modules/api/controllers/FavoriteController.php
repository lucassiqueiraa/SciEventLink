<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use common\models\UserSessionFavorite;
use common\models\Session;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class FavoriteController extends Controller
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
     * GET /api/favorites
     * Lista apenas as favoritas (para a tela "Minha Agenda")
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $favorites = UserSessionFavorite::find()
            ->where(['user_id' => $userId])
            ->with('session')
            ->all();

        $result = [];
        foreach ($favorites as $fav) {
            if ($fav->session) {
                $result[] = [
                    'session_id' => $fav->session->id,
                    'title' => $fav->session->title,
                    'start_time' => $fav->session->start_time,
                    'location' => $fav->session->venue ? $fav->session->venue->name : 'N/A',
                    'is_favorite' => true // Redundante aqui, mas não faz mal
                ];
            }
        }
        return $result;
    }

    /**
     * POST /api/favorites
     * Body: { "session_id": 5 }
     */
    public function actionCreate()
    {
        $userId = Yii::$app->user->id;
        $sessionId = Yii::$app->request->post('session_id');

        if (!$sessionId) return ['message' => 'session_id obrigatorio.'];

        if (!Session::findOne($sessionId)) throw new NotFoundHttpException("Sessão não encontrada.");

        if (UserSessionFavorite::findOne(['user_id' => $userId, 'session_id' => $sessionId])) {
            return ['message' => 'Sessão favoritada.', 'session_id' => (int)$sessionId];
        }

        $model = new UserSessionFavorite();
        $model->user_id = $userId;
        $model->session_id = $sessionId;

        if ($model->save()) {
            return ['message' => 'Sessão favoritada.', 'session_id' => (int)$sessionId];
        }

        Yii::$app->response->statusCode = 500;
        return ['message' => 'Erro ao salvar.'];
    }

    /**
     * DELETE /api/favorites/{session_id}
     * Deleta pelo ID da SESSÃO.
     */
    public function actionDelete($id)
    {
        $userId = Yii::$app->user->id;
        $model = UserSessionFavorite::findOne(['user_id' => $userId, 'session_id' => $id]);

        if ($model) $model->delete();

        return ['message' => 'Removido dos favoritos.', 'session_id' => (int)$id];
    }
}