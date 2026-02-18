<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\JqueryAsset;
use yii\widgets\Pjax;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Event $event */
/** @var yii\data\ActiveDataProvider $candidatesProvider */
/** @var yii\data\ActiveDataProvider $evaluatorsProvider */
/** @var string $search */

$this->registerJsFile('@web/js/event-evaluator.js', ['depends' => [JqueryAsset::class]]);
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
            <div class="card-header py-3 bg-primary d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">Disponíveis</h6>
            </div>
            <div class="card-body">

                <?php Pjax::begin(['id' => 'pjax-candidates', 'enablePushState' => false, 'timeout' => 5000]); ?>

                <div class="mb-3">
                    <form action="<?= Url::to(['index', 'event_id' => $event->id]) ?>" method="get" data-pjax="1" class="form-inline w-100">
                        <div class="input-group w-100">
                            <input type="text" name="search" class="form-control bg-light border-0 small"
                                   placeholder="Pesquisar nome..."
                                   value="<?= Html::encode($search ?? '') ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                                <?php if (!empty($search)): ?>
                                    <a href="<?= Url::to(['index', 'event_id' => $event->id]) ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>

                <?= GridView::widget([
                        'dataProvider' => $candidatesProvider,
                        'summary' => '',
                        'emptyText' => 'Nenhum usuário encontrado.',
                        'columns' => [
                                'username',
                                [
                                        'class' => 'yii\grid\ActionColumn',
                                        'template' => '{add}',
                                        'header' => 'Ação',
                                        'contentOptions' => ['style' => 'width: 120px; text-align: center;'],
                                        'buttons' => [
                                                'add' => function ($url, $model) use ($event) {
                                                    return Html::a('<i class="fas fa-plus"></i> Add',
                                                            ['event-evaluator/add', 'event_id' => $event->id, 'user_id' => $model->id],
                                                            [
                                                                    'class' => 'btn btn-sm btn-success ajax-action w-100',
                                                                    'title' => 'Adicionar',
                                                                    'style' => 'font-weight: bold;',
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
                        'emptyText' => 'Ainda sem avaliadores.',
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
                                                                    'style' => 'font-weight: bold;',
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