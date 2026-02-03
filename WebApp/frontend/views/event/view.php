<?php

use common\models\EventEvaluators;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Article;

BootstrapPluginAsset::register($this);

/** @var yii\web\View $this */
/** @var common\models\Event $model */
/** @var common\models\Registration|null $userRegistration */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Eventos', 'url' => ['site/index']];
$this->params['breadcrumbs'][] = $this->title;

$hasPaid = false;
$userArticle = null;

$isEvaluator = $model->isEvaluator(Yii::$app->user->id);

if ($userRegistration) {
    if ($userRegistration->payment_status === 'paid' || $userRegistration->payment_status === 'confirmed') {
        $hasPaid = true;
    }
    $userArticle = Article::findOne(['registration_id' => $userRegistration->id]);
}
?>
<div class="event-view">

    <div class="jumbotron p-4 p-md-5 text-white rounded bg-dark" style="background-image: linear-gradient(to right, #2c3e50, #4ca1af);">
        <div class="col-md-8 px-0">
            <h1 class="display-4 font-italic"><?= Html::encode($model->name) ?></h1>
            <p class="lead my-3">
                <i class="far fa-calendar-alt"></i> <?= Yii::$app->formatter->asDate($model->start_date, 'long') ?>
                até <?= Yii::$app->formatter->asDate($model->end_date, 'long') ?>
            </p>

            <div class="mt-4">
                <?php if ($isEvaluator): ?>
                    <div class="mb-3">
                        <?= Html::a('<i class="fas fa-gavel"></i> Painel de Avaliação',
                                ['evaluator/index'],
                                ['class' => 'btn btn-light text-primary btn-lg font-weight-bold shadow border-primary']
                        ) ?>
                        <span class="badge bg-light text-dark ms-2">Você é Avaliador neste evento</span>
                    </div>
                <?php endif; ?>
                <?php if (!$userRegistration): ?>

                    <a href="#bilhetes" class="btn btn-warning btn-lg font-weight-bold">
                        <i class="fas fa-ticket-alt"></i> Garantir Inscrição
                    </a>

                <?php else: ?>

                    <?php if ($userArticle): ?>

                        <?= Html::a('<i class="fas fa-check-circle"></i> Ver Submissão',
                                ['article/update', 'id' => $userArticle->id],
                                [
                                        'class' => 'btn btn-info btn-lg font-weight-bold shadow text-white',
                                        'title' => 'Clique para ver ou editar o seu artigo'
                                ]
                        ) ?>
                        <div class="mt-2 text-light">
                            <small><i class="fas fa-info-circle"></i> Estado: <?= Html::encode($userArticle->status) ?></small>
                        </div>

                    <?php elseif ($hasPaid): ?>

                        <?= Html::a('<i class="fas fa-file-upload"></i> Submeter Artigo',
                                ['article/create', 'event_id' => $model->id],
                                ['class' => 'btn btn-success btn-lg font-weight-bold shadow']
                        ) ?>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary btn-lg" disabled>
                            <i class="fas fa-lock"></i> Pagamento Pendente
                        </button>
                        <small class="d-block mt-2 text-warning">
                            <i class="fas fa-exclamation-triangle"></i> Regularize o pagamento para submeter artigos.
                        </small>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
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
                            <div class="col-12"><div class="alert alert-warning">Não há bilhetes disponíveis.</div></div>
                        <?php else: ?>
                            <?php foreach ($model->ticketTypes as $ticket): ?>
                                <div class="col-md-4 mb-3">
                                    <?php
                                    $isMyTicket = ($userRegistration && $userRegistration->ticket_type_id === $ticket->id);
                                    $cardClass = $isMyTicket ? 'border-primary shadow' : 'border-success';
                                    ?>

                                    <div class="card <?= $cardClass ?> h-100">
                                        <div class="card-header bg-transparent border-success font-weight-bold text-success">
                                            <?= Html::encode($ticket->name) ?>
                                            <?php if ($isMyTicket): ?>
                                                <span class="badge bg-primary float-end">Meu Bilhete</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body text-center d-flex flex-column">
                                            <h2 class="card-title pricing-card-title">
                                                <?= Yii::$app->formatter->asCurrency($ticket->price, 'EUR') ?>
                                            </h2>
                                            <p class="card-text mb-4"><?= Html::encode($ticket->name) ?></p>

                                            <div class="mt-auto">
                                                <?php
                                                if (Yii::$app->user->isGuest) {
                                                    echo Html::a('Login para Comprar', ['site/login'], ['class' => 'btn btn-outline-primary w-100']);
                                                }
                                                elseif ($userRegistration) {
                                                    if ($isMyTicket) {
                                                        if ($userRegistration->payment_status === 'paid' || $userRegistration->payment_status === 'confirmed') {
                                                            echo '<button class="btn btn-success w-100 disabled"><i class="fas fa-check-circle"></i> Inscrição Confirmada</button>';
                                                        } else {
                                                            echo Html::a('<i class="fas fa-credit-card"></i> Pagar Agora',
                                                                    ['registration/checkout', 'id' => $userRegistration->id],
                                                                    ['class' => 'btn btn-warning w-100 font-weight-bold text-dark']
                                                            );
                                                        }
                                                    } else {
                                                        echo '<button class="btn btn-secondary w-100 disabled">Inscrito noutra categoria</button>';
                                                    }
                                                }
                                                else {
                                                    echo Html::a('Inscrever', ['registration/create', 'ticket_type_id' => $ticket->id], [
                                                            'class' => 'btn btn-lg w-100 btn-outline-success',
                                                            'data' => [
                                                                    'method' => 'post',
                                                                    'confirm' => 'Confirmar inscrição neste bilhete?',
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