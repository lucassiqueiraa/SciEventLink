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
     * Lista todas as sessões favoritas do utilizador logado
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
                ];
            }
        }

        return $result;
    }

    /**
     * POST /api/favorites
     * Adiciona uma sessão aos favoritos
     * Body JSON: { "session_id": 7 }
     */
    public function actionCreate()
    {
        $userId = Yii::$app->user->id;
        $sessionId = Yii::$app->request->post('session_id');

        if (!$sessionId) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'O campo session_id é obrigatório.'];
        }

        if (!Session::findOne($sessionId)) {
            throw new NotFoundHttpException("Sessão não encontrada.");
        }

        $exists = UserSessionFavorite::findOne(['user_id' => $userId, 'session_id' => $sessionId]);
        if ($exists) {
            return ['message' => 'Esta sessão já está nos teus favoritos.'];
        }

        $model = new UserSessionFavorite();
        $model->user_id = $userId;
        $model->session_id = $sessionId;

        if ($model->save()) {
            return [
                'message' => 'Adicionado aos favoritos!',
                'id' => $model->id
            ];
        }

        Yii::$app->response->statusCode = 500;
        return ['message' => 'Erro ao salvar', 'errors' => $model->errors];
    }

    /**
     * DELETE /api/favorites/{session_id}
     * Remove uma sessão dos favoritos
     * NOTA: O ID na URL é o ID da SESSÃO, não do favorito. É mais fácil para a App.
     */
    public function actionDelete($id)
    {
        $userId = Yii::$app->user->id;
        $sessionId = $id;

        $model = UserSessionFavorite::findOne(['user_id' => $userId, 'session_id' => $sessionId]);

        if (!$model) {
            throw new NotFoundHttpException("Esta sessão não está nos teus favoritos.");
        }

        $model->delete();
        return ['message' => 'Removido dos favoritos.'];
    }
}