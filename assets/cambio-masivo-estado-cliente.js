
$(document).ready(function () {



    var buttonpressed;

    $('.submitbutton').click(function () {
        buttonpressed = $(this).attr('name')
    })


    $.validate({
        language: jsLanguageValidator,
        form: '#frm_table_ordenes_user',
        onError: function () {
        },
        onSuccess: function () {
            if (buttonpressed == 'imprimir-comprobantes')
            {
                var params = $("#frm_table_ordenes_user").serialize();
                window.open("/user/Comprobantes?" + params, '_blank');
            }

            buttonpressed = '';
            return false;
        }
    });



    $(document).on("click", ".imprimir-comprobante", function () {
        var orden_id = $(this).data('id');
        window.open("/user/Comprobante?orden_id=" + orden_id, '_blank');
    });



});

