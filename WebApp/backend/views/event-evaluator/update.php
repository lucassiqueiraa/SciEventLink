<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\EventEvaluators $model */

$this->title = 'Update Event Evaluators: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Event Evaluators', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="event-evaluators-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
