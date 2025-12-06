<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $sessionsDataProvider */
/** @var common\models\Event $model */

if ($model->isNewRecord) {
    echo '<div class="alert alert-info">Salve o evento para criar a agenda.</div>';
    return;
}
?>

<div class="sessions-list mt-3">
    <p>
        <?= Html::a('<i class="fas fa-plus"></i> Nova Sessão',
            [
                'session/create',
                'event_id' => $model->id,
                'returnUrl' => Url::to(['event/update', 'id' => $model->id])
            ],
            ['class' => 'btn btn-success btn-sm'])
        ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $sessionsDataProvider,
        'summary' => '',
        'columns' => [
            'title', // Título da Sessão
            'start_time',
            'end_time',


            [
                'label' => 'Sala',
                'value' => function($model) {
                    return $model->venue ? $model->venue->name : 'N/A';
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'session',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $sessionModel) use ($model) {
                        $url = Url::to(['session/update', 'id' => $sessionModel->id, 'returnUrl' => Url::to(['event/update', 'id' => $model->id])]);
                        return Html::a('Editar', $url, ['class' => 'btn btn-primary btn-xs']);
                    },
                    'delete' => function ($url, $sessionModel) use ($model) {
                        $url = Url::to(['session/delete', 'id' => $sessionModel->id, 'returnUrl' => Url::to(['event/update', 'id' => $model->id])]);
                        return Html::a('Apagar', $url, ['class' => 'btn btn-danger btn-xs', 'data-method' => 'post', 'data-confirm' => 'Certeza?']);
                    }
                ],
            ],
        ],
    ]); ?>
</div>