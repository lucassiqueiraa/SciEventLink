<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Event $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// Helper simples para o Status
$statusBadge = $model->status === 'published'
        ? '<span class="badge badge-success">Publicado</span>'
        : '<span class="badge badge-secondary">' . $model->status . '</span>';
?>

<div class="event-view">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-primary mb-1"><?= Html::encode($this->title) ?></h1>
            <?= $statusBadge ?>
        </div>
        <div>
            <?= Html::a('<i class="fas fa-edit" style="margin-right: 5px;"></i> Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-primary']) ?>
            <?= Html::a('<i class="fas fa-trash" style="margin-right: 5px;"></i>', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-outline-danger ml-1',
                    'data' => [
                            'confirm' => 'Tem a certeza?',
                            'method' => 'post',
                    ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">

            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-secondary">
                        <i class="fas fa-info-circle" style="margin-right: 15px;"></i>Sobre o Evento
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text lead" style="font-size: 1.1rem;">
                        <?= empty($model->description) ? '<em>Sem descrição.</em>' : nl2br(Html::encode($model->description)) ?>
                    </p>
                </div>
            </div>

            <h5 class="text-secondary mb-3">
                <i class="fas fa-th" style="margin-right: 15px;"></i>Gestão Rápida
            </h5>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="<?= Url::to(['venue/index', 'VenueSearch[event_id]' => $model->id]) ?>" class="text-decoration-none">
                        <div class="card h-100 border-info text-center shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-building fa-2x text-info mb-2"></i>
                                <h6 class="text-dark font-weight-bold">Salas</h6>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3 mb-3">
                    <a href="<?= Url::to(['session/index', 'SessionSearch[event_id]' => $model->id]) ?>" class="text-decoration-none">
                        <div class="card h-100 border-warning text-center shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-calendar-alt fa-2x text-warning mb-2"></i>
                                <h6 class="text-dark font-weight-bold">Agenda</h6>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3 mb-3">
                    <a href="<?= Url::to(['event-evaluator/index', 'event_id' => $model->id]) ?>" class="text-decoration-none">
                        <div class="card h-100 border-success text-center shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-user-graduate fa-2x text-success mb-2"></i>
                                <h6 class="text-dark font-weight-bold">Avaliadores</h6>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3 mb-3">
                    <a href="<?= Url::to(['article/index']) ?>" class="text-decoration-none">
                        <div class="card h-100 border-primary text-center shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                                <h6 class="text-dark font-weight-bold">Submissões</h6>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow bg-white border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="far fa-calendar-alt" style="margin-right: 15px;"></i>Cronograma
                    </h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center">
                        <div style="margin-right: 25px;">
                            <i class="fas fa-flag-checkered fa-2x text-success"></i>
                        </div>
                        <div>
                            <strong class="d-block text-dark">Data do Evento</strong>
                            <small class="text-muted">
                                <?= Yii::$app->formatter->asDate($model->start_date, 'php:d/m/Y') ?> até
                                <?= Yii::$app->formatter->asDate($model->end_date, 'php:d/m/Y') ?>
                            </small>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center">
                        <div style="margin-right: 25px;">
                            <i class="fas fa-upload fa-2x text-primary"></i>
                        </div>
                        <div>
                            <strong class="d-block text-dark">Submissões</strong>
                            <span class="text-danger font-weight-bold">
                                Até <?= Yii::$app->formatter->asDate($model->submission_deadline, 'php:d/m/Y') ?>
                            </span>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center">
                        <div style="margin-right: 25px;">
                            <i class="fas fa-gavel fa-2x text-warning"></i>
                        </div>
                        <div>
                            <strong class="d-block text-dark">Avaliações</strong>
                            <span class="text-danger font-weight-bold">
                                Até <?= Yii::$app->formatter->asDate($model->evaluation_deadline, 'php:d/m/Y') ?>
                            </span>
                        </div>
                    </li>
                </ul>
                <div class="card-footer text-center text-muted small">
                    ID do Evento: #<?= $model->id ?>
                </div>
            </div>
        </div>
    </div>
</div>