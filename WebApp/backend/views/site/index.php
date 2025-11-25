<?php

/** @var yii\web\View $this */
/** @var int $totalParticipants */
/** @var int $totalOrganizers */
/** @var int $totalEvents */
/** @var int $suspendedUsers */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard Administrativo';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-light p-4 mb-4 rounded-3">
        <h1 class="display-4">Olá, Administrador!</h1>
        <p class="lead">Bem-vindo ao painel de controlo do <b>SciEventLink</b>. Aqui está o resumo da plataforma.</p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2 border-0 border-start border-primary border-5">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Participantes</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalParticipants ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-people-fill fa-2x text-gray-300" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2 border-0 border-start border-success border-5">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Organizadores</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalOrganizers ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-briefcase-fill fa-2x text-gray-300" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2 border-0 border-start border-info border-5">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Eventos Criados</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalEvents ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-calendar-event-fill fa-2x text-gray-300" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2 border-0 border-start border-warning border-5">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Contas Suspensas</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $suspendedUsers ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-person-x-fill fa-2x text-gray-300" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h3>Ações Rápidas</h3>
                <hr>
                <a href="<?= Url::to(['user/create']) ?>" class="btn btn-success btn-lg me-3">
                    <i class="bi bi-person-plus-fill"></i> Criar Novo Organizador
                </a>
                <a href="<?= Url::to(['user/index']) ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-people"></i> Gerir Utilizadores
                </a>
            </div>
        </div>

    </div>
</div>