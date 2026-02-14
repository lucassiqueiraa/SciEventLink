<?php

use common\models\Event;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var \common\models\EventSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Gestão de Eventos';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="event-index">

    <div class="card shadow mb-4 border-0">

        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary text-white">
            <h6 class="m-0 font-weight-bold text-uppercase">
                <i class="fas fa-calendar-alt mr-2"></i> Lista de Eventos
            </h6>
            <?= Html::a('<i class="fas fa-plus-circle"></i> Novo Evento', ['create'], ['class' => 'btn btn-light btn-sm text-primary font-weight-bold']) ?>
        </div>

        <div class="card-body p-0"> <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],
                    'layout' => "{items}\n<div class='p-3 d-flex justify-content-center'>{pager}</div>",
                    'columns' => [


                            [
                                    'attribute' => 'id',
                                    'contentOptions' => ['style' => 'width: 60px; text-align: center; font-weight: bold;'],
                                    'headerOptions' => ['style' => 'width: 60px; text-align: center;'],
                            ],

                            [
                                    'attribute' => 'name',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        return Html::a(Html::encode($model->name), ['view', 'id' => $model->id], [
                                                'class' => 'text-primary font-weight-bold',
                                                'style' => 'font-size: 1.1em;'
                                        ]);
                                    },
                            ],

                            [
                                    'attribute' => 'description',
                                    'value' => function ($model) {
                                        return StringHelper::truncate($model->description, 50);
                                    },
                                    'contentOptions' => ['class' => 'text-muted small'],
                            ],

                            [
                                    'label' => 'Cronograma',
                                    'format' => 'raw',
                                    'headerOptions' => ['style' => 'width: 200px;'],
                                    'value' => function ($model) {
                                        $start = Yii::$app->formatter->asDate($model->start_date, 'php:d M Y');
                                        $end = Yii::$app->formatter->asDate($model->end_date, 'php:d M Y');
                                        return "<small class='d-block text-success'><i class='fas fa-play-circle'></i> $start</small>" .
                                                "<small class='d-block text-danger'><i class='fas fa-stop-circle'></i> $end</small>";
                                    },
                            ],

                            [
                                    'class' => ActionColumn::className(),
                                    'header' => 'Ações',
                                    'contentOptions' => ['style' => 'width: 150px; text-align: center;'],
                                    'template' => '{view} {update} {delete}',
                                    'buttons' => [
                                            'view' => function ($url, $model) {
                                                return Html::a('<i class="fas fa-eye"></i>', $url, [
                                                        'class' => 'btn btn-sm btn-info',
                                                        'title' => 'Ver Detalhes',
                                                        'data-toggle' => 'tooltip'
                                                ]);
                                            },
                                            'update' => function ($url, $model) {
                                                return Html::a('<i class="fas fa-pencil-alt"></i>', $url, [
                                                        'class' => 'btn btn-sm btn-warning',
                                                        'title' => 'Editar',
                                                        'data-toggle' => 'tooltip'
                                                ]);
                                            },
                                            'delete' => function ($url, $model) {
                                                return Html::a('<i class="fas fa-trash"></i>', $url, [
                                                        'class' => 'btn btn-sm btn-danger',
                                                        'title' => 'Apagar',
                                                        'data-method' => 'post',
                                                        'data-confirm' => 'Tem a certeza que deseja apagar este evento?',
                                                        'data-toggle' => 'tooltip'
                                                ]);
                                            },
                                    ],
                                    'urlCreator' => function ($action, Event $model, $key, $index, $column) {
                                        return Url::toRoute([$action, 'id' => $model->id]);
                                    }
                            ],
                    ],
            ]); ?>
        </div>
    </div>
</div>