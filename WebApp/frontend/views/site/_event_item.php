<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var common\models\Event $model */
?>

<div class="card h-100 shadow-sm">
    <img src="https://placehold.co/600x400?text=<?= Html::encode($model->name) ?>" class="card-img-top" alt="...">

    <div class="card-body d-flex flex-column">
        <h5 class="card-title">
            <?= Html::encode($model->name) ?>
        </h5>

        <h6 class="card-subtitle mb-2 text-muted">
            <i class="far fa-calendar-alt"></i>
            <?= Yii::$app->formatter->asDate($model->start_date, 'php:d/m/Y') ?>
        </h6>

        <p class="card-text text-truncate" style="max-height: 3em;">
            <?= Html::encode($model->description) ?>
        </p>

        <div class="mt-auto">
            <a href="<?= Url::to(['event/view', 'id' => $model->id]) ?>" class="btn btn-primary btn-block w-100">
                Ver Detalhes <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>