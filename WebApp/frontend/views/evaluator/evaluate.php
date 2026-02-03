<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Evaluation $model */
/** @var common\models\Article $article */

$this->title = 'Avaliar: ' . $article->title;
$this->params['breadcrumbs'][] = ['label' => 'Painel do Avaliador', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Avaliar';
?>
<div class="evaluation-create container py-4">

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> Detalhes do Artigo</h5>
                </div>
                <div class="card-body">
                    <h3><?= Html::encode($article->title) ?></h3>

                    <div class="badge bg-info text-dark mb-3">
                        <?= Html::encode($article->registration->event->name) ?>
                    </div>

                    <h6 class="fw-bold mt-3">Resumo:</h6>
                    <div class="p-3 bg-light border rounded">
                        <?= nl2br(Html::encode($article->abstract)) ?>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-bold">Ficheiro:</h6>
                        <?php if ($article->file_path): ?>
                            <?= Html::a('<i class="fas fa-file-pdf fa-2x text-danger align-middle"></i> Descarregar PDF do Artigo',
                                \yii\helpers\Url::to('@web/' . $article->file_path),
                                [
                                    'class' => 'btn btn-outline-dark w-100 py-3',
                                    'target' => '_blank',
                                    'data-pjax' => 0
                                ]
                            ) ?>
                        <?php else: ?>
                            <div class="alert alert-warning">Ficheiro não disponível.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow border-primary h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> O Seu Parecer</h5>
                </div>
                <div class="card-body">

                    <?php $form = ActiveForm::begin(); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'score')->input('number', [
                                'min' => 0,
                                'max' => 20, // Ou 10, conforme a tua escala
                                'step' => '0.1', // PERMITE DECIMAIS (ex: 15.5)
                                'class' => 'form-control form-control-lg text-center fw-bold',
                                'placeholder' => '0.0'
                            ])->label('Nota (0-20)') ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'comments')->textarea([
                        'rows' => 8,
                        'placeholder' => 'Escreva aqui os pontos fortes e fracos do artigo, sugestões de melhoria, etc.'
                    ])->label('Comentários / Justificação') ?>

                    <hr>

                    <div class="d-grid gap-2">
                        <?= Html::submitButton('<i class="fas fa-paper-plane"></i> Submeter Avaliação', ['class' => 'btn btn-success btn-lg']) ?>
                        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
</div>