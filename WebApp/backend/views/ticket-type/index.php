<?php

use common\models\Event;
use common\models\TicketType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\TicketTypeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Ticket Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Ticket Type', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
                [
                        'attribute' => 'event_id',
                        'label' => 'Evento',
                        'value' => function ($model) {
                            return $model->event ? $model->event->name : 'N/A';
                        },
                        'filter' => ArrayHelper::map(Event::find()->all(), 'id', 'name'),
                ],
            'name',
            'price',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TicketType $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
