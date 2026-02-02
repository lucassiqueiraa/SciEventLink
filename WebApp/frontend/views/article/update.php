<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Article $model */
/** @var int $event_id */

$this->title = 'Editar Artigo Científico: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Eventos', 'url' => ['event/index']];
$this->params['breadcrumbs'][] = ['label' => 'Evento', 'url' => ['event/view', 'id' => $event_id]];
$this->params['breadcrumbs'][] = 'Editar Artigo';
?>

<div class="article-update container py-4">

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Submissão</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-light border">
                        <small><i class="fas fa-info-circle"></i> Podes alterar o título, o resumo ou carregar um novo PDF para substituir o anterior.</small>
                    </div>

                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'abstract')->textarea(['rows' => 6]) ?>

                    <div class="mb-3 p-3 bg-light rounded border">
                        <label class="form-label fw-bold">Ficheiro PDF</label>

                        <?php if ($model->file_path): ?>
                            <div class="mb-2">
                                <span class="badge bg-success">Atual</span>
                                <a href="<?= Url::to('@web/' . $model->file_path) ?>" target="_blank" class="text-decoration-none">
                                    <i class="fas fa-file-pdf text-danger"></i> Ver ficheiro atual
                                </a>
                            </div>
                        <?php endif; ?>

                        <?= $form->field($model, 'articleFile')->fileInput([
                            'accept' => '.pdf',
                            'class' => 'form-control'
                        ])->label('Carregar Novo PDF (Opcional - Substitui o atual)') ?>
                        <div class="form-text text-muted">Deixe em branco para manter o ficheiro atual.</div>
                    </div>

                    <hr>

                    <div class="form-group d-flex justify-content-between">
                        <?= Html::a('<i class="fas fa-arrow-left"></i> Cancelar', ['event/view', 'id' => $event_id], ['class' => 'btn btn-outline-secondary']) ?>

                        <?= Html::submitButton('<i class="fas fa-save"></i> Atualizar Artigo', ['class' => 'btn btn-warning px-4 font-weight-bold']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>

        </div>
    </div>

</div>