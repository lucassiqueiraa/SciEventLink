<?php

namespace backend\controllers;

use common\models\Event;
use common\models\EventSearch;
use common\models\OrganizerEvent;
use common\models\Session;
use common\models\TicketType;
use common\models\Venue;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * EventController implements the CRUD actions for Event model.
 */
class EventController extends Controller
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
                            'roles' => ['organizer', 'admin'],
                        ],
                    ],
                ],
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
     * Lists all Event models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EventSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Event model.
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
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Event();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $transaction = Yii::$app->db->beginTransaction();

                try {
                    if (!$model->save()) {
                        throw new Exception('Erro ao salvar evento.');
                    }
                    $relation = new OrganizerEvent();
                    $relation->event_id = $model->id; // ID do evento recém-criado
                    $relation->user_id = Yii::$app->user->id; // ID de quem está logado
                    $relation->role_description = 'Criador/Dono';

                    if (!$relation->save()) {
                        throw new Exception('Erro ao associar organizador.');
                    }

                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);

                } catch (Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('admin') && $model->created_by != Yii::$app->user->id) {
            throw new ForbiddenHttpException('Você não tem permissão para editar este evento.');
        }

        $venuesDataProvider = new ActiveDataProvider([
            'query' => Venue::find()->where(['event_id' => $id]),
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        $ticketsDataProvider = new ActiveDataProvider([
            'query' => TicketType::find()->where(['event_id' => $model->id]),
            'pagination' => ['pageSize' => 5],
        ]);

        $sessionsDataProvider = new ActiveDataProvider([
            'query' => Session::find()->where(['event_id' => $model->id]),
            'sort' => ['defaultOrder' => ['start_time' => SORT_ASC]], // Ordenar por hora!
            'pagination' => ['pageSize' => 10],
        ]);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'venuesDataProvider' => $venuesDataProvider,
            'ticketsDataProvider' => $ticketsDataProvider,
            'sessionsDataProvider' => $sessionsDataProvider,
        ]);
    }

    /**
     * Deletes an existing Event model.
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
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Event the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Event::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
