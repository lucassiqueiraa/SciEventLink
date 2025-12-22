<?php

use yii\bootstrap5\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\helpers\Url;

// Regista o JS do Bootstrap 5 para as abas funcionarem
BootstrapPluginAsset::register($this);

/** @var yii\web\View $this */
/** @var common\models\Event $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Eventos', 'url' => ['site/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-view">

    <div class="jumbotron p-4 p-md-5 text-white rounded bg-dark" style="background-image: linear-gradient(to right, #2c3e50, #4ca1af);">
        <div class="col-md-8 px-0">
            <h1 class="display-4 font-italic"><?= Html::encode($model->name) ?></h1>
            <p class="lead my-3">
                <i class="far fa-calendar-alt"></i> <?= Yii::$app->formatter->asDate($model->start_date, 'long') ?>
                até <?= Yii::$app->formatter->asDate($model->end_date, 'long') ?>
            </p>
            <a href="#bilhetes" class="btn btn-warning btn-lg font-weight-bold">
                <i class="fas fa-ticket-alt"></i> Garantir Inscrição
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs" id="eventTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="sobre-tab" data-bs-toggle="tab" href="#sobre" role="tab" aria-controls="sobre" aria-selected="true">Sobre o Evento</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="agenda-tab" data-bs-toggle="tab" href="#agenda" role="tab" aria-controls="agenda" aria-selected="false">Agenda / Sessões</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="bilhetes-tab" data-bs-toggle="tab" href="#bilhetes" role="tab" aria-controls="bilhetes" aria-selected="false">Bilhetes & Preços</a>
                </li>
            </ul>

            <div class="tab-content py-4" id="eventTabsContent">

                <div class="tab-pane fade show active" id="sobre" role="tabpanel" aria-labelledby="sobre-tab">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3>Detalhes</h3>
                            <p class="card-text" style="white-space: pre-line;">
                                <?= Html::encode($model->description) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="agenda" role="tabpanel" aria-labelledby="agenda-tab">
                    <?php if (empty($model->sessions)): ?>
                        <div class="alert alert-info">A agenda será divulgada em breve.</div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php
                            $currentDate = null;
                            foreach ($model->sessions as $session):
                                $sessionDate = Yii::$app->formatter->asDate($session->start_time, 'php:Y-m-d');
                                ?>

                                <?php if ($currentDate !== $sessionDate): ?>
                                <div class="list-group-item list-group-item-secondary font-weight-bold text-uppercase mt-3">
                                    <i class="far fa-calendar-check"></i>
                                    <?= Yii::$app->formatter->asDate($session->start_time, 'full') ?>
                                </div>
                                <?php $currentDate = $sessionDate; ?>
                            <?php endif; ?>

                                <div class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?= Html::encode($session->title) ?></h5>
                                        <small class="text-muted font-weight-bold">
                                            <i class="far fa-clock"></i>
                                            <?= Yii::$app->formatter->asTime($session->start_time, 'short') ?>
                                            às
                                            <?= Yii::$app->formatter->asTime($session->end_time, 'short') ?>
                                        </small>
                                    </div>

                                    <p class="mb-1 mt-2">
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                        <strong>Local:</strong>
                                        <?php if ($session->venue): ?>
                                            <?= Html::encode($session->venue->name) ?>
                                        <?php else: ?>
                                            <span class="text-success">Online / Remoto</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="bilhetes" role="tabpanel" aria-labelledby="bilhetes-tab">
                    <div class="row">
                        <?php if (empty($model->ticketTypes)): ?>
                            <div class="col-12"><div class="alert alert-warning">Não há bilhetes disponíveis no momento.</div></div>
                        <?php else: ?>
                            <?php foreach ($model->ticketTypes as $ticket): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-success h-100">
                                        <div class="card-header bg-transparent border-success font-weight-bold text-success">
                                            <?= Html::encode($ticket->name) ?>
                                        </div>
                                        <div class="card-body text-center d-flex flex-column">
                                            <h2 class="card-title pricing-card-title">
                                                <?= Yii::$app->formatter->asCurrency($ticket->price, 'EUR') ?>
                                            </h2>
                                            <p class="card-text mb-4"><?= Html::encode($ticket->name) ?></p>

                                            <div class="mt-auto">
                                                <?php
                                                if (Yii::$app->user->isGuest) {
                                                    echo Html::a('Fazer Login para Comprar', ['site/login'], [
                                                            'class' => 'btn btn-lg w-100 btn-outline-primary'
                                                    ]);
                                                } else {
                                                    // USUÁRIO LOGADO: Botão de Inscrever (POST)
                                                    echo Html::a('Inscrever', ['registration/create', 'ticket_type_id' => $ticket->id], [
                                                            'class' => 'btn btn-lg w-100 btn-outline-success',
                                                            'data' => [
                                                                    'method' => 'post',
                                                                    'confirm' => 'Tem a certeza que deseja inscrever-se neste bilhete?',
                                                            ],
                                                    ]);
                                                }
                                                ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>