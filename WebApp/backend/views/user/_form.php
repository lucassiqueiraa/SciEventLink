<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\OrganizerSignupForm $model */

$this->title = 'Criar Novo Organizador';
$this->params['breadcrumbs'][] = ['label' => 'Utilizadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">



    <div class="organizer-form mt-4">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-person-vcard"></i> Dados do Perfil
                    </div>
                    <div class="card-body">
                        <?= $form->field($model, 'name')->textInput(['autofocus' => true])->label('Nome Completo') ?>

                        <?= $form->field($model, 'nif')->textInput(['maxlength' => 9]) ?>

                        <?= $form->field($model, 'phone')->textInput(['maxlength' => 20]) ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        <i class="bi bi-key"></i> Dados de Acesso
                    </div>
                    <div class="card-body">
                        <?= $form->field($model, 'username')->textInput() ?>

                        <?= $form->field($model, 'email')->textInput() ?>

                        <?= $form->field($model, 'password')->passwordInput() ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mt-3">
            <?= Html::submitButton('<i class="bi bi-check-lg"></i> Criar Organizador', ['class' => 'btn btn-success btn-lg']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>