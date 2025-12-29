<?php

namespace frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\models\TicketType;
use common\models\Registration;

class RegistrationController extends Controller
{
    /**
     * Behaviors: Segurança e Regras HTTP
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'confirm-payment' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Cria a inscrição
     * @param int $ticket_type_id O ID do bilhete que o user quer
     */
    public function actionCreate($ticket_type_id)
    {
        // 1. Busca o Bilhete (e garante que existe)
        $ticket = TicketType::findOne($ticket_type_id);

        if (!$ticket) {
            throw new NotFoundHttpException('Bilhete não encontrado.');
        }

        // 2. Cria o Registo
        $registration = new Registration();
        $registration->user_id = Yii::$app->user->id;
        $registration->ticket_type_id = $ticket->id;
        $registration->event_id = $ticket->event_id;

        if ($registration->save()) {
            Yii::$app->session->setFlash('success', 'Inscrição realizada com sucesso! Vemo-nos lá! 🎟️');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao processar inscrição. Tente novamente.');
        }

        return $this->redirect(['event/view', 'id' => $ticket->event_id]);
    }

    /**
     * List my tickets
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Registration::find()
                ->where(['user_id' => Yii::$app->user->id]) // Só os meus
                ->with(['event', 'ticketType']) // Eager Loading para performance
                ->orderBy(['registration_date' => SORT_DESC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Payment page(Checkout)
     */
    public function actionCheckout($id)
    {
        $model = $this->findModel($id);

        if ($model->payment_status !== 'pending') {
            Yii::$app->session->setFlash('info', 'Este bilhete já está pago.');
            return $this->redirect(['index']);
        }

        return $this->render('checkout', [
            'model' => $model,
        ]);
    }

    /**
     * Processing paymeny
     */
    public function actionConfirmPayment($id)
    {
        $model = $this->findModel($id);

        // Simula processamento...

        $model->payment_status = 'paid';

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Pagamento confirmado! O seu bilhete está garantido.');
        } else {
            Yii::$app->session->setFlash('error', 'Erro ao processar pagamento.');
        }

        return $this->redirect(['index']);
    }

    public function actionTicket($id)
    {
        // Garante que o user só baixa o PRÓPRIO bilhete
        $model = Registration::find()
            ->where(['id' => $id, 'user_id' => Yii::$app->user->id])
            ->andWhere(['payment_status' => ['paid', 'confirmed']])
            ->one();

        if (!$model) {
            Yii::$app->session->setFlash('error', 'Bilhete indisponível.');
            return $this->redirect(['index']);
        }

        return $model->generateTicketPdf();
    }

    protected function findModel($id)
    {
        $model = Registration::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Registo não encontrado.');
    }


}

