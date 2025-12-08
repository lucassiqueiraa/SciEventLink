<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Session $model */
/** @var $venueList */
/** @var $eventList */

$this->title = 'Create Session';
$this->params['breadcrumbs'][] = ['label' => 'Sessions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
            'venueList' => $venueList,
            'eventList' => $eventList,
    ]) ?>

</div>
