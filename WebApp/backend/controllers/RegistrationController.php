<?php

namespace backend\controllers;

use common\models\Registration;
use backend\models\RegistrationSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class RegistrationController extends Controller
{
    public function behaviors()
    {
        return [
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
                    'mark-paid' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lista todas as inscrições.
     */
    public function actionIndex()
    {
        $searchModel = new RegistrationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Ação Rápida: Marcar como Pago
     */
    public function actionMarkPaid($id)
    {
        $model = $this->findModel($id);

        // Só permite mudar se estiver pendente
        if ($model->payment_status === 'pending') {
            $model->payment_status = 'paid';
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Inscrição marcada como PAGA com sucesso.');
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao atualizar status.');
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Garante que o Organizador só acessa o que é dele
     */
    protected function findModel($id)
    {
        $query = Registration::find()->where(['id' => $id]);

        // Se não for admin, adiciona a trava de segurança na busca
        if (!Yii::$app->user->can('admin')) {
            $query->joinWith('event')
                ->andWhere(['event.created_by' => Yii::$app->user->id]);
        }

        if (($model = $query->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('A inscrição solicitada não existe ou você não tem permissão para vê-la.');
    }
}