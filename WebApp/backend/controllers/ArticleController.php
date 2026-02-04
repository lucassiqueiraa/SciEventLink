<?php

namespace backend\controllers;

use common\models\Session;
use Yii;
use common\models\Article;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

class ArticleController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['admin', 'organizer'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'set-status' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Action Index (Lista Simples)
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        $myEventIds = \common\models\OrganizerEvent::find()
        ->select('event_id')
            ->where(['user_id' => $userId])
            ->column();

        $query = Article::find();

        $query->joinWith(['registration']);

        if (!Yii::$app->user->can('admin')) {
            $query->where(['IN', 'registration.event_id', $myEventIds]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Action View
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $sessions = Session::find()
            ->where(['event_id' => $model->registration->event_id])
            ->orderBy(['start_time' => SORT_ASC])
            ->all();

        $sessionList = ArrayHelper::map($sessions, 'id',
            function($session) {
                $start = date('H:i', strtotime($session->start_time));
                $end   = date('H:i', strtotime($session->end_time));
                return "$start - $end | {$session->title}";
            },
            function($session) {
                return date('d/m/Y', strtotime($session->start_time));
            }
        );

        return $this->render('view', [
            'model' => $model,
            'sessionList' => $sessionList,
        ]);
    }

    /**
     * Action Set Status
     */
    public function actionSetStatus($id, $status)
    {
        $model = $this->findModel($id);

        $validStatuses = ['accepted', 'rejected', 'in_review'];

        if (in_array($status, $validStatuses)) {
            $model->status = $status;
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Artigo marcado como: ' . strtoupper($status));
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao gravar.');
            }
        }

        return $this->redirect(['view', 'id' => $id]);
    }


    /**
     * Atribui uma Sessão ao Artigo
     */
    public function actionAssignSession($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $sessionId = $this->request->post('session_id');

            if ($model->status === 'accepted') {
                $model->session_id = $sessionId;
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Artigo agendado com sucesso!');
                } else {
                    Yii::$app->session->setFlash('error', 'Erro ao agendar.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Só pode agendar artigos aceites.');
            }
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Article::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('O artigo solicitado não existe.');
    }
}