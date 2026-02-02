<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Event $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>

        <?= Html::a('<i class="fas fa-building"></i> Gerir Salas',
                ['venue/index', 'VenueSearch[event_id]' => $model->id],
                ['class' => 'btn btn-info'])
        ?>

        <?= Html::a('<i class="fas fa-calendar-alt"></i> Gerir Agenda',
                ['session/index', 'SessionSearch[event_id]' => $model->id],
                ['class' => 'btn btn-warning'])
        ?>

        <?= Html::a('<i class="fas fa-user-graduate"></i> Gerir Avaliadores',
                ['event-evaluator/index', 'event_id' => $model->id],
                ['class' => 'btn btn-success']
        ) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description:ntext',
            'start_date',
            'end_date',
            'submission_deadline',
            'evaluation_deadline',
            'status',
        ],
    ]) ?>

</div>
