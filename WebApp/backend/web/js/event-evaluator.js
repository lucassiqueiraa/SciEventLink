/**
 * Script para gestão de avaliadores (Adicionar/Remover via AJAX)
 */
$(document).ready(function() {

    // Usamos 'body' com delegação de eventos para funcionar mesmo após o Pjax recarregar a grelha
    $('body').on('click', '.ajax-action', function(e) {
        e.preventDefault(); // Impede o link de abrir uma nova página

        var btn = $(this);
        var url = btn.attr('href');

        // Evita cliques duplos se o botão já estiver desativado
        if (btn.hasClass('disabled')) return false;

        // Guarda o conteúdo original (ícone/texto) para restaurar se der erro
        if (!btn.data('original-content')) {
            btn.data('original-content', btn.html());
        }

        // Estado de Loading
        btn.addClass('disabled');
        btn.html('<i class="fas fa-spinner fa-spin"></i>');

        // Prepara os dados de segurança (CSRF Token) do Yii2
        var data = {};
        var csrfParam = $('meta[name="csrf-param"]').attr("content");
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        if (csrfParam) {
            data[csrfParam] = csrfToken;
        }

        console.log('Enviando pedido para: ' + url);

        // Envia o pedido AJAX
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    // Atualiza as duas tabelas sem recarregar a página
                    $.pjax.reload({container: '#pjax-candidates', async: false});
                    $.pjax.reload({container: '#pjax-evaluators', async: false});
                } else {
                    alert(response.message || 'Erro ao processar.');
                    resetBtn(btn);
                }
            },
            error: function(xhr) {
                // Erro 405, 500, etc.
                alert('Erro: ' + xhr.status + ' ' + xhr.statusText);
                console.error(xhr.responseText);
                resetBtn(btn);
            }
        });

        return false;
    });

    // Função auxiliar para restaurar o botão
    function resetBtn(btn) {
        btn.removeClass('disabled');
        btn.html(btn.data('original-content'));
    }
});