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

$this->title = 'Agenda de Sessões';
$this->params['breadcrumbs'][] = $this->title;

// Captura o ID do evento para manter o filtro ao criar
$params = Yii::$app->request->getQueryParam('SessionSearch');
$event_id = $params['event_id'] ?? null;
?>

<div class="session-index">

    <div class="card shadow mb-4 border-0">

        <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-alt" style="margin-right: 10px;"></i><?= Html::encode($this->title) ?>
            </h5>

            <?= Html::a('<i class="fas fa-plus" style="margin-right: 5px;"></i> Nova Sessão',
                    ['create', 'event_id' => $event_id, 'returnUrl' => Url::current()],
                    ['class' => 'btn btn-success btn-sm shadow-sm']
            ) ?>
        </div>

        <div class="card-body p-0">
            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,

                    'layout' => "{items}\n<div class='d-flex justify-content-center p-3'>{pager}</div>",

                    'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],


                    'emptyText' => '
                    <div class="text-center p-5 text-muted">
                        <i class="far fa-folder-open fa-3x mb-3 text-gray-300"></i><br>
                        <span>Nenhuma sessão encontrada para este filtro.</span>
                    </div>',

                    'columns' => [

                            [
                                    'attribute' => 'event_id',
                                    'label' => 'Evento',
                                    'value' => function($model) {
                                        return $model->event ? $model->event->name : 'Sem Evento';
                                    },
                                    'filter' => ArrayHelper::map(Event::find()->all(), 'id', 'name'),
                                    'contentOptions' => ['style' => 'vertical-align: middle; font-weight: 600; color: #555;'],
                            ],

                            [
                                    'attribute' => 'title',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        // Link direto no título para facilitar
                                        return Html::a(Html::encode($model->title),
                                                ['view', 'id' => $model->id, 'returnUrl' => Url::current()],
                                                ['class' => 'text-primary font-weight-bold', 'style' => 'text-decoration: none;']
                                        );
                                    },
                                    'contentOptions' => ['style' => 'vertical-align: middle; min-width: 200px;'],
                            ],


                            [
                                    'attribute' => 'venue_id',
                                    'label' => 'Sala',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return $model->venue
                                                ? '<i class="fas fa-map-marker-alt text-danger" style="margin-right: 5px;"></i>' . Html::encode($model->venue->name)
                                                : '<span class="badge badge-light">A definir</span>';
                                    },
                                    'contentOptions' => ['style' => 'vertical-align: middle; width: 15%;'],
                            ],


                            [
                                    'attribute' => 'start_time',
                                    'label' => 'Início',
                                    'format' => ['datetime', 'php:d/m H:i'],
                                    'contentOptions' => ['class' => 'text-success', 'style' => 'vertical-align: middle; font-weight: bold; width: 120px;'],
                                    'filterInputOptions' => ['class' => 'form-control', 'placeholder' => 'Dia...'],
                            ],


                            [
                                    'attribute' => 'end_time',
                                    'label' => 'Fim',
                                    'format' => ['datetime', 'php:H:i'], // Só mostro a hora para poupar espaço
                                    'contentOptions' => ['class' => 'text-muted', 'style' => 'vertical-align: middle; width: 80px;'],
                            ],

                        // 6. BOTÕES DE AÇÃO
                            [
                                    'class' => ActionColumn::className(),
                                    'header' => 'Ações',
                                    'template' => '{view} {update} {delete}',
                                    'contentOptions' => ['style' => 'width: 130px; text-align: center; vertical-align: middle;'],

                                    'urlCreator' => function ($action, Session $model, $key, $index, $column) {
                                        return Url::toRoute([$action, 'id' => $model->id, 'returnUrl' => Url::current()]);
                                    },

                                    'buttons' => [
                                            'view' => function ($url, $model) {
                                                return Html::a('<i class="fas fa-eye"></i>', $url, [
                                                        'class' => 'btn btn-outline-info btn-sm',
                                                        'title' => 'Ver detalhes',
                                                        'style' => 'margin-right: 4px;'
                                                ]);
                                            },
                                            'update' => function ($url, $model) {
                                                return Html::a('<i class="fas fa-pencil-alt"></i>', $url, [
                                                        'class' => 'btn btn-outline-primary btn-sm',
                                                        'title' => 'Editar',
                                                        'style' => 'margin-right: 4px;'
                                                ]);
                                            },
                                            'delete' => function ($url, $model) {
                                                return Html::a('<i class="fas fa-trash"></i>', $url, [
                                                        'class' => 'btn btn-outline-danger btn-sm',
                                                        'title' => 'Apagar',
                                                        'data-confirm' => 'Tem a certeza que quer apagar esta sessão?',
                                                        'data-method' => 'post',
                                                ]);
                                            },
                                    ],
                            ],
                    ],
            ]); ?>
        </div>
    </div>

</div>