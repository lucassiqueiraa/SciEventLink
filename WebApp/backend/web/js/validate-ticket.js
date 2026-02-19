document.addEventListener('DOMContentLoaded', function() {

    function logError(msg) {
        console.error(msg);
        document.getElementById('debug-log').innerText = "ERRO: " + msg;
    }

    console.log("1. Script de validação iniciado...");

    if (typeof Html5QrcodeScanner === 'undefined') {
        logError("A biblioteca Html5QrcodeScanner NÃO carregou. Verifique a internet ou o bloqueio do Brave.");
        return;
    }

    console.log("2. Biblioteca detetada. A iniciar scanner...");

    const scanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: { width: 250, height: 250 } },
        false
    );

    try {
        scanner.render(onScanSuccess, onScanFailure);
        console.log("3. Comando render() enviado.");
    } catch (e) {
        logError("Falha ao iniciar câmara: " + e.message);
    }

    // --- Funções de Lógica ---

    function onScanSuccess(decodedText, decodedResult) {
        scanner.clear();

        console.log(`Código lido: ${decodedText}`);

        let ticketId = decodedText;
        if (decodedText.startsWith("BILHETE-")) {
            ticketId = decodedText.split('-')[1];
        }

        validateTicket(ticketId);
    }

    function onScanFailure(error) {
    }

    async function validateTicket(code) {
        Swal.fire({ title: 'A processar...', didOpen: () => Swal.showLoading() });

        const url = `/scieventlink/backend/web/index.php/api/checkin/validate?code=${code}`;

        try {
            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                Swal.fire('Sucesso!', `Bem-vindo ${data.participant}`, 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Erro!', data.message, 'error')
                    .then(() => location.reload());
            }
        } catch (err) {
            Swal.fire('Erro Técnico', 'Falha na conexão', 'error');
            console.error(err);
        }
    }
});