<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Article $model */
/** @var int $event_id */

$this->title = 'Submeter Artigo Científico';
$this->params['breadcrumbs'][] = ['label' => 'Eventos', 'url' => ['event/index']];
$this->params['breadcrumbs'][] = ['label' => 'Evento', 'url' => ['event/view', 'id' => $event_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="article-create container py-4">

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-file-upload"></i> <?= Html::encode($this->title) ?></h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> O artigo deve ser submetido em formato <strong>PDF</strong>. Certifique-se de que o título e o resumo estão corretos antes de enviar.</small>
                    </div>

                    <?php
                    $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
                    ?>

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => 'Ex: Impacto da IA na Medicina Moderna']) ?>

                    <?= $form->field($model, 'abstract')->textarea(['rows' => 6, 'placeholder' => 'Escreva um breve resumo do seu trabalho...']) ?>

                    <div class="mb-3">
                        <?= $form->field($model, 'articleFile')->fileInput([
                            'accept' => '.pdf',
                            'class' => 'form-control'
                        ])->label('Ficheiro PDF do Artigo') ?>
                        <div class="form-text text-muted">Tamanho máximo: 10MB (Apenas .pdf)</div>
                    </div>

                    <hr>

                    <div class="form-group d-flex justify-content-between">
                        <?= Html::a('<i class="fas fa-arrow-left"></i> Voltar ao Evento', ['event/view', 'id' => $event_id], ['class' => 'btn btn-outline-secondary']) ?>

                        <?= Html::submitButton('<i class="fas fa-paper-plane"></i> Submeter Artigo', ['class' => 'btn btn-success px-4']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>

        </div>
    </div>

</div>