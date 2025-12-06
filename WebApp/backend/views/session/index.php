<?php

use common\models\Event;
use common\models\Session;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\SessionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sessions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Session', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

                [
                        'attribute' => 'event_id',
                        'label' => 'Evento',
                        'value' => 'event.name',
                        'filter' => ArrayHelper::map(Event::find()->all(), 'id', 'name'),
                ],
                [
                        'attribute' => 'venue_id',
                        'label' => 'Sala',
                        'value' => function($model) {
                            return $model->venue ? $model->venue->name : 'N/A';
                        }
                ],
            'title',
            'start_time',
            'end_time',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Session $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
