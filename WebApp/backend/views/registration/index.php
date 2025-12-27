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
                // Removemos bordas duplas e adicionamos hover suave
                'tableOptions' => ['class' => 'table table-hover table-striped align-middle mb-0', 'id' => 'dataTable', 'width' => '100%'],
                'layout' => "{items}\n<div class='p-3 d-flex justify-content-between align-items-center'>{summary}{pager}</div>",
                'columns' => [

                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:60px; text-align:center;'],
                        'contentOptions' => ['class' => 'text-center text-muted fw-bold'],
                    ],

                    [
                        'attribute' => 'user_email',
                        'label' => 'Participante',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $user = $model->user;
                            return '<div class="d-flex align-items-center">' .
                                '<div class="bg-light rounded-circle p-2 me-2 text-primary d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">' .
                                '<i class="fas fa-user"></i>' .
                                '</div>' .
                                '<div>' .
                                '<div class="fw-bold text-dark">' . Html::encode($user->username) . '</div>' .
                                '<div class="small text-muted">' . Html::encode($user->email) . '</div>' .
                                '</div>' .
                                '</div>';
                        },
                    ],

                    // Evento
                    [
                        'attribute' => 'event_name',
                        'label' => 'Evento',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<span class="fw-bold text-dark"><i class="fas fa-calendar-day text-muted me-1"></i>' . Html::encode($model->event->name) . '</span>';
                        },
                    ],

                    // Bilhete e Valor (Destaque no Dinheiro)
                    [
                        'label' => 'Bilhete / Valor',
                        'format' => 'raw',
                        'contentOptions' => ['style' => 'min-width: 150px;'],
                        'value' => function ($model) {
                            return '<div class="d-flex flex-column">' .
                                '<span class="badge bg-light text-dark border mb-1" style="width: fit-content;">' . Html::encode($model->ticketType->name) . '</span>' .
                                '<span class="fw-bold text-success">' . Yii::$app->formatter->asCurrency($model->ticketType->price, 'EUR') . '</span>' .
                                '</div>';
                        },
                    ],

                    [
                        'attribute' => 'registration_date',
                        'label' => 'Data',
                        'format' => ['date', 'php:d/m/Y H:i'],
                        'filter' => false,
                        'contentOptions' => ['class' => 'small text-muted'],
                    ],

                    [
                        'attribute' => 'payment_status',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'filter' => [
                            'pending' => 'Pendente',
                            'paid' => 'Pago',
                            'confirmed' => 'Confirmado',
                            'canceled' => 'Cancelado'
                        ],
                        'value' => function ($model) {
                            // Mapeamento de Status -> Design
                            $statusMap = [
                                'paid' => ['class' => 'bg-success text-white', 'icon' => 'check-circle', 'label' => 'Pago'],
                                'confirmed' => ['class' => 'bg-success text-white', 'icon' => 'check-circle', 'label' => 'Confirmado'],
                                'pending' => ['class' => 'bg-warning text-dark', 'icon' => 'clock', 'label' => 'Pendente'], // text-dark garante leitura
                                'canceled' => ['class' => 'bg-danger text-white', 'icon' => 'times-circle', 'label' => 'Cancelado'],
                            ];

                            $status = $model->payment_status;
                            // Fallback caso status não esteja no mapa
                            $config = $statusMap[$status] ?? ['class' => 'bg-secondary text-white', 'icon' => 'question', 'label' => $status];

                            return '<span class="badge ' . $config['class'] . ' p-2 rounded-pill shadow-sm" style="min-width: 100px;">' .
                                '<i class="fas fa-' . $config['icon'] . ' me-1"></i> ' . $config['label'] .
                                '</span>';
                        },
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{action}',
                        'header' => 'Ações',
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 160px;'],
                        'buttons' => [
                            'action' => function ($url, $model, $key) {
                                // Se PENDENTE -> Botão de Validar
                                if ($model->payment_status === 'pending') {
                                    return Html::a('<i class="fas fa-check"></i> Validar', ['mark-paid', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-outline-success w-100 fw-bold',
                                        'title' => 'Confirmar Pagamento Manualmente',
                                        'data' => [
                                            'confirm' => 'Confirmar recebimento de pagamento para este bilhete?',
                                            'method' => 'post',
                                        ],
                                    ]);
                                }

                                return '<button class="btn btn-sm btn-light w-100 text-muted border" disabled><i class="fas fa-lock"></i> Concluído</button>';
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>