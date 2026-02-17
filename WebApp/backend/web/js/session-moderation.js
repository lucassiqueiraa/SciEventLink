$(document).ready(function() {
    $.pjax.defaults.scrollTo = false;

    $('body').on('click', '.btn-moderation-action', function(e) {
        e.preventDefault();

        var $button = $(this);
        var url = $button.attr('href');
        var confirmMsg = $button.data('confirm-msg');

        if (confirmMsg && !confirm(confirmMsg)) {
            return false;
        }

        var originalContent = $button.html();
        $button.html('<i class="fas fa-spinner fa-spin"></i>').addClass('disabled');

        var postData = {};
        if (window.yii && yii.getCsrfParam()) {
            postData[yii.getCsrfParam()] = yii.getCsrfToken();
        }

        $.post(url, postData, function(data) {
            if (data.success) {
                $.pjax.reload({container: '#pjax-questions-container', async: true});
            } else {
                alert(data.message || 'Erro ao processar a ação.');
                $button.html(originalContent).removeClass('disabled');
            }
        }).fail(function() {
            alert('Erro de conexão.');
            $button.html(originalContent).removeClass('disabled');
        });
    });

    $('body').on('click', '#btn-refresh-questions', function(e) {
        e.preventDefault();
        var $icon = $(this).find('i');
        $icon.addClass('fa-spin');

        $.pjax.reload({container: '#pjax-questions-container'}).done(function() {
            $icon.removeClass('fa-spin');
        });
    });
});