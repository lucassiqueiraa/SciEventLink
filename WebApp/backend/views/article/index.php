<?php

use common\models\Article;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Gestão de Artigos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-file-signature me-2"></i> <?= Html::encode($this->title) ?>
            </h5>
            <span class="badge bg-light text-dark border">
                Total: <?= $dataProvider->getTotalCount() ?>
            </span>
        </div>

        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover table-striped align-middle mb-0'],
                'layout' => "{items}\n<div class='p-3 d-flex justify-content-end'>{pager}</div>",
                'columns' => [
                    [
                        'attribute' => 'id',
                        'contentOptions' => ['class' => 'text-center fw-bold', 'style' => 'width: 60px;'],
                        'headerOptions' => ['class' => 'text-center'],
                    ],

                    [
                        'attribute' => 'title',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<span class="fw-bold text-dark">' . Html::encode($model->title) . '</span>';
                        }
                    ],

                    [
                        'attribute' => 'Autor',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $user = $model->registration->user->username ?? 'Desconhecido';
                            return '<div class="text-secondary"><i class="fas fa-user me-1"></i> ' . Html::encode($user) . '</div>';
                        }
                    ],

                    [
                        'attribute' => 'Evento',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $event = $model->registration->event->name ?? 'N/A';
                            return '<div class="text-info"><i class="fas fa-calendar-alt me-1"></i> ' . Html::encode($event) . '</div>';
                        }
                    ],

                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'value' => function ($model) {
                            $statusColors = [
                                'accepted'  => 'success',
                                'rejected'  => 'danger',
                                'submitted' => 'primary',
                                'in_review' => 'warning',
                            ];

                            $statusIcons = [
                                'accepted'  => 'check-circle',
                                'rejected'  => 'times-circle',
                                'submitted' => 'arrow-circle-up',
                                'in_review' => 'clock',
                            ];

                            $color = isset($statusColors[$model->status]) ? $statusColors[$model->status] : 'secondary';
                            $icon = isset($statusIcons[$model->status]) ? $statusIcons[$model->status] : 'circle';

                            return "<span class='badge rounded-pill bg-{$color} px-3 py-2 shadow-sm'>"
                                . "<i class='fas fa-{$icon} me-1'></i>"
                                . strtoupper($model->status)
                                . "</span>";
                        }
                    ],

                    [
                        'class' => ActionColumn::class,
                        'template' => '{view} {delete}',
                        'header' => 'Ações',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 160px;'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('Ver', $url, [
                                    'class' => 'btn btn-sm btn-outline-primary shadow-sm fw-bold',
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('Apagar', $url, [ // Mudei de Icon para Texto "Apagar"
                                    'class' => 'btn btn-sm btn-outline-danger shadow-sm ms-2 fw-bold',
                                    'data-confirm' => 'Tem a certeza que pretende eliminar este artigo?',
                                    'data-method' => 'post',
                                ]);
                            },
                        ],
                        'urlCreator' => function ($action, Article $model, $key, $index, $column) {
                            return Url::toRoute([$action, 'id' => $model->id]);
                        }
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>