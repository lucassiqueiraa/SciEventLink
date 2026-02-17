<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Session */

$this->title = $model->title;
?>

<div class="session-view">

    <div class="card shadow-sm mb-4 border-left-primary">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="font-weight-bold text-primary mb-3">
                        <?= Html::encode($model->title) ?>
                    </h2>

                    <div class="d-flex flex-wrap text-muted gap-5">
                        <div class="mr-4 mb-2">
                            <i class="fas fa-clock text-warning mr-1"></i>
                            <strong>Início:</strong> <?= Yii::$app->formatter->asDatetime($model->start_time, 'php:d M Y, H:i') ?>
                        </div>
                        <div class="mr-4 mb-2">
                            <i class="fas fa-hourglass-end text-warning mr-1"></i>
                            <strong>Fim:</strong> <?= Yii::$app->formatter->asTime($model->end_time, 'php:H:i') ?>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                            <strong>Local:</strong> <?= Html::encode($model->location ?? 'A definir') ?>
                        </div>
                    </div>

                    <?php if (!empty($model->description)): ?>
                        <div class="mt-3 pt-3 border-top">
                            <p class="mb-0 text-secondary"><?= Html::encode($model->description) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4 text-right">
                    <?= Html::a('<i class="fas fa-edit"></i> Editar Sessão', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-primary']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php Pjax::begin(['id' => 'pjax-questions-container', 'timeout' => 5000]); ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0"><i class="fas fa-comments"></i> Moderação ao Vivo</h4>
        <button id="btn-refresh-questions" class="btn btn-sm btn-light border shadow-sm">
            <i class="fas fa-sync-alt text-primary"></i> Atualizar Lista
        </button>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card h-100 border-warning shadow-sm">
                <div class="card-header bg-warning text-dark font-weight-bold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-hourglass-half"></i> Pendentes</span>
                    <span class="badge badge-light badge-pill"><?= $pendingProvider->totalCount ?></span>
                </div>
                <div class="card-body p-0">
                    <?= GridView::widget([
                            'dataProvider' => $pendingProvider,
                            'layout' => "{items}\n<div class='p-2'>{pager}</div>",
                            'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],
                            'emptyText' => '<div class="text-center p-4 text-muted"><i class="far fa-check-circle fa-2x mb-2"></i><br>Tudo limpo! Nenhuma pergunta pendente.</div>',
                            'columns' => [
                                    [
                                            'attribute' => 'question_text',
                                            'label' => 'Pergunta',
                                            'format' => 'ntext',
                                            'contentOptions' => ['style' => 'vertical-align: middle;'],
                                    ],
                                    [
                                            'header' => 'Ações',
                                            'format' => 'raw',
                                            'contentOptions' => ['class' => 'text-right', 'style' => 'width: 100px; vertical-align: middle;'],
                                            'value' => function($model) {
                                                return
                                                        Html::a('<i class="fas fa-check"></i>', ['/session-question/approve-question', 'id' => $model->id], [
                                                                'class' => 'btn btn-success btn-sm btn-moderation-action mr-1',
                                                                'title' => 'Aprovar',
                                                        ]) .
                                                        Html::a('<i class="fas fa-trash"></i>', ['/session-question/delete-question', 'id' => $model->id], [
                                                                'class' => 'btn btn-danger btn-sm btn-moderation-action',
                                                                'title' => 'Rejeitar',
                                                                'data-confirm-msg' => 'Rejeitar esta pergunta permanentemente?'
                                                        ]);
                                            }
                                    ]
                            ],
                    ]); ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card h-100 border-success shadow-sm">
                <div class="card-header bg-success text-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-broadcast-tower"></i> Ao Vivo (App)</span>
                    <span class="badge badge-light badge-pill"><?= $approvedProvider->totalCount ?></span>
                </div>
                <div class="card-body p-0">
                    <?= GridView::widget([
                            'dataProvider' => $approvedProvider,
                            'layout' => "{items}\n<div class='p-2'>{pager}</div>",
                            'tableOptions' => ['class' => 'table table-hover mb-0'],
                            'emptyText' => '<div class="text-center p-4 text-muted">Nenhuma pergunta aprovada ainda.</div>',
                            'columns' => [
                                    [
                                            'attribute' => 'user.username',
                                            'label' => 'Autor',
                                            'contentOptions' => ['class' => 'text-muted small', 'style' => 'width: 80px; vertical-align: middle;'],
                                    ],
                                    [
                                            'attribute' => 'question_text',
                                            'format' => 'ntext',
                                            'label' => 'Pergunta',
                                            'contentOptions' => ['style' => 'vertical-align: middle;'],
                                    ],
                                    [
                                            'header' => '',
                                            'format' => 'raw',
                                            'contentOptions' => ['class' => 'text-right', 'style' => 'width: 50px; vertical-align: middle;'],
                                            'value' => function($model) {
                                                return Html::a('<i class="fas fa-times"></i>', ['/session-question/delete-question', 'id' => $model->id], [
                                                        'class' => 'btn btn-outline-danger btn-sm btn-moderation-action',
                                                        'title' => 'Remover do ecrã',
                                                        'data-confirm-msg' => 'Remover esta pergunta do ecrã?'
                                                ]);
                                            }
                                    ]
                            ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>

    <?php Pjax::end(); ?>
</div>