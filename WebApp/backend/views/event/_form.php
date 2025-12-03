<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Tabs; // Importar o Widget de Abas

/** @var yii\web\View $this */
/** @var common\models\Event $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    // Definição das Abas
    echo Tabs::widget([
            'items' => [
                    [
                            'label' => 'Informações Gerais',
                            'content' => $this->render('_form_general', ['form' => $form, 'model' => $model]),
                            'active' => true
                    ],
                    [
                            'label' => 'Cronograma & Prazos',
                            'content' => $this->render('_form_dates', ['form' => $form, 'model' => $model]),
                    ],
                    [
                            'label' => 'Locais & Salas',
                            'content' => $this->render('_form_venues', [
                                    'model' => $model,
                                // Só passamos o dataProvider se ele existir (no create ele é null)
                                    'venuesDataProvider' => $venuesDataProvider ?? null
                            ]),
                    ],

                    [
                            'label' => 'Bilhetes (Modalidades)',
                            'content' => $this->render('_form_tickets', [
                                    'model' => $model,
                                    'ticketsDataProvider' => $ticketsDataProvider ?? null
                            ]),
                    ],

            ],
            'navType' => 'nav-tabs',
            'encodeLabels' => false,
    ]);
    ?>

    <div class="form-group mt-4">
        <?= Html::submitButton('Salvar Evento', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>