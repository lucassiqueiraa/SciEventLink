<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Event $model */
/** @var yii\data\ActiveDataProvider $venuesDataProvider */
/** @var yii\data\ActiveDataProvider $ticketsDataProvider */
/** @var yii\data\ActiveDataProvider $sessionsDataProvider */

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
            'ticketsDataProvider' => $ticketsDataProvider,
            'sessionsDataProvider' => $sessionsDataProvider,

    ]) ?>

</div>
