<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Criar Conta';
?>

<div class="site-signup container d-flex align-items-center justify-content-center min-vh-100 py-4">

    <div class="card shadow border-0 rounded-4 w-100" style="max-width: 500px;">
        <div class="card-body p-4">

            <div class="text-center mb-3">
                <?= Html::img('@web/images/logoSciEventLink-noBg2.png', [
                        'alt' => Yii::$app->name,
                        'style' => 'height: 45px; width: auto;',
                        'class' => 'mb-2'
                ]); ?>
                <h5 class="fw-bold text-dark mb-1">Registo de Participante</h5>
                <p class="text-muted small mb-0">Preencha os dados para criar o seu bilhete digital.</p>
            </div>

            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

            <div class="mb-2">
                <?= $form->field($model, 'name')->textInput([
                        'autofocus' => true,
                        'class' => 'form-control bg-light border-0',
                        'placeholder' => 'Seu Nome Completo'
                ])->label('Nome Completo', ['class' => 'form-label fw-bold small text-muted mb-0']) ?>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <?= $form->field($model, 'nif')->textInput([
                            'maxlength' => 9,
                            'class' => 'form-control bg-light border-0',
                            'placeholder' => '123456789'
                    ])->label('NIF <span class="fw-normal text-muted">(Opcional)</span>', ['class' => 'form-label fw-bold small text-muted mb-0']) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'phone')->textInput([
                            'maxlength' => 20,
                            'class' => 'form-control bg-light border-0',
                            'placeholder' => '+351 ...'
                    ])->label('Telefone <span class="fw-normal text-muted">(Opcional)</span>', ['class' => 'form-label fw-bold small text-muted mb-0']) ?>
                </div>
            </div>

            <div class="d-flex align-items-center mb-3">
                <hr class="flex-grow-1 my-0 text-muted">
                <span class="px-2 small text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Dados de Acesso</span>
                <hr class="flex-grow-1 my-0 text-muted">
            </div>

            <div class="row g-2 mb-2">
                <div class="col-6">
                    <?= $form->field($model, 'username')->textInput([
                            'class' => 'form-control bg-light border-0',
                            'placeholder' => 'Username'
                    ])->label('Username', ['class' => 'form-label fw-bold small text-muted mb-0']) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'email')->textInput([
                            'class' => 'form-control bg-light border-0',
                            'placeholder' => 'email@exemplo.com'
                    ])->label('Email', ['class' => 'form-label fw-bold small text-muted mb-0']) ?>
                </div>
            </div>

            <div class="mb-4">
                <?= $form->field($model, 'password')->passwordInput([
                        'class' => 'form-control bg-light border-0',
                        'placeholder' => 'Defina uma senha segura'
                ])->label('Palavra-passe', ['class' => 'form-label fw-bold small text-muted mb-0']) ?>
            </div>

            <div class="d-grid mb-3">
                <?= Html::submitButton('Criar Conta', [
                        'class' => 'btn btn-primary fw-bold shadow-sm',
                        'name' => 'signup-button'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <div class="text-center border-top pt-2 mt-2">
                <div class="small">
                    <span class="text-muted">Já tem conta?</span>
                    <?= Html::a('Entrar', ['site/login'], ['class' => 'text-decoration-none fw-bold text-primary']) ?>
                </div>
            </div>

        </div>
    </div>
</div>