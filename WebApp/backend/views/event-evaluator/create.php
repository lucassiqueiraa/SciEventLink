<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\EventEvaluators $model */

$this->title = 'Create Event Evaluators';
$this->params['breadcrumbs'][] = ['label' => 'Event Evaluators', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-evaluators-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
