<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
?>

<div class="site-login container py-5">

    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">

        <div class="col-12 col-md-6 col-lg-4">

            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <?= Html::img('@web/images/logoSciEventLink-noBg2.png', [
                                'alt' => Yii::$app->name,
                                'class' => 'mb-3',
                                'style' => 'max-height: 80px; width: auto; max-width: 100%;',
                        ]); ?>

                        <h4 class="fw-bold text-dark"><?= Html::encode($this->title) ?></h4>
                        <p class="text-muted small">Aceda à sua conta de gestão.</p>
                    </div>

                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                    <div class="mb-3">
                        <?= $form->field($model, 'username')->textInput([
                                'autofocus' => true,
                                'class' => 'form-control form-control-lg bg-light border-0',
                                'placeholder' => 'Utilizador'
                        ])->label(false)
                        ?>
                    </div>

                    <div class="mb-3">
                        <?= $form->field($model, 'password')->passwordInput([
                                'class' => 'form-control form-control-lg bg-light border-0',
                                'placeholder' => 'Palavra-passe'
                        ])->label(false)
                        ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <?= $form->field($model, 'rememberMe')->checkbox([
                                'class' => 'form-check-input me-2',
                        ])->label('Lembrar-me', ['class' => 'form-check-label small text-muted']) ?>
                    </div>

                    <div class="d-grid">
                        <?= Html::submitButton('Entrar', [
                                'class' => 'btn btn-primary btn-lg fw-bold shadow-sm',
                                'style' => 'border-radius: 10px;',
                                'name' => 'login-button'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>

            <div class="text-center mt-4">
                <a href="<?= Yii::$app->homeUrl ?>" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left"></i> Voltar à Página Inicial
                </a>
            </div>

        </div>
    </div>
</div>