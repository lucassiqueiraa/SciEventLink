<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var bool $isAdmin */

// Recebemos todas, mas usamos só as que fazem sentido para o papel
$this->title = $isAdmin ? 'Painel Administrativo' : 'Painel do Organizador';
?>

<div class="site-index">

    <?php if ($isAdmin): ?>

        <div class="jumbotron text-center bg-light p-4 mb-4 rounded-3">
            <h1 class="display-4">Olá, Administrador!</h1>
            <p class="lead">Painel Geral do SciEventLink</p>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2 border-0 border-start border-primary border-5">
                    <div class="card-body">
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalParticipants ?></div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Participantes</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2 border-0 border-start border-info border-5">
                    <div class="card-body">
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalEvents ?></div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Eventos</div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>

        <div class="jumbotron text-center bg-transparent mb-4">
            <h1 class="display-4">Bem-vindo!</h1>
            <p class="lead">Gestão dos seus eventos.</p>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header"><i class="fas fa-calendar-alt"></i> Meus Eventos</div>
                    <div class="card-body">
                        <h2 class="card-title display-4"><?= $myEvents ?></h2>
                        <p class="card-text">Eventos sob sua gestão.</p>
                        <?= Html::a('Gerir Eventos', ['event/index'], ['class' => 'btn btn-light btn-sm text-primary']) ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header"><i class="fas fa-clock"></i> Minhas Sessões</div>
                    <div class="card-body">
                        <h2 class="card-title display-4"><?= $mySessions ?></h2>
                        <p class="card-text">Total na agenda.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header"><i class="fas fa-door-open"></i> Meus Locais</div>
                    <div class="card-body">
                        <h2 class="card-title display-4"><?= $myVenues ?></h2>
                        <p class="card-text">Espaços configurados.</p>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>