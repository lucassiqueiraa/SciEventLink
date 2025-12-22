<?php
use yii\helpers\Html;

/** @var common\models\Registration $model */

$this->title = 'Finalizar Compra';
?>
<div class="site-checkout container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="my-0"><i class="fas fa-credit-card"></i> Pagamento Seguro</h3>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Resumo do Pedido</h5>
                    <p class="card-text">
                        <strong>Evento:</strong> <?= Html::encode($model->event->name) ?><br>
                        <strong>Bilhete:</strong> <?= Html::encode($model->ticketType->name) ?><br>
                    </p>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="text-muted">Total a Pagar:</span>
                        <h2 class="text-primary mb-0">
                            <?= Yii::$app->formatter->asCurrency($model->ticketType->price, 'EUR') ?>
                        </h2>
                    </div>

                    <?= Html::a('Pagar com Cartão', ['confirm-payment', 'id' => $model->id], [
                        'class' => 'btn btn-success btn-lg btn-block',
                        'data' => [
                            'method' => 'post', // POST é obrigatório
                        ],
                    ]) ?>

                    <div class="text-center mt-3">
                        <?= Html::a('Cancelar', ['index'], ['class' => 'text-muted']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>