<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $venuesDataProvider */
/** @var common\models\Event $model */

// Se o evento for novo (ainda não tem ID), não mostramos a lista
if ($model->isNewRecord) {
    echo '<div class="alert alert-info">Por favor, salve o evento primeiro para poder adicionar salas.</div>';
    return;
}
?>

<div class="venues-list mt-3">

    <p>
        <?= Html::a('<i class="fas fa-plus"></i> Adicionar Sala',
            [
                'venue/create',
                'event_id' => $model->id,
                'returnUrl' => Url::to(['event/update', 'id' => $model->id]) // <--- O Segredo
            ],
            ['class' => 'btn btn-success btn-sm']) // Removido target blank
        ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $venuesDataProvider,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'capacity',

            // BOTÕES DE AÇÃO (EDITAR / APAGAR)
            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'venue',
                'template' => '{update} {delete}',
                'buttons' => [
                        'update' => function ($url, $venueModel, $key) use ($model) {
                            // Adiciona returnUrl à URL padrão de update
                            $url = Url::to([
                                'venue/update',
                                'id' => $venueModel->id,
                                'returnUrl' => Url::to(['event/update', 'id' => $model->id])
                            ]);
                            return Html::a('Editar', $url, ['class' => 'btn btn-primary btn-sm']);
                        },
                        'delete' => function ($url, $venueModel, $key) use ($model) {
                            // Adiciona returnUrl à URL padrão de delete
                            $url = Url::to([
                                'venue/delete',
                                'id' => $venueModel->id,
                                'returnUrl' => Url::to(['event/update', 'id' => $model->id])
                            ]);
                            return Html::a('<i class="bi bi-trash"></i>', $url, [
                                'class' => 'btn btn-danger btn-sm',
                                'data-confirm' => 'Tem a certeza?',
                                'data-method' => 'post',
                            ]);
                    }
                ],
            ],
        ],
    ]); ?>

</div>