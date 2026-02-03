<?php

namespace backend\controllers;

use Yii;
use common\models\Article;
use yii\filters\AccessControl;
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
        return $this->render('view', [
            'model' => $this->findModel($id),
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