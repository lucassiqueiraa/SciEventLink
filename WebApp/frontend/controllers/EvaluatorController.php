<?php

namespace frontend\controllers;

use common\models\Event;
use Yii;
use common\models\Article;
use common\models\Evaluation;
use common\models\EventEvaluators;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;

class EvaluatorController extends Controller
{
    /**
     * Só avaliadores logados podem entrar aqui
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all articles that this user can rate
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        $eventIds = EventEvaluators::find()
            ->select('event_id')
            ->where(['user_id' => $userId])
            ->column();

        if (empty($eventIds)) {
            Yii::$app->session->setFlash('warning', 'Não estás atribuído como avaliador em nenhum evento.');
        }

        $articles = Article::find()
            ->joinWith(['registration.event', 'registration.user']) // Carrega logo o Evento e o User
            ->where(['IN', 'registration.event_id', $eventIds])
            ->andWhere(['IN', 'article.status', ['submitted', 'in_review']])
            ->all();

        return $this->render('index', [
            'articles' => $articles,
        ]);
    }

    /**
     * Page to view the PDF and give feedback
     */
    public function actionEvaluate($id)
    {
        $article = Article::findOne($id);

        if (!$article) {
            throw new NotFoundHttpException('Artigo não encontrado.');
        }
        $event = $article->registration->event;

        if (!$event->isEvaluator(Yii::$app->user->id)) {
            throw new ForbiddenHttpException('Não tens permissão para avaliar artigos deste evento.');
        }

        $model = Evaluation::findOne(['article_id' => $article->id, 'evaluator_id' => Yii::$app->user->id]);

        if (!$model) {
            $model = new Evaluation();
            $model->article_id = $article->id;
            $model->evaluator_id = Yii::$app->user->id;
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {

                if ($article->status === 'submitted') {
                    $article->status = 'in_review';
                    $article->save(false);
                }

                Yii::$app->session->setFlash('success', 'Avaliação submetida com sucesso!');
                return $this->redirect(['index']);
            }
        }

        return $this->render('evaluate', [
            'model' => $model,
            'article' => $article,
        ]);
    }
}