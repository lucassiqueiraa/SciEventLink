<?php

use common\models\Registration;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var backend\models\RegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Gestão de Inscrições';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="registration-index card shadow mb-4 border-0">
    <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between border-bottom">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list-alt me-2"></i> Lista de Vendas / Inscritos
        </h6>
        <span class="badge bg-primary text-white p-2">
            Total: <?= $dataProvider->getTotalCount() ?>
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover table-striped align-middle mb-0', 'id' => 'dataTable', 'width' => '100%'],
                    'layout' => "{items}\n<div class='p-3 d-flex justify-content-between align-items-center'>{summary}{pager}</div>",
                    'columns' => [

                        // Id
                            [
                                    'attribute' => 'id',
                                    'headerOptions' => ['style' => 'width: 50px; text-align:center;'],
                                    'contentOptions' => ['class' => 'text-center text-muted fw-bold'],
                            ],

                        // participant
                            [
                                    'attribute' => 'user_email',
                                    'label' => 'Participante',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $user = $model->user;
                                        return '<div class="d-flex align-items-center">' .
                                                '<div class="bg-light rounded-circle p-1 me-2 text-primary d-flex justify-content-center align-items-center" style="width: 30px; height: 30px;">' .
                                                '<i class="fas fa-user small"></i>' .
                                                '</div>' .
                                                '<div style="line-height: 1.1;">' .
                                                '<div class="fw-bold text-dark text-truncate" style="max-width: 180px;">' . Html::encode($user->username) . '</div>' .
                                                '<div class="small text-muted text-truncate" style="max-width: 180px;">' . Html::encode($user->email) . '</div>' .
                                                '</div>' .
                                                '</div>';
                                    },
                            ],

                        // Event
                            [
                                    'attribute' => 'event_name',
                                    'label' => 'Evento',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        return '<div class="text-truncate" style="max-width: 180px; font-weight: 500;">' .
                                                '<i class="fas fa-calendar-day text-muted me-1 small"></i>' . Html::encode($model->event->name) .
                                                '</div>';
                                    },
                            ],

                        // Ticket
                            [
                                    'label' => 'Bilhete',
                                    'format' => 'raw',
                                    'headerOptions' => ['style' => 'width: 130px;'],
                                    'value' => function ($model) {
                                        return '<div class="d-flex flex-column align-items-start">' .
                                                '<span class="badge bg-light text-dark border mb-1">' . Html::encode($model->ticketType->name) . '</span>' .
                                                '<span class="fw-bold text-success small">' . Yii::$app->formatter->asCurrency($model->ticketType->price, 'EUR') . '</span>' .
                                                '</div>';
                                    },
                            ],

                        // Data
                            [
                                    'attribute' => 'registration_date',
                                    'label' => 'Data',
                                    'format' => ['date', 'php:d/m/y'],
                                    'filter' => false,
                                    'headerOptions' => ['style' => 'width: 80px; text-align:center;'],
                                    'contentOptions' => ['class' => 'small text-muted text-center'],
                            ],

                        // Payment status
                            [
                                    'attribute' => 'payment_status',
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 100px;'],
                                    'contentOptions' => ['class' => 'text-center'],
                                    'filter' => [
                                            'pending' => 'Pendente',
                                            'paid' => 'Pago',
                                            'confirmed' => 'Confirmado',
                                            'canceled' => 'Cancelado'
                                    ],
                                    'value' => function ($model) {
                                        $statusMap = [
                                                'paid' => ['class' => 'bg-success text-white', 'label' => 'Pago'],
                                                'confirmed' => ['class' => 'bg-success text-white', 'label' => 'Pago'],
                                                'pending' => ['class' => 'bg-warning text-dark', 'label' => 'Pend.'],
                                                'canceled' => ['class' => 'bg-danger text-white', 'label' => 'Canc.'],
                                        ];
                                        $status = $model->payment_status;
                                        $config = $statusMap[$status] ?? ['class' => 'bg-secondary', 'label' => $status];

                                        return '<span class="badge ' . $config['class'] . ' rounded-pill small">' . $config['label'] . '</span>';
                                    },
                            ],

                        // CHECK-IN MANUAL
                            [
                                    'label' => 'Acesso',
                                    'format' => 'raw',
                                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 120px;'],
                                    'contentOptions' => ['class' => 'text-center'],
                                    'value' => function ($model) {
                                        if ($model->checkin_at) {

                                            return '<div class="d-flex flex-column align-items-center">' .
                                                    '<span class="badge bg-success mb-1"><i class="fas fa-check-circle"></i> Validado</span>' .
                                                    '<small class="text-muted" style="font-size: 0.75rem;">' .
                                                    Yii::$app->formatter->asTime($model->checkin_at, 'short') .
                                                    '</small>' .
                                                    '</div>';
                                        }

                                        if ($model->payment_status !== 'paid' && $model->payment_status !== 'confirmed') {
                                            return '<span class="text-muted small"><i class="fas fa-ban"></i> Aguarda Pagamento</span>';
                                        }

                                        return Html::a('<i class="fas fa-sign-in-alt"></i> Entrou', ['checkin', 'id' => $model->id], [
                                                'class' => 'btn btn-sm btn-outline-primary shadow-sm',
                                                'title' => 'Marcar entrada manualmente',
                                                'data' => [
                                                        'confirm' => 'Confirmar a entrada deste participante?',
                                                        'method' => 'post',
                                                ],
                                        ]);
                                    },
                            ],

                        // Actions
                            [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{mark-paid} {ticket}',
                                    'header' => 'Ações',
                                    'contentOptions' => ['class' => 'text-center text-nowrap', 'style' => 'width: 160px;'],
                                    'buttons' => [
                                            'mark-paid' => function ($url, $model, $key) {
                                                if ($model->payment_status === 'pending') {
                                                    return Html::a('<i class="fas fa-check"></i>', ['mark-paid', 'id' => $model->id], [
                                                            'class' => 'btn btn-sm btn-outline-success me-1',
                                                            'title' => 'Validar Pagamento',
                                                            'data' => [
                                                                    'confirm' => 'Confirmar pagamento?',
                                                                    'method' => 'post',
                                                            ],
                                                    ]);
                                                }
                                                return '';
                                            },
                                            'ticket' => function ($url, $model) {
                                                return Html::a('<i class="fas fa-print"></i> PDF', ['ticket', 'id' => $model->id], [
                                                        'class' => 'btn btn-sm btn-danger',
                                                        'target' => '_blank',
                                                        'data-pjax' => '0',
                                                        'title' => 'Imprimir Bilhete'
                                                ]);
                                            },
                                    ],
                            ],
                    ],
            ]); ?>
        </div>
    </div>
</div>