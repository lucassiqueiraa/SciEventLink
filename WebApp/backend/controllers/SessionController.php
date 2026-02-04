<?php

namespace backend\controllers;

use common\models\Event;
use common\models\Session;
use backend\models\SessionSearch;
use common\models\Venue;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * SessionController implements the CRUD actions for Session model.
 */
class SessionController extends Controller
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
     * Lists all Session models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SessionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if (!Yii::$app->user->can('admin')) {
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
     * Displays a single Session model.
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
     * Creates a new Session model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate($event_id = null)
    {
        $model = new Session();

        if ($event_id) {
            $model->event_id = $event_id;
        }

        $venueList = [];
        $eventList = [];

        if ($model->event_id) {
            $venues = Venue::find()
                ->where(['event_id' => $model->event_id])
                ->all();
            $venueList = ArrayHelper::map($venues, 'id', 'name');
        }
        else{
            $eventList = ArrayHelper::map(Event::find()->all(), 'id', 'name');
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                $returnUrl = Yii::$app->request->get('returnUrl', ['index']);
                return $this->redirect($returnUrl);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'venueList' => $venueList,
            'eventList' => $eventList,
        ]);
    }

    /**
     * Updates an existing Session model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $venueList = [];
        if ($model->event_id) {
            $venues = Venue::find()
                ->where(['event_id' => $model->event_id])
                ->all();
            $venueList = ArrayHelper::map($venues, 'id', 'name');
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            $returnUrl = Yii::$app->request->get('returnUrl', ['view', 'id' => $model->id]);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'venueList' => $venueList,
        ]);
    }

    /**
     * Deletes an existing Session model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Returns the HTML options for rooms for a given event (via AJAX)
     */
    public function actionListVenues($id)
    {

        $venues = Venue::find()
            ->where(['event_id' => $id])
            ->orderBy('name')
            ->all();

        if ($venues) {
            echo "<option value=''>Selecione uma Sala...</option>";
            foreach ($venues as $venue) {
                echo "<option value='" . $venue->id . "'>" . $venue->name . "</option>";
            }
        } else {
            echo "<option value=''>- Sem salas cadastradas -</option>";
        }
    }

    /**
     * Finds the Session model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Session the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    /**
     * Finds the Session model based on its primary key value.
     * Checks permissions: Admin OR Event Owner.
     */
    protected function findModel($id)
    {
        if (($model = Session::findOne(['id' => $id])) !== null) {
            $userId = Yii::$app->user->id;

            if (Yii::$app->user->can('admin')) {
                return $model;
            }

            if ($model->event && $model->event->created_by == $userId) {
                return $model;
            }

            throw new ForbiddenHttpException('Você não tem permissão para gerir sessões deste evento.');
        }

        throw new NotFoundHttpException('A sessão solicitada não existe.');
    }
}
