<?php

namespace backend\controllers;

use common\models\EventEvaluators;
use Yii;
use common\models\Event;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl; // Para controlar login
use yii\data\ActiveDataProvider;
use yii\web\Response;

/**
 * EventEvaluatorController implements the CRUD actions for EventEvaluator model.
 */
class EventEvaluatorController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'add' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all EventEvaluator models for a specific event.
     */
    public function actionIndex($event_id)
    {
        $event = $this->findEvent($event_id);

        $this->checkAccess($event);

        $evaluatorsProvider = new ActiveDataProvider([
            'query' => EventEvaluators::find()->where(['event_id' => $event_id]),
            'pagination' => ['pageSize' => 20],
        ]);

        $currentEvaluatorsIds = EventEvaluators::find()
            ->select('user_id')
            ->where(['event_id' => $event_id]);

        $candidatesProvider = new ActiveDataProvider([
            'query' => User::find()
                ->where(['status' => 10])
                ->andWhere(['NOT IN', 'id', $currentEvaluatorsIds]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'event' => $event,
            'evaluatorsProvider' => $evaluatorsProvider,
            'candidatesProvider' => $candidatesProvider,
        ]);
    }

    /**
     * Add Evaluator
     */
    public function actionAdd($event_id, $user_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $event = $this->findEvent($event_id);
            $this->checkAccess($event);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        $user = User::findOne($user_id);
        if (!$user) {
            return ['success' => false, 'message' => 'Utilizador não encontrado.'];
        }

        $exists = EventEvaluators::find()
            ->where(['event_id' => $event_id, 'user_id' => $user_id])
            ->exists();

        if ($exists) {
            return ['success' => false, 'message' => 'Este utilizador já é avaliador.'];
        }

        $model = new EventEvaluators();
        $model->event_id = $event_id;
        $model->user_id = $user_id;

        if ($model->save()) {
            return ['success' => true, 'message' => 'Avaliador adicionado com sucesso!'];
        }

        return ['success' => false, 'message' => 'Erro ao salvar. Tente novamente.'];
    }

    /**
     * Remove Evaluator
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = EventEvaluators::findOne($id);

        if (!$model) {
            return ['success' => false, 'message' => 'Registo não encontrado.'];
        }

        try {
            $event = $this->findEvent($model->event_id);
            $this->checkAccess($event);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Sem permissão para remover este avaliador.'];
        }

        if ($model->delete()) {
            return ['success' => true, 'message' => 'Avaliador removido com sucesso!'];
        }

        return ['success' => false, 'message' => 'Erro ao remover.'];
    }

    /**
     * Helper to find the event
     */
    protected function findEvent($id)
    {
        if (($model = Event::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('O evento solicitado não existe.');
    }

    /**
     * Security Helper: verify that the user has permission to modify this event
     */
    protected function checkAccess($event)
    {
        if (Yii::$app->user->can('admin')) {
            return true;
        }

        if ($event->created_by !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('Você não tem permissão para gerir avaliadores neste evento.');
        }

        return true;
    }
}