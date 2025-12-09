<?php

use common\models\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\TicketType $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="ticket-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if ($model->event_id) {
        echo $form->field($model, 'event_id')->hiddenInput()->label(false);

        $eventName = $model->event ? $model->event->name : 'N/A';
        echo '<div class="alert alert-info">A criar bilhete para: <b>' . $eventName . '</b></div>';
    }
    else {
        echo $form->field($model, 'event_id')->dropDownList(
                ArrayHelper::map(Event::find()->all(), 'id', 'name'),
                ['prompt' => 'Selecione o Evento...']
        );
    }
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
