<?php

use common\models\UserProfile;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\UserProfileSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Profiles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-profile-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Profile', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'name',
            'nif',
            'phone',
            //'role',
                [
                        'class' => ActionColumn::className(),
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                                'delete' => function ($url, $model, $key) {
                                    $isSuspended = $model->user->status !== 10; // 10 = Active

                                    $icon = $isSuspended ? 'check-lg' : 'trash'; // Ícone muda
                                    $color = $isSuspended ? 'btn-success' : 'btn-danger'; // Cor muda
                                    $title = $isSuspended ? 'Reativar' : 'Suspender';

                                    return Html::a('<i class="bi bi-' . $icon . '"></i>', $url, [
                                            'title' => $title,
                                            'class' => 'btn btn-sm ' . $color,
                                            'data-method' => 'post',
                                            'data-confirm' => 'Tem a certeza que deseja ' . strtolower($title) . ' este utilizador?',
                                    ]);
                                },
                        ],
                ],
        ],
    ]); ?>


</div>
