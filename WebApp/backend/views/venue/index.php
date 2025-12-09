<?php

use common\models\Event;
use common\models\Venue;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\VenueSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Venues';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="venue-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php $event_id = Yii::$app->request->getQueryParam('VenueSearch')['event_id'] ?? null; ?>

        <?= Html::a('Create Venue', ['create', 'event_id' => $event_id], ['class' => 'btn btn-success']) ?>
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
                    // Transforma a caixa de texto "2" num Dropdown com os nomes reais
                        'filter' => ArrayHelper::map(
                                Event::find()->asArray()->all(),
                                'id',
                                'name'
                        ),
                ],
            'name',
            'capacity',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Venue $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
