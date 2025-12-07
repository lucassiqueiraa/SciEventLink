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
        <?php
        // 1. LÓGICA DO BOTÃO CRIAR
        // Capturamos o ID do evento que está no filtro da URL (ex: &SessionSearch[event_id]=5)
        $params = Yii::$app->request->getQueryParam('SessionSearch');
        $event_id = $params['event_id'] ?? null;
        ?>

        <?= Html::a('Create Session',
                // Passamos o ID para o create e definimos o returnUrl para voltar para AQUI
                ['create', 'event_id' => $event_id, 'returnUrl' => Url::current()],
                ['class' => 'btn btn-success']
        ) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'], // Adicionei o SerialColumn para numerar as linhas

                    [
                            'attribute' => 'event_id',
                            'value' => 'event.name',
                            'filter' => ArrayHelper::map(Event::find()->all(), 'id', 'name'),
                    ],
                    [
                            'attribute' => 'venue_id',
                            'value' => function($model) {
                                return $model->venue ? $model->venue->name : 'N/A';
                            }
                    ],
                    'title',
                    [
                            'attribute' => 'start_time',
                            'label' => 'Início',
                            'format' => ['datetime', 'php:d/m/Y H:i'],
                            'contentOptions' => ['style' => 'width: 150px; font-weight: bold;'],
                    ],
                    [
                            'attribute' => 'end_time',
                            'label' => 'Fim',
                            'format' => ['datetime', 'php:d/m/Y H:i'],
                            'contentOptions' => ['style' => 'width: 80px;'],
                    ],

                // 2. LÓGICA DOS BOTÕES DE AÇÃO (View, Update, Delete)
                    [
                            'class' => ActionColumn::className(),
                            'urlCreator' => function ($action, Session $model, $key, $index, $column) {
                                // Aqui definimos a URL para onde cada botão (olho, lápis, lixeira) vai.
                                // Adicionamos 'returnUrl' => Url::current() em TODOS eles.
                                // Assim, quando acabar de editar ou apagar, ele volta para esta lista filtrada.
                                return Url::toRoute([
                                        $action,
                                        'id' => $model->id,
                                        'returnUrl' => Url::current()
                                ]);
                            }
                    ],
            ],
    ]); ?>

</div>