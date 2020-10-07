
$(document).ready(function () {

    


    $(document).on("click", ".edit-mensajero", function () {
        var id_mensajero = $(this).data('id_mensajero');
        dump('modal show');
        $("#id_mensajero").val(id_mensajero);
        $(".new-mensajero").modal({
    		backdrop: 'static',
    		keyboard: false
		});
    });


    $('.new-mensajero').on('hide.bs.modal', function (e) {
        $("#id_mensajero").val('');
        $(".profile-photo").html("<p>" + jslang.profile_photo + "</p>");
        clearFormElements("#frm");
    });
    $('.new-mensajero').on('show.bs.modal', function (e) {

        //$(".modal-title").html(jslang.add_driver);
        if (!empty($("#id_mensajero").val())) {
            //$(".modal-title").html(jslang.update_driver);
            callAjax("getEditMensajero", "id_mensajero=" + $("#id_mensajero").val(), $("#frm .orange-button"));
        }
    });




    $(document).on("click", "#tipo_vehiculo", function () {
        var selected = $(this).val();
        switchTransportType(selected);
    });

    if ($("#upload-mensajero-photo").exists()) {
        var uploader = new ss.SimpleUpload({
            button: 'upload-mensajero-photo', // HTML element used as upload button
            url: ajax_url + "/uploadprofilephoto", // URL of server-side upload handler
            name: 'uploadfile', // Parameter name of the uploaded file
            responseType: 'json',
            allowedExtensions: ['jpeg', 'png', 'jpg', 'gif'],
            maxSize: 11024, // kilobytes
            onExtError: function (filename, extension) {
                nAlert("Invalid File extennsion", "warning");
            },
            onSizeError: function (filename, fileSize) {
                nAlert("Invalid File size", "warning");
            },
            onSubmit: function (filename, extension) {
                busy(true);
            },
            onComplete: function (filename, response) {
                dump(response);
                busy(false);
                if (response.code == 1) {
                    nAlert(response.msg, "success");
                    $("#foto_perfil").val(filename);
                    var image = '<img src="' + response.details + '" />';
                    $(".profile-photo").html(image);
                } else {
                    nAlert(response.msg, "warning");
                }
            }
        });
    }

    if ($("#upload-mensajero-cv").exists()) {
        var uploader = new ss.SimpleUpload({
            button: 'upload-mensajero-cv', // HTML element used as upload button
            url: ajax_url + "/uploadcv", // URL of server-side upload handler
            name: 'uploadfile', // Parameter name of the uploaded file
            responseType: 'json',
            allowedExtensions: ['pdf', 'docx', 'doc'],
            maxSize: 11024, // kilobytes
            onExtError: function (filename, extension) {
                nAlert("Invalid File extennsion", "warning");
            },
            onSizeError: function (filename, fileSize) {
                nAlert("Invalid File size", "warning");
            },
            onSubmit: function (filename, extension) {
                busy(true);
            },
            onComplete: function (filename, response) {
                dump(response);
                busy(false);
                if (response.code == 1) {
                    nAlert(response.msg, "success");
                    $("#cv").val(filename);
                    var cv = '<a  target="_blank" href="' + response.details + '"> Descargar CV</a>';
                    $(".cv").html(cv);
                } else {
                    nAlert(response.msg, "warning");
                }
            }
        });
    }

    $(document).on("click", ".add-new-mensajero", function () {

        if (account_status == "expired") {
            nAlert(jslang.account_expired, "warning");
        } else {
            $(".id_mensajero").val('');
            $(".new-mensajero").modal({
    		backdrop: 'static',
    		keyboard: false
		});
        }
    });




});







