
$(document).ready(function () {

    


    $(document).on("click", ".edit-cliente", function () {
        var id_cliente = $(this).data('id');
        dump('modal show');
        console.log(id_cliente);
        $("#id_cliente").val(id_cliente);
        $(".new-cliente").modal({
    		backdrop: 'static',
    		keyboard: false
		});
    });
    
       $(document).on("click", ".resetea-password-cliente", function () {
        var id_cliente = $(this).data('id');
        dump('modal show');
        if (!empty(id_cliente)) {
            //$(".modal-title").html(jslang.update_driver);
            callAjax("reseteaPasswordCliente", "id_cliente=" + id_cliente);
        }
    });


    $('.new-cliente').on('hide.bs.modal', function (e) {
        $("#id_cliente").val('');
        clearFormElements("#frm");
    });
    $('.new-cliente').on('show.bs.modal', function (e) {

        //$(".modal-title").html(jslang.add_driver);
        if (!empty($("#id_cliente").val())) {
            //$(".modal-title").html(jslang.update_driver);
            callAjax("getEditCliente", "id_cliente=" + $("#id_cliente").val(), $("#frm .orange-button"));
        }
    });


    $(document).on("click", ".add-new-cliente", function () {

        if (account_status == "expired") {
            nAlert(jslang.account_expired, "warning");
        } else {
            $(".id_cliente").val('');
            $(".new-cliente").modal({
    		backdrop: 'static',
    		keyboard: false
		});
        }
    });




});







