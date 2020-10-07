
$(document).ready(function () {

    $('.detalle-ruta-modal').on('show.bs.modal', function (e) {
        dump('modal totally loaded');
        dump($(".id_ruta").val());
        callAjax("getDetalleRuta", "id_ruta=" + $(".id_ruta").val());
    });

    /*edit*/
    $(document).on("click", ".administrar-ordenes", function () {
        var id_ruta = $(this).data('id');
        var id_mensajero = $(this).data('id_mensajero');
        var fecha_ruta = $(this).data('fecha_ruta');
        var detalle = $(this).data('detalle');
        var mensajero = $(this).data('mensajero');
        var foto_perfil_url = $(this).data('foto_perfil_url');
        var cv_url = $(this).data('cv_url');
        window.location.href = "/app/OrdenesPorRuta?id_ruta=" + id_ruta + "&id_mensajero=" + id_mensajero + "&fecha_ruta=" + fecha_ruta + "&detalle=" + detalle + "&foto_perfil_url=" + foto_perfil_url + "&cv_url=" + cv_url + "&mensajero=" + mensajero;
    });

    $(document).on("click", ".cambiar-estado", function () {
        var id_ruta = $(this).data('id_ruta');
        var modal_detalle = $(this).data('modal_detalle');
        var modal_new = $(this).data('modal_new');
        dump(id_ruta);
        $(".ruta-id").html(id_ruta);
        $(".ruta-id").val(id_ruta);
        $("." + modal_detalle).modal("hide");
        $("." + modal_new).modal("show");
    });



    $.validate({
        language: jsLanguageValidator,
        form: '#frm_changes_status_ruta',
        onError: function () {
        },
        onSuccess: function () {
            var params = $("#frm_changes_status_ruta").serialize();
            var action = $("#frm_changes_status_ruta #action").val();
            var button = $('#frm_changes_status_ruta button[type="submit"]');
            dump(button);
            callAjax(action, params, button);
            return false;
        }
    });


    $('.new-ruta').on('hide.bs.modal', function (e) {
        $("#id_ruta").val('');
        clearFormElements("#frm");
    });
    $('.new-ruta').on('show.bs.modal', function (e) {

        //$(".modal-title").html(jslang.add_driver);
        if (!empty($("#id_ruta").val())) {
            //$(".modal-title").html(jslang.update_driver);
            callAjax("getEditRuta", "id_ruta=" + $("#id_ruta").val(), $("#frm .orange-button"));
        }
    });


    $(document).on("click", ".add-new-ruta", function () {

        if (account_status == "expired") {
            nAlert(jslang.account_expired, "warning");
        } else {
            $(".id_ruta").val('');
            $("#id_mensajero").trigger("chosen:updated");
            $(".new-ruta").modal({
    		backdrop: 'static',
    		keyboard: false
		});
        }
    });

    if ($("#frm_table_rutas_busqueda").exists()) {
        jQuery.datetimepicker.setLocale('es');
        jQuery('#fecha_ruta_desde').datetimepicker({
            format: 'Y/m/d',
            onShow: function (ct) {
                this.setOptions({
                    maxDate: jQuery('#fecha_ruta_desde').val() ? jQuery('#fecha_ruta_desde').val() : false
                })
            },
            timepicker: false,
            readonly: true,
        });
        jQuery('#fecha_ruta_hasta').datetimepicker({
            format: 'Y/m/d',
            onShow: function (ct) {
                this.setOptions({
                    minDate: jQuery('#fecha_ruta_hasta').val() ? jQuery('#fecha_ruta_hasta').val() : false
                })
            },
            timepicker: false,
            readonly: true,
        });

        jQuery('#fecha_creacion_desde').datetimepicker({
            format: 'Y/m/d',
            onShow: function (ct) {
                this.setOptions({
                    maxDate: jQuery('#fecha_creacion_hasta').val() ? jQuery('#fecha_creacion_hasta').val() : false
                })
            },
            timepicker: false,
            readonly: true,
        });
        jQuery('#fecha_creacion_hasta').datetimepicker({
            format: 'Y/m/d',
            onShow: function (ct) {
                this.setOptions({
                    minDate: jQuery('#fecha_creacion_desde').val() ? jQuery('#fecha_creacion_desde').val() : false
                })
            },
            timepicker: false,
            readonly: true,
        });
    }


    $.validate({
        language: jsLanguageValidator,
        form: '#frm_table_rutas_busqueda',
        onError: function () {
        },
        onSuccess: function () {
            initTableRutasBusquedaAvanzada();
            $("#table_busqueda").modal('hide');
            clearFormElements("#frm_table_rutas_busqueda");
            return false;
        }
    });

    function initTableRutasBusquedaAvanzada()
    {
        var params = $("#frm_table_rutas_busqueda").serialize();
        var action = $("#frm_table_rutas_busqueda #action").val();
        params += "&language=" + language;
        if ($.fn.dataTable.isDataTable('#table_list')) {
            table = $('#table_list').DataTable();
            table.destroy();
        }

        data_table = $('#table_list').dataTable({
            "iDisplayLength": 20,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": ajax_url + "/" + action + "/?currentController=admin&" + params,
            "aaSorting": [[0, "DESC"]],
            "sPaginationType": "full_numbers",
"responsive": true,
            //"bFilter":false,            
            "bLengthChange": false,
            "oLanguage": {
                "sProcessing": "<p>Procesando.. <i class=\"fa fa-spinner fa-spin\"></i></p>"
            },
            "oLanguage": {
                "sEmptyTable": js_lang.tablet_1,
                "sInfo": js_lang.tablet_2,
                "sInfoEmpty": js_lang.tablet_3,
                "sInfoFiltered": js_lang.tablet_4,
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": js_lang.tablet_5,
                "sLoadingRecords": js_lang.tablet_6,
                "sProcessing": js_lang.tablet_7,
                "sSearch": js_lang.tablet_8,
                "sZeroRecords": js_lang.tablet_9,
                "oPaginate": {
                    "sFirst": js_lang.tablet_10,
                    "sLast": js_lang.tablet_11,
                    "sNext": js_lang.tablet_12,
                    "sPrevious": js_lang.tablet_13
                },
                "oAria": {
                    "sSortAscending": js_lang.tablet_14,
                    "sSortDescending": js_lang.tablet_15
                }
            },
            "fnInitComplete": function (oSettings, json) {

            },
            "fnDrawCallback": function (oSettings) {
                dump('fnDrawCallback');
            }
        });
        $("#codigo_orden_busqueda").val("");
        $('#codigo_orden_busqueda').focus();
    }






});







