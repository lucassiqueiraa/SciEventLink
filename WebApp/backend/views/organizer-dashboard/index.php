<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
// Novas variáveis
/** @var float $myRevenue */
/** @var int $myAttendees */
/** @var int $myEvents */
/** @var int $mySessions */
/** @var int $myVenues */

$this->title = 'Painel do Organizador';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="organizer-dashboard">

    <div class="jumbotron bg-white shadow-sm pt-4 pb-4">
        <h1 class="display-5">Olá, Organizador! 👋</h1>
        <p class="lead">Aqui está o resumo financeiro e operacional dos seus eventos.</p>
        <hr class="my-4">

        <a class="btn btn-primary btn-lg shadow" href="<?= Url::to(['/event/create']) ?>" role="button"> <i class="fas fa-plus-circle"></i> Criar Novo Evento
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card border-success shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-success text-uppercase font-weight-bold mb-1">Minha Receita Estimada</h6>
                        <h2 class="display-4 font-weight-bold text-dark mb-0">
                            <?= Yii::$app->formatter->asCurrency($myRevenue, 'EUR') ?>
                        </h2>
                    </div>
                    <div class="text-success opacity-50">
                        <i class="fas fa-wallet fa-4x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card border-info shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-info text-uppercase font-weight-bold mb-1">Total de Inscritos</h6>
                        <h2 class="display-4 font-weight-bold text-dark mb-0"><?= $myAttendees ?></h2>
                    </div>
                    <div class="text-info opacity-50">
                        <i class="fas fa-users fa-4x"></i>
                    </div>
                </div>
            </div>
        </div>
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