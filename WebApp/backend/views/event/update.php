<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Event $model */
/** @var yii\data\ActiveDataProvider $venuesDataProvider */

$this->title = 'Update Event: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="event-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'venuesDataProvider' => $venuesDataProvider,
    ]) ?>

</div>
