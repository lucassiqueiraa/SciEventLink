<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use common\models\Session;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SessionController extends Controller
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
     * GET /api/sessions/{id}
     * Mostra o detalhe da sessão e os artigos (apresentações) lá dentro
     */
    public function actionView($id)
    {
        $session = Session::findOne($id);

        if (!$session) {
            throw new NotFoundHttpException("Sessão não encontrada.");
        }

        $presentations = [];

        foreach ($session->articles as $article) {

            $authorName = 'Desconhecido';

            if ($article->registration && $article->registration->user) {
                $authorName = $article->registration->user->userProfile->name;
            }

            $presentations[] = [
                'id' => $article->id,
                'title' => $article->title,
                'abstract' => $article->abstract,
                'author' => $authorName,

                // Opcional: URL completo para o PDF pra baixar na app
                // 'file_url' => \yii\helpers\Url::to('/' . $article->file_path, true),
            ];
        }

        return [
            'id' => $session->id,
            'title' => $session->title,
            'start_time' => $session->start_time,
            'end_time' => $session->end_time,
            'location' => $session->venue ? $session->venue->name : 'Local a definir',
            'presentations' => $presentations,
        ];
    }
}