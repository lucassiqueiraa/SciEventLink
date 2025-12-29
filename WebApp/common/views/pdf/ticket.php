<?php
use yii\helpers\Html;

/* @var $model common\models\Registration */

// --- FUNÇÃO PARA CONVERTER IMAGEM EM BASE64 ---
// Isso resolve o problema do "X" vermelho em conexões HTTPS
function imageToBase64($url) {
    try {
        // Tenta baixar a imagem
        $imageData = @file_get_contents($url);
        if ($imageData !== false) {
            // Se conseguiu, converte para base64
            $base64 = base64_encode($imageData);
            // Retorna o formato pronto para o src da tag img
            return 'data:image/png;base64,' . $base64;
        }
    } catch (\Exception $e) {
        // Silencia erros
    }
    return '';
}

// 1. Gerar URL do QR Code
$qrData = "BILHETE-{$model->id}-EVENTO-{$model->event_id}-" . md5($model->registration_date);
$qrUrlExternal = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);

// 2. Converter para Base64 (AQUI ESTÁ O SEGREDO DO SUCESSO)
$qrSrc = imageToBase64($qrUrlExternal);

// 3. Ícone de Calendário (Também em Base64 para garantir)
$calendarIconSrc = imageToBase64("https://img.icons8.com/ios-glyphs/30/000000/calendar.png");

// Se a conversão falhar, usamos um placeholder transparente ou texto
if (empty($qrSrc)) $qrSrc = $qrUrlExternal;
?>

<div style="font-family: 'Helvetica', sans-serif; color: #333; padding: 20px; border: 2px dashed #007bff;">

    <table width="100%" style="border-bottom: 2px solid #eee; padding-bottom: 10px;">
        <tr>
            <td width="70%">
                <h1 style="color: #007bff; margin: 0; text-transform: uppercase;">SciEvent Link</h1>
                <p style="margin: 5px 0; color: #777;">Bilhete de Entrada Oficial</p>
            </td>
            <td width="30%" style="text-align: right;">
                <h2 style="margin: 0; color: #444;">#<?= str_pad($model->id, 6, '0', STR_PAD_LEFT) ?></h2>
                <div style="margin-top:5px; color: green; font-weight: bold; border: 1px solid green; display: inline-block; padding: 5px; font-size: 12px;">
                    <?= strtoupper($model->payment_status) ?>
                </div>
            </td>
        </tr>
    </table>

    <br>

    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
        <h2 style="margin-top: 0; margin-bottom: 10px;"><?= Html::encode($model->event->name) ?></h2>
        <p style="font-size: 14px; line-height: 1.5;">
            <img src="<?= $calendarIconSrc ?>" width="12" style="vertical-align: middle; margin-right: 5px;">
            <strong>Data:</strong> <?= Yii::$app->formatter->asDate($model->event->start_date, 'long') ?><br>
            <span style="display:inline-block; width: 16px;"></span> <strong>Local:</strong> SciEvent Center, Lisboa (Auditório Principal)
        </p>
    </div>

    <br>

    <table width="100%">
        <tr>
            <td width="65%" valign="top">
                <p style="color: #999; font-size: 12px; margin-bottom: 2px; text-transform: uppercase;">Participante</p>
                <h3 style="margin-top: 0; margin-bottom: 5px;">
                    <?= Html::encode($model->user->userProfile->name ?? $model->user->username) ?>
                </h3>
                <p style="color: #666; font-size: 12px; margin-top: 0;"><?= Html::encode($model->user->email) ?></p>

                <br>

                <p style="color: #999; font-size: 12px; margin-bottom: 2px; text-transform: uppercase;">Tipo de Bilhete</p>
                <h3 style="margin-top: 0; margin-bottom: 5px;">
                    <?= Html::encode($model->ticketType->name) ?>
                </h3>
                <p style="font-size: 14px; margin-top: 0;">
                    Preço: <?= Yii::$app->formatter->asCurrency($model->ticketType->price, 'EUR') ?>
                </p>
            </td>

            <td width="35%" align="center" valign="middle" style="border-left: 1px solid #eee;">
                <?php if (!empty($qrSrc)): ?>
                    <img src="<?= $qrSrc ?>" alt="QR Code" style="width: 120px; height: 120px;">
                <?php else: ?>
                    <div style="width: 120px; height: 120px; border: 1px solid #ccc; color: #red; display: flex; align-items: center; justify-content: center;">
                        Erro QR
                    </div>
                <?php endif; ?>
                <p style="font-size: 10px; color: #999; margin-top: 5px;">Apresente este código na entrada.</p>
            </td>
        </tr>
    </table>

    <div style="margin-top: 50px; text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 10px;">
        &copy; <?= date('Y') ?> SciEventLink. Este bilhete é pessoal e intransmissível.<br>
        Emitido em: <?= date('d/m/Y H:i:s') ?>
    </div>

</div>