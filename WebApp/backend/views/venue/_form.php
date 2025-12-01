<?php

use common\models\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Venue $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="venue-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if ($model->event_id) {
        echo $form->field($model, 'event_id')->hiddenInput()->label(false);

        if ($model->event) {
            echo '<div class="alert alert-info">Adicionando sala ao evento: <b>' . $model->event->name . '</b></div>';
        }
    } else {
        echo $form->field($model, 'event_id')->dropDownList(
                ArrayHelper::map(Event::find()->all(), 'id', 'name'),
                ['prompt' => 'Selecione um Evento...']
        );
    }
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'capacity')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
