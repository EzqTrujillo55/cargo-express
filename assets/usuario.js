
$(document).ready(function () {

    


    $(document).on("click", ".edit-usuario", function () {
        var id = $(this).data('id');
        dump('modal show');
        console.log(id);
        $("#id").val(id);
        $(".new-usuario").modal({
    		backdrop: 'static',
    		keyboard: false
		});
    });
    
       $(document).on("click", ".resetea-password-usuario", function () {
        var id = $(this).data('id');
        dump('modal show');
        if (!empty(id)) {
            //$(".modal-title").html(jslang.update_driver);
            callAjax("reseteaPasswordUsuario", "id=" + id);
        }
    });


    $('.new-usuario').on('hide.bs.modal', function (e) {
        $("#id").val('');
        clearFormElements("#frm");
    });
    $('.new-usuario').on('show.bs.modal', function (e) {

        //$(".modal-title").html(jslang.add_driver);
        if (!empty($("#id").val())) {
            //$(".modal-title").html(jslang.update_driver);
            callAjax("getEditUsuario", "id=" + $("#id").val(), $("#frm .orange-button"));
        }
    });


    $(document).on("click", ".add-new-usuario", function () {

        if (account_status == "expired") {
            nAlert(jslang.account_expired, "warning");
        } else {
            $(".id").val('');
            $(".new-usuario").modal({
    		backdrop: 'static',
    		keyboard: false
		});
        }
    });




});







