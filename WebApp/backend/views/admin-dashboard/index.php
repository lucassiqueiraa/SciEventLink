<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var int $totalParticipants */
/** @var int $totalOrganizers */
/** @var int $totalEvents */
/** @var int $suspendedUsers */

$this->title = 'Painel Administrativo';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-dashboard">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-tachometer-alt"></i> Visão Geral do Sistema</h1>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Participantes Ativos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalParticipants ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300" style="color: #28a745; opacity: 0.5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Organizadores</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalOrganizers ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300" style="color: #17a2b8; opacity: 0.5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Eventos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalEvents ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300" style="color: #007bff; opacity: 0.5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Contas Suspensas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $suspendedUsers ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300" style="color: #dc3545; opacity: 0.5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Atalhos de Gestão</h6>
                </div>
                <div class="card-body">
                    <p>O que deseja fazer hoje?</p>
                    <a href="<?= Url::to(['/user/create']) ?>" class="btn btn-primary btn-icon-split mb-2">
                        <span class="icon text-white-50"><i class="fas fa-user-plus"></i></span>
                        <span class="text">Criar Novo Utilizador</span>
                    </a>
                    <a href="<?= Url::to(['/user/index']) ?>" class="btn btn-info btn-icon-split mb-2">
                        <span class="icon text-white-50"><i class="fas fa-list"></i></span>
                        <span class="text">Listar Todos</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Área de Risco</h6>
                </div>
                <div class="card-body">
                    <p>Ações sensíveis do sistema.</p>
                    <a href="<?= Url::to(['/rbac/init']) ?>" class="btn btn-warning btn-sm" onclick="return confirm('Tem a certeza? Isto reseta as permissões!')">
                        <i class="fas fa-sync"></i> Re-inicializar Permissões (RBAC)
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>