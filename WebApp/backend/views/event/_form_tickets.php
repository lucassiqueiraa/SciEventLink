<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $ticketsDataProvider */
/** @var common\models\Event $model */

if ($model->isNewRecord) {
    echo '<div class="alert alert-info">Salve o evento para adicionar bilhetes.</div>';
    return;
}
?>

<div class="tickets-list mt-3">
    <p>
        <?= Html::a('<i class="fas fa-plus"></i> Criar Modalidade',
            [
                'ticket-type/create',
                'event_id' => $model->id,
                'returnUrl' => Url::to(['event/update', 'id' => $model->id])
            ],
            ['class' => 'btn btn-success btn-sm'])
        ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $ticketsDataProvider,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'price:currency',

            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'ticket-type',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $ticketModel) use ($model) {
                        $url = Url::to(['ticket-type/update', 'id' => $ticketModel->id, 'returnUrl' => Url::to(['event/update', 'id' => $model->id])]);
                        return Html::a('Editar', $url, ['class' => 'btn btn-primary btn-xs']);
                    },
                    'delete' => function ($url, $ticketModel) use ($model) {
                        $url = Url::to(['ticket-type/delete', 'id' => $ticketModel->id, 'returnUrl' => Url::to(['event/update', 'id' => $model->id])]);
                        return Html::a('Apagar', $url, ['class' => 'btn btn-danger btn-xs', 'data-method' => 'post', 'data-confirm' => 'Certeza?']);
                    }
                ],
            ],
        ],
    ]); ?>
</div>