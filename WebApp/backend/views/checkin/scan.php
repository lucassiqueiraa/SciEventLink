<?php

/** @var yii\web\View $this */

use yii\web\JqueryAsset;

$this->title = 'Validar Bilhetes';

$this->registerJsFile('@web/js/validate-ticket.js', ['depends' => [JqueryAsset::class]]);
?>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">

            <h2 class="mb-4"><i class="fas fa-qrcode"></i> Validar Entrada</h2>

            <div class="alert alert-info small">
                Aponte a câmara para o QR Code do bilhete.
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-2">
                    <div id="reader" style="width: 100%; border-radius: 8px; overflow: hidden;"></div>
                </div>
            </div>

            <div class="mt-4">
                <p class="text-muted small">A câmara não funciona? Digite o código:</p>
                <div class="input-group mb-3">
                    <input type="number" id="manual-code" class="form-control" placeholder="ID do Bilhete">
                    <button class="btn btn-primary" type="button" onclick="validateManual()">Validar Manualmente</button>
                </div>
            </div>

            <div id="scan-log" class="mt-4 text-start small text-muted"></div>

        </div>
    </div>
</div>