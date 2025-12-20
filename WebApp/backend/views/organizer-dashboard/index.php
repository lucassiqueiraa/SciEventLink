<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var int $myEvents */
/** @var int $mySessions */
/** @var int $myVenues */

$this->title = 'Painel do Organizador';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="organizer-dashboard">

    <div class="jumbotron bg-white shadow-sm pt-4 pb-4">
        <h1 class="display-5">Olá, Organizador! 👋</h1>
        <p class="lead">Aqui está o resumo da sua atividade no SciEventLink.</p>
        <hr class="my-4">
        <p>Pronto para lançar o próximo grande evento?</p>
        <a class="btn btn-primary btn-lg" href="<?= Url::to(['/eventos/create']) ?>" role="button">
            <i class="fas fa-plus-circle"></i> Criar Novo Evento
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-box mb-3 text-primary">
                        <i class="fas fa-calendar-check fa-3x"></i>
                    </div>
                    <h3 class="card-title display-4"><?= $myEvents ?></h3>
                    <p class="card-text text-muted">Meus Eventos Criados</p>
                    <a href="<?= Url::to(['/event/index']) ?>" class="btn btn-outline-primary btn-sm mt-2">Gerir Eventos</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-box mb-3 text-info">
                        <i class="fas fa-clock fa-3x"></i>
                    </div>
                    <h3 class="card-title display-4"><?= $mySessions ?></h3>
                    <p class="card-text text-muted">Sessões Agendadas</p>
                    <a href="<?= Url::to(['session/index'])?>" class="btn btn-outline-info btn-sm mt-2">Ver Sessões</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-box mb-3 text-warning">
                        <i class="fas fa-map-marker-alt fa-3x"></i>
                    </div>
                    <h3 class="card-title display-4"><?= $myVenues ?></h3>
                    <p class="card-text text-muted">Espaços Geridos</p>
                    <a href="<?= Url::to(['venue/index'])?>" class="btn btn-outline-warning btn-sm mt-2">Gerir Espaços</a>
                </div>
            </div>
        </div>
    </div>

</div>