<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Login';
?>

<div class="site-login container d-flex align-items-center justify-content-center min-vh-100 py-3">

    <div class="card shadow border-0 rounded-4 w-100" style="max-width: 400px;"> <div class="card-body p-4">

            <div class="text-center mb-3">
                <?= Html::img('@web/images/logoSciEventLink-noBg2.png', [
                        'alt' => Yii::$app->name,
                    // Reduzi a logo de 80px para 50px
                        'style' => 'height: 50px; width: auto;',
                        'class' => 'mb-2'
                ]); ?>
                <h5 class="fw-bold text-dark mb-1">Bem-vindo</h5>
                <p class="text-muted small mb-0">Insira os seus dados para entrar.</p>
            </div>

            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <div class="mb-2">
                <?= $form->field($model, 'username')->textInput([
                        'autofocus' => true,
                        'class' => 'form-control bg-light border-0', // Removi form-control-lg
                        'placeholder' => 'Utilizador'
                ])->label('Utilizador', ['class' => 'form-label fw-bold small text-muted mb-0']) ?>
            </div>

            <div class="mb-2">
                <?= $form->field($model, 'password')->passwordInput([
                        'class' => 'form-control bg-light border-0',
                        'placeholder' => 'Senha'
                ])->label('Palavra-passe', ['class' => 'form-label fw-bold small text-muted mb-0']) ?>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">

                <?= $form->field($model, 'rememberMe', ['options' => ['class' => 'mb-0']])->checkbox([
                        'class' => 'form-check-input',
                        'style' => 'transform: scale(0.9);' // Deixa o quadradinho levemente menor
                ])->label('Lembrar', ['class' => 'form-check-label small text-muted']) ?>

                <?= Html::a('Esqueceu a senha?', ['site/request-password-reset'], [
                        'class' => 'small text-decoration-none fw-bold',
                        'style' => 'font-size: 0.85rem;'
                ]) ?>
            </div>

            <div class="d-grid mb-3">
                <?= Html::submitButton('Entrar', [
                        'class' => 'btn btn-primary fw-bold shadow-sm',
                        'name' => 'login-button'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <div class="text-center border-top pt-2 mt-2">
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Não tem conta?</span>
                    <?= Html::a('Criar conta', ['site/signup'], ['class' => 'text-decoration-none fw-bold text-success']) ?>
                </div>
                <div class="mt-1" style="font-size: 0.75rem;">
                    <?= Html::a('Reenviar email de verificação', ['site/resend-verification-email'], ['class' => 'text-muted text-decoration-none']) ?>
                </div>
            </div>

        </div>
    </div>
</div>