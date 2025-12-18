<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper; // <--- A FERRAMENTA MÁGICA

/** @var yii\web\View $this */
/** @var common\models\User $model */

// Usa username que nunca falha
$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                        'confirm' => 'Tem certeza?',
                        'method' => 'post',
                ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">Dados de Conta</div>
                <div class="card-body">
                    <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                    'id',
                                    'username',
                                    'email:email',
                                    [
                                            'label' => 'Role',
                                        // PROTEÇÃO TOTAL: Se userProfile for null, escreve 'Erro/Vazio' e não quebra
                                            'value' => ArrayHelper::getValue($model, 'userProfile.role', '(Sem Perfil)'),
                                    ],
                                    'status',
                            ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">Dados Pessoais</div>
                <div class="card-body">
                    <?php if ($model->userProfile): ?>
                        <?= DetailView::widget([
                                'model' => $model->userProfile,
                                'attributes' => [
                                        'name',
                                        'nif',
                                        'phone',
                                ],
                        ]) ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            O sistema não carregou o perfil neste momento.
                            <br>
                            (Dados existem na BD mas a relação falhou)
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>