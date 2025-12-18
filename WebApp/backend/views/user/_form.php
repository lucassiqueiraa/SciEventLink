<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var common\models\UserProfile $profile */ // <--- Tem de ser profile
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary([$user, $profile]) ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">Dados de Acesso</div>
                <div class="card-body">
                    <?= $form->field($user, 'username')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'email')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($user, 'password_hash')->passwordInput(['value' => ''])
                            ->label('Password (Deixe vazio para manter)') ?>

                    <?= $form->field($user, 'role')->dropDownList(
                            ['participant' => 'Participante', 'organizer' => 'Organizador', 'admin' => 'Administrador'],
                            ['prompt' => 'Selecione o Papel...']
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">Dados Pessoais</div>
                <div class="card-body">
                    <?= $form->field($profile, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($profile, 'nif')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($profile, 'phone')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>