<?php

namespace frontend\controllers;

use Yii;
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
            // 1. Só deixa entrar quem está LOGADO
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['@'], // @ = Autenticado
                    ],
                ],
                // Se não estiver logado, o Yii redireciona para Login automaticamente
                // e depois volta para cá. Magia! ✨
            ],
            // 2. A ação CREATE tem de ser POST (Melhor prática)
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
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
}