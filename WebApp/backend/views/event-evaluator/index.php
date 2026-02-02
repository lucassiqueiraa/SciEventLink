<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\JqueryAsset;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\Event $event */
/** @var yii\data\ActiveDataProvider $candidatesProvider */
/** @var yii\data\ActiveDataProvider $evaluatorsProvider */

$this->registerJsFile(
        '@web/js/event-evaluator.js',
        ['depends' => [JqueryAsset::class]]
);

$this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css");
?>

<div class="row mt-3">
    <div class="col-12 mb-3">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Gerir equipa de avaliação para: <b><?= Html::encode($event->name) ?></b>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white">Disponíveis</h6>
            </div>
            <div class="card-body">

                <?php Pjax::begin(['id' => 'pjax-candidates', 'enablePushState' => false, 'timeout' => 5000]); ?>

                <?= GridView::widget([
                        'dataProvider' => $candidatesProvider,
                        'summary' => '',
                        'columns' => [
                                'username',
                                [
                                        'class' => 'yii\grid\ActionColumn',
                                        'template' => '{add}',
                                        'header' => 'Ação',
                                        'contentOptions' => ['style' => 'width: 120px; text-align: center;'],
                                        'buttons' => [
                                                'add' => function ($url, $model) use ($event) {
                                                    return Html::a('<i class="fas fa-plus"></i> Adicionar',
                                                            ['event-evaluator/add', 'event_id' => $event->id, 'user_id' => $model->id],
                                                            [
                                                                    'class' => 'btn btn-sm btn-success ajax-action w-100',
                                                                    'title' => 'Adicionar',
                                                                    'style' => 'color: white; font-weight: bold;',
                                                                    'data-pjax' => '0',
                                                            ]
                                                    );
                                                },
                                        ],
                                ],
                        ],
                ]); ?>

                <?php Pjax::end(); ?>

            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success">
                <h6 class="m-0 font-weight-bold text-white">Avaliadores Atuais</h6>
            </div>
            <div class="card-body">

                <?php Pjax::begin(['id' => 'pjax-evaluators', 'enablePushState' => false, 'timeout' => 5000]); ?>

                <?= GridView::widget([
                        'dataProvider' => $evaluatorsProvider,
                        'summary' => '',
                        'columns' => [
                                [
                                        'attribute' => 'user_id',
                                        'value' => 'user.username',
                                ],
                                [
                                        'class' => 'yii\grid\ActionColumn',
                                        'template' => '{delete}',
                                        'header' => 'Ação',
                                        'contentOptions' => ['style' => 'width: 120px; text-align: center;'],
                                        'buttons' => [
                                                'delete' => function ($url, $model) {
                                                    return Html::a('<i class="fas fa-trash"></i> Remover',
                                                            ['delete', 'id' => $model->id],
                                                            [
                                                                    'class' => 'btn btn-sm btn-danger ajax-action w-100',
                                                                    'title' => 'Remover',
                                                                    'style' => 'color: white; font-weight: bold;',
                                                                    'data-pjax' => '0',
                                                            ]
                                                    );
                                                },
                                        ],
                                ],
                        ],
                ]); ?>

                <?php Pjax::end(); ?>

            </div>
        </div>
    </div>
</div>