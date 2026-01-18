document.addEventListener('DOMContentLoaded', function() {

    const API_URL = '/scieventlink/WebApp/backend/web/api/checkin/validate?code=';

    let isScanning = true;
    let html5QrcodeScanner = null;

    /**
     * Main function that handles scanner reading
     */
    function onScanSuccess(decodedText, decodedResult) {
        if (!isScanning) return;

        isScanning = false;

        let ticketId = decodedText;

        if (decodedText.startsWith("BILHETE-")) {
            let parts = decodedText.split('-');
            if (parts[1]) {
                ticketId = parts[1];
                console.log("Código limpo: " + ticketId);
            }
        }

        validateTicket(ticketId);
    }

    function onScanFailure(error) {
    }

    /**
     * Validate the ticket in the API
     */
    async function validateTicket(code) {
        Swal.fire({
            title: 'A validar...',
            text: 'Aguarde um momento',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading() }
        });

        try {
            const response = await fetch(API_URL + code);

            const data = await response.json();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Entrada Permitida!',
                    text: `Participante: ${data.participant}`,
                    timer: 2000,
                    showConfirmButton: false
                });

                logScan(`✅ Bilhete #${code} - ${data.participant}`);

            } else {
                await Swal.fire({
                    icon: 'error',
                    title: 'Acesso Negado',
                    text: data.message,
                    footer: data.checkin_time ? `Entrou às: ${data.checkin_time}` : ''
                });

                logScan(`❌ Bilhete #${code} - ${data.message}`);
            }

        } catch (error) {
            console.error('Erro Técnico:', error);
            await Swal.fire('Erro', 'Falha na conexão com a API', 'error');

        } finally {
            isScanning = true;
        }
    }

    /**
     * Records in the page history in a SECURE manner
     */
    function logScan(message) {
        const log = document.getElementById('scan-log');
        if (!log) return;

        const time = new Date().toLocaleTimeString();

        const newEntry = document.createElement('div');

        const timeSpan = document.createElement('small');
        timeSpan.textContent = time + ' - ';

        const messageNode = document.createTextNode(message);

        newEntry.appendChild(timeSpan);
        newEntry.appendChild(messageNode);

        log.prepend(newEntry);
    }

    /**
     * Function for manual button
     */
    function validateManual() {
        const input = document.getElementById('manual-code');
        const code = input.value;
        if(code) {
            validateTicket(code);
            input.value = '';
        }
    }

    if (document.getElementById('reader')) {
        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: { width: 250, height: 250 } },
            false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    const btnManual = document.querySelector('#manual-btn');L
    if (btnManual) {
        btnManual.addEventListener('click', validateManual);
    }
});