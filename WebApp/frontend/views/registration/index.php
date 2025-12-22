<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Os Meus Bilhetes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="registration-index container py-4">

    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold text-dark">
                <i class="fas fa-ticket-alt text-primary"></i> Meus Bilhetes
            </h1>
            <p class="text-muted lead">Gerencie as suas inscrições e aceda aos seus eventos.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="<?= Url::to(['site/index']) ?>" class="btn btn-outline-primary rounded-pill">
                <i class="fas fa-plus-circle"></i> Procurar Novos Eventos
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 text-primary font-weight-bold">Histórico de Inscrições</h5>
        </div>

        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false, // Remove o texto "Mostrando 1-10 de..." para ficar mais limpo
                'tableOptions' => ['class' => 'table table-hover table-striped mb-0 align-middle'],
                'emptyText' => '<div class="text-center py-5"><i class="far fa-sad-tear fa-3x text-muted mb-3"></i><h4 class="text-muted">Você ainda não tem bilhetes.</h4><a href="'.Url::to(['site/index']).'" class="btn btn-primary mt-2">Ver Eventos Disponíveis</a></div>',
                'columns' => [
                    // Coluna Serial (#) - Opcional, pode remover se quiser
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 50px;'],
                        'contentOptions' => ['class' => 'text-center text-muted fw-bold'],
                    ],

                    // Evento (Com destaque)
                    [
                        'attribute' => 'event_id',
                        'label' => 'Evento',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<div class="fw-bold text-dark" style="font-size: 1.1rem;">' . Html::encode($model->event->name) . '</div>' .
                                '<small class="text-muted"><i class="far fa-calendar"></i> ' . Yii::$app->formatter->asDate($model->event->start_date, 'short') . '</small>';
                        },
                    ],

                    // Tipo de Bilhete e Preço
                    [
                        'attribute' => 'ticket_type_id',
                        'label' => 'Bilhete',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<span class="badge bg-light text-dark border">' . Html::encode($model->ticketType->name) . '</span> <br>' .
                                '<small class="text-success fw-bold">' . Yii::$app->formatter->asCurrency($model->ticketType->price, 'EUR') . '</small>';
                        },
                        'contentOptions' => ['style' => 'width: 20%;'],
                    ],

                    // Data da Inscrição
                    [
                        'attribute' => 'registration_date',
                        'label' => 'Data Compra',
                        'format' => ['date', 'php:d/m/Y'],
                        'contentOptions' => ['class' => 'text-muted'],
                    ],

                    // Status (Com Pills Coloridos Modernos)
                    [
                        'attribute' => 'payment_status',
                        'label' => 'Estado',
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 150px;'],
                        'headerOptions' => ['class' => 'text-center'],
                        'value' => function ($model) {
                            if ($model->payment_status === 'paid' || $model->payment_status === 'confirmed') {
                                return '<span class="badge rounded-pill bg-success px-3 py-2"><i class="fas fa-check-circle"></i> Confirmado</span>';
                            }
                            return '<span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fas fa-clock"></i> Pendente</span>';
                        },
                    ],

                    // Botões de Ação (A grande correção!)
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Ações',
                        'template' => '{pay} {view}',
                        'contentOptions' => ['class' => 'text-end pe-4', 'style' => 'min-width: 200px;'],
                        'headerOptions' => ['class' => 'text-end pe-4'],
                        'buttons' => [
                            // Botão PAGAR (Grande e visível)
                            'pay' => function ($url, $model) {
                                if ($model->payment_status === 'pending') {
                                    return Html::a('<i class="fas fa-credit-card"></i> Pagar Agora', ['checkout', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-primary shadow-sm me-2 fw-bold',
                                        'style' => 'min-width: 110px;'
                                    ]);
                                }
                                return '';
                            },
                            // Botão VER (Agora com texto e borda)
                            'view' => function ($url, $model) {
                                return Html::a('Detalhes', ['event/view', 'id' => $model->event_id], [
                                    'class' => 'btn btn-sm btn-outline-secondary',
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>