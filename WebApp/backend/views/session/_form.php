<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Session $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $venueList */
?>

<div class="session-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
        if ($model->event_id) {
            echo $form->field($model, 'event_id')->hiddenInput()->label(false);
        } else {
            echo $form->field($model, 'event_id')->textInput();
        } ?>

    <?= $form->field($model, 'venue_id')->dropDownList(
            $venueList,
            ['prompt' => 'Selecione uma Sala...']
    ); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'start_time')->textInput() ?>

    <?= $form->field($model, 'end_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
