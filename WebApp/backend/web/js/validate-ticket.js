document.addEventListener('DOMContentLoaded', function() {

    // Função para mostrar erros na tela (caso a consola esteja escondida)
    function logError(msg) {
        console.error(msg);
        document.getElementById('debug-log').innerText = "ERRO: " + msg;
    }

    console.log("1. Script de validação iniciado...");

    // 1. Verifica se a biblioteca carregou
    if (typeof Html5QrcodeScanner === 'undefined') {
        logError("A biblioteca Html5QrcodeScanner NÃO carregou. Verifique a internet ou o bloqueio do Brave.");
        return;
    }

    console.log("2. Biblioteca detetada. A iniciar scanner...");

    // 2. Configura o Scanner
    const scanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: { width: 250, height: 250 } },
        false
    );

    // 3. O PASSO CRÍTICO: Mandar renderizar (Iniciar a câmara)
    try {
        scanner.render(onScanSuccess, onScanFailure);
        console.log("3. Comando render() enviado.");
    } catch (e) {
        logError("Falha ao iniciar câmara: " + e.message);
    }

    // --- Funções de Lógica ---

    function onScanSuccess(decodedText, decodedResult) {
        // Pausa para não ler 1000 vezes
        scanner.clear();

        console.log(`Código lido: ${decodedText}`);

        let ticketId = decodedText;
        if (decodedText.startsWith("BILHETE-")) {
            ticketId = decodedText.split('-')[1];
        }

        validateTicket(ticketId);
    }

    function onScanFailure(error) {
        // Não fazer nada, é normal falhar enquanto procura
    }

    // Simulação da função de validar (ajusta a URL se necessário)
    async function validateTicket(code) {
        Swal.fire({ title: 'A processar...', didOpen: () => Swal.showLoading() });

        // ATENÇÃO: Confirma se esta URL bate certo com o teu backend
        const url = `/scieventlink/backend/web/index.php?r=api/checkin/validate&code=${code}`;

        try {
            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                Swal.fire('Sucesso!', `Bem-vindo ${data.participant}`, 'success')
                    .then(() => location.reload()); // Recarrega para ler o próximo
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