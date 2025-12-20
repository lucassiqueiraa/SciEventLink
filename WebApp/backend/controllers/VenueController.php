<?php

namespace backend\controllers;

use common\models\Venue;
use backend\models\VenueSearch;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * VenueController implements the CRUD actions for Venue model.
 */
class VenueController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Venue models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new VenueSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if(!Yii::$app->user->can('admin')){
            $dataProvider->query
                ->joinWith('event')
                ->andWhere(['event.created_by' => Yii::$app->user->id]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Venue model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Venue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate($event_id = null)
    {
        $model = new Venue();

        if ($event_id) {
            $model->event_id = $event_id;
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                $returnUrl = Yii::$app->request->get('returnUrl', ['index', 'VenueSearch[event_id]' => $model->event_id]);
                return $this->redirect($returnUrl);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Venue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            $returnUrl = Yii::$app->request->get('returnUrl', ['view', 'id' => $model->id]);
            return $this->redirect($returnUrl);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Venue model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $eventId = $model->event_id;

        $model->delete();

        $returnUrl = Yii::$app->request->get('returnUrl', ['index', 'VenueSearch[event_id]' => $eventId]);
        return $this->redirect($returnUrl);
    }

    /**
     * Finds the Venue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Venue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Venue::findOne(['id' => $id])) !== null) {
            if (!Yii::$app->user->can('admin') && $model->created_by !== Yii::$app->user->id) {
                throw new ForbiddenHttpException('Você não tem permissão para aceder a este evento.');
            }
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
