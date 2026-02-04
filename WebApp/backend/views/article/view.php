<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Article $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Artigos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar à Lista', ['index'], ['class' => 'btn btn-secondary']) ?>

        <?= Html::a('<i class="fas fa-trash"></i> Apagar Registo', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-outline-danger float-end',
                'data' => [
                        'confirm' => 'ATENÇÃO: Isto apaga o artigo permanentemente da base de dados! Para reprovar um artigo, use o botão "Rejeitar" abaixo. Quer mesmo apagar?',
                        'method' => 'post',
                ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-12">
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                            'id',
                            'title',
                            'abstract:ntext',
                            [
                                    'attribute' => 'status',
                                    'value' => function ($model) {
                                        return strtoupper($model->status);
                                    },
                                    'contentOptions' => ['class' => 'fw-bold'],
                            ],
                            [
                                    'attribute' => 'file_path',
                                    'format' => 'raw',
                                    'value' => $model->file_path ? Html::a('<i class="fas fa-file-pdf"></i> Ver PDF', Yii::getAlias('@web/') . '../frontend/web/' . $model->file_path, ['target' => '_blank', 'class' => 'btn btn-sm btn-outline-dark']) : 'Sem ficheiro',
                            ],
                    ],
            ]) ?>
        </div>
    </div>

    <hr class="my-5">

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fas fa-star-half-alt"></i> Avaliações dos Revisores</h4>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <h5 class="me-3 mb-0">Média Atual:</h5>
                <span class="badge bg-dark" style="font-size: 1.2em">
                    <?= $model->getAverageScore() ?> / 20
                </span>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Avaliador</th>
                    <th class="text-center" style="width: 100px;">Nota</th>
                    <th>Parecer / Comentários</th>
                    <th style="width: 150px;">Data</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($model->evaluations)): ?>
                    <tr><td colspan="4" class="text-center text-muted">Ainda não existem avaliações para este artigo.</td></tr>
                <?php else: ?>
                    <?php foreach ($model->evaluations as $evaluation): ?>
                        <tr>
                            <td><?= Html::encode($evaluation->evaluator->username ?? 'Desconhecido') ?></td>
                            <td class="fw-bold text-center"><?= $evaluation->score ?></td>
                            <td><?= nl2br(Html::encode($evaluation->comments)) ?></td>
                            <td><?= Yii::$app->formatter->asDate($evaluation->evaluation_date) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-dark shadow">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0"><i class="fas fa-gavel"></i> Veredito Final do Organizador</h4>
        </div>
        <div class="card-body text-center py-4">

            <?php if ($model->status === 'accepted'): ?>
                <div class="alert alert-success d-inline-block mb-4 px-5">
                    <i class="fas fa-check-circle fa-2x d-block mb-2"></i>
                    <h5 class="mb-0">Este artigo foi <strong>ACEITE</strong>.</h5>
                </div>
                <div class="card bg-light border-success mb-3 mx-auto" style="max-width: 500px;">
                    <div class="card-body">
                        <h6 class="card-title text-success fw-bold"><i class="fas fa-calendar-alt"></i> Agendar Apresentação</h6>
                        <p class="card-text small text-muted">Escolha em qual sessão este artigo será apresentado.</p>

                        <?= Html::beginForm(['assign-session', 'id' => $model->id], 'post', ['class' => 'd-flex gap-2 justify-content-center']) ?>

                        <?= Html::dropDownList(
                                'session_id',
                                $model->session_id,
                                $sessionList,
                                ['class' => 'form-select', 'prompt' => '--- Selecione uma Sessão ---']
                        ) ?>

                        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>

                        <?= Html::endForm() ?>
                    </div>
                </div>
            <?php elseif ($model->status === 'rejected'): ?>
                <div class="alert alert-danger d-inline-block mb-4 px-5">
                    <i class="fas fa-times-circle fa-2x d-block mb-2"></i>
                    <h5 class="mb-0">Este artigo foi <strong>REJEITADO</strong>.</h5>
                </div>
            <?php else: ?>
                <p class="lead mb-4">Com base nas avaliações acima, qual é a decisão para este artigo?</p>
            <?php endif; ?>

            <div class="mt-2">

                <?php if ($model->status !== 'accepted'): ?>
                    <?= Html::a('<i class="fas fa-check"></i> ACEITAR',
                            ['set-status', 'id' => $model->id, 'status' => 'accepted'],
                            [
                                    'class' => 'btn btn-success btn-lg mx-2 px-4 shadow-sm',
                                    'data' => ['method' => 'post', 'confirm' => 'Confirmar aceitação deste artigo?']
                            ]
                    ) ?>
                <?php endif; ?>

                <?php if ($model->status !== 'rejected'): ?>
                    <?= Html::a('<i class="fas fa-times"></i> REJEITAR',
                            ['set-status', 'id' => $model->id, 'status' => 'rejected'],
                            [
                                    'class' => 'btn btn-danger btn-lg mx-2 px-4 shadow-sm',
                                    'data' => ['method' => 'post', 'confirm' => 'Confirmar rejeição deste artigo?']
                            ]
                    ) ?>
                <?php endif; ?>

                <?php if ($model->status !== 'in_review'): ?>
                    <?= Html::a('<i class="fas fa-undo"></i> COLOCAR EM REVISÃO',
                            ['set-status', 'id' => $model->id, 'status' => 'in_review'],
                            ['class' => 'btn btn-secondary mx-2 shadow-sm', 'data' => ['method' => 'post']]
                    ) ?>
                <?php endif; ?>

            </div>
        </div>
    </div>

</div>