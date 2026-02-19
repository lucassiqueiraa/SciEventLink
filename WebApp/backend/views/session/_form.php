<?php

use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Session $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $venueList */
/** @var array $eventList */
?>

<div class="session-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if ($model->event_id && empty($eventList)) {
        echo $form->field($model, 'event_id')->hiddenInput()->label(false);
        echo '<div class="alert alert-info py-2 mb-3">Evento: <b>' . $model->event->name . '</b></div>';
    }
    else {
        echo $form->field($model, 'event_id')->dropDownList(
                $eventList,
                [
                        'prompt' => 'Selecione o Evento...',
                        'id' => 'select-evento-id',
                ]
        );
    }

    echo $form->field($model, 'venue_id')->dropDownList(
            $venueList,
            [
                    'prompt' => 'Selecione o Evento primeiro...',
                    'id' => 'select-venue-id',
            ]
    );
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php
    $minDate = '';
    $maxDate = '';

    if ($model->event_id && $model->event) {
        $minDate = date('Y-m-d\T00:00', strtotime($model->event->start_date));
        $maxDate = date('Y-m-d\T23:59', strtotime($model->event->end_date));
    } else {
        $minDate = date('Y-m-d\TH:i');
    }

    if ($model->start_time) {
        $model->start_time = date('Y-m-d\TH:i', strtotime($model->start_time));
    }
    if ($model->end_time) {
        $model->end_time = date('Y-m-d\TH:i', strtotime($model->end_time));
    }
    ?>

    <?= $form->field($model, 'start_time')->input('datetime-local', [
            'min' => $minDate,
            'max' => $maxDate ?: null
    ]) ?>

    <?= $form->field($model, 'end_time')->input('datetime-local', [
            'min' => $minDate,
            'max' => $maxDate ?: null
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php
    $this->registerJsFile(
            '@web/js/session-form.js',
            ['depends' => [JqueryAsset::class]]
    );
    ?>

</div>