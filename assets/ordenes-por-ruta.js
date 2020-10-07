
$(document).ready(function () {

    $(document).on("click", ".asignar-orden", function () {
        let paramsSearch = new URLSearchParams(window.location.search);
        let id_ruta = paramsSearch.get('id_ruta');
        if (account_status == "expired") {
            nAlert(jslang.account_expired, "warning");
        } else {
            $(".id_ruta").val(id_ruta);
            $("#orden_id").trigger("chosen:updated");
            $(".asignar-ruta-orden-modal").modal({
    		backdrop: 'static',
    		keyboard: false
		});
        }
    });

    if ($("#frm_table_ordenes_por_ruta").exists()) {

        $("#codigo_orden_busqueda").focus();

        initTableOrdenesPorRuta();
        let paramsSearch = new URLSearchParams(window.location.search);
        let fecha_ruta = paramsSearch.get('fecha_ruta');
        let detalle = paramsSearch.get('detalle');
        let mensajero = paramsSearch.get('mensajero');
        let foto_perfil_url = paramsSearch.get('foto_perfil_url');
        let cv_url = paramsSearch.get('cv_url');

        $(".mensajero").html(mensajero);
        $(".fecha_ruta").html(fecha_ruta);
        $(".detalle").html(detalle);
        $(".datos").html(foto_perfil_url + "<br/>" + cv_url);

        jQuery.datetimepicker.setLocale('es');



        jQuery('#fecha_envio_desde').datetimepicker({
            format: 'Y/m/d',
            onShow: function (ct) {
                this.setOptions({
                    maxDate: jQuery('#fecha_envio_hasta').val() ? jQuery('#fecha_envio_hasta').val() : false
                })
            },
            timepicker: false,
            readonly: true,
        });
        jQuery('#fecha_envio_hasta').datetimepicker({
            format: 'Y/m/d',
            onShow: function (ct) {
                this.setOptions({
                    minDate: jQuery('#fecha_envio_desde').val() ? jQuery('#fecha_envio_desde').val() : false
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

        jQuery('#fecha_entrega_desde').datetimepicker({
            format: 'Y/m/d',
            onShow: function (ct) {
                this.setOptions({
                    maxDate: jQuery('#fecha_entrega_desde').val() ? jQuery('#fecha_entrega_desde').val() : false
                })
            },
            timepicker: false,
            readonly: true,
        });
        jQuery('#fecha_entrega_hasta').datetimepicker({
            format: 'Y/m/d',
            onShow: function (ct) {
                this.setOptions({
                    minDate: jQuery('#fecha_entrega_hasta').val() ? jQuery('#fecha_entrega_hasta').val() : false
                })
            },
            timepicker: false,
            readonly: true,
        });


    }

    $(document).on("click", ".cambiar-estado-orden", function () {
        var orden_id = $(this).data('id');
        var codigo_orden = $(this).data('codigo_orden');
        var modal_detalle = $(this).data('modal_detalle');
        var modal_new = $(this).data('modal_new');
        dump(orden_id);
        $(".codigo_orden").html(codigo_orden);
        $(".orden_id").val(orden_id);
        $("." + modal_detalle).modal("hide");
        $("." + modal_new).modal("show");
    });

    $(document).on("click", ".migrar-de-ruta", function () {
        var orden_id = $(this).data('id');
        var codigo_orden = $(this).data('codigo_orden');
        var modal_detalle = $(this).data('modal_detalle');
        var modal_new = $(this).data('modal_new');
        dump(orden_id);
        $(".codigo_orden").html(codigo_orden);
        $(".orden_id").val(orden_id);
        $("." + modal_detalle).modal("hide");
        $("." + modal_new).modal("show");
    });

    $(document).on("click", ".cambiar-fecha-entrega", function () {
        var orden_id = $(this).data('id');
        var codigo_orden = $(this).data('codigo_orden');
        var modal_detalle = $(this).data('modal_detalle');
        var modal_new = $(this).data('modal_new');
        dump(orden_id);
        $(".codigo_orden").html(codigo_orden);
        $(".orden_id").val(orden_id);
        $("." + modal_detalle).modal("hide");
        $("." + modal_new).modal("show");
    });

    $(document).on("click", ".cambiar-fecha-envio", function () {
        var orden_id = $(this).data('id');
        var codigo_orden = $(this).data('codigo_orden');
        var modal_detalle = $(this).data('modal_detalle');
        var modal_new = $(this).data('modal_new');
        dump(orden_id);
        $(".codigo_orden").html(codigo_orden);
        $(".orden_id").val(orden_id);
        $("." + modal_detalle).modal("hide");
        $("." + modal_new).modal("show");
    });

    $(document).on("click", ".imprimir-comprobante", function () {
        var orden_id = $(this).data('id');
        window.location.href = "/app/Comprobante?orden_id=" + orden_id;
    });

    $.validate({
        language: jsLanguageValidator,
        form: '#frm_asignar_ruta_orden',
        onError: function () {
        },
        onSuccess: function () {
            var params = $("#frm_asignar_ruta_orden").serialize();
            var action = $("#frm_asignar_ruta_orden #action").val();
            var button = $('#frm_asignar_ruta_orden button[type="submit"]');
            dump(button);
            callAjax(action, params, button);
            return false;
        }
    });

    $.validate({
        language: jsLanguageValidator,
        form: '#frm_changes_status_orden',
        onError: function () {
        },
        onSuccess: function () {
            var params = $("#frm_changes_status_orden").serialize();
            var action = $("#frm_changes_status_orden #action").val();
            var button = $('#frm_changes_status_orden button[type="submit"]');
            dump(button);
            callAjax(action, params, button);
            return false;
        }
    });

    $.validate({
        language: jsLanguageValidator,
        form: '#frm_migrar_ruta_orden',
        onError: function () {
        },
        onSuccess: function () {
            var params = $("#frm_migrar_ruta_orden").serialize();
            var action = $("#frm_migrar_ruta_orden #action").val();
            var button = $('#frm_migrar_ruta_orden button[type="submit"]');
            dump(button);
            callAjax(action, params, button);
            return false;
        }
    });

    $.validate({
        language: jsLanguageValidator,
        form: '#frm_change_fecha_envio_orden',
        onError: function () {
        },
        onSuccess: function () {
            var params = $("#frm_change_fecha_envio_orden").serialize();
            var action = $("#frm_change_fecha_envio_orden #action").val();
            var button = $('#frm_change_fecha_envio_orden button[type="submit"]');
            dump(button);
            callAjax(action, params, button);
            return false;
        }
    });

    $.validate({
        language: jsLanguageValidator,
        form: '#frm_change_fecha_entrega_orden',
        onError: function () {
        },
        onSuccess: function () {
            var params = $("#frm_change_fecha_entrega_orden").serialize();
            var action = $("#frm_change_fecha_entrega_orden #action").val();
            var button = $('#frm_change_fecha_entrega_orden button[type="submit"]');
            dump(button);
            callAjax(action, params, button);
            return false;
        }
    });


    $.validate({
        language: jsLanguageValidator,
        form: '#frm_table_ordenes_por_ruta_busqueda',
        onError: function () {
        },
        onSuccess: function () {
            initTableOrdenesPorRutaBusquedaAvanzada();
            $("#frm_table_ordenes_por_ruta_busqueda").modal('hide');
            clearFormElements("#frm_table_ordenes_por_ruta_busqueda");
            return false;
        }
    });

    $.validate({
        language: jsLanguageValidator,
        form: '#frm_busca_orden',
        onError: function () {
        },
        onSuccess: function () {
            var params = $("#frm_busca_orden").serialize();
            var action = $("#frm_busca_orden #action").val();
            var button = $('#frm_busca_orden button[type="submit"]');
            dump(button);
            console.log(JSON.stringify(params));
            callAjax(action, params, button);
            return false;
        }
    });



});

function initTableOrdenesPorRuta()
{
    var params = $("#frm_table_ordenes_por_ruta").serialize();
    var action = $("#frm_table_ordenes_por_ruta #action").val();
    params += "&language=" + language;

    let paramsSearch = new URLSearchParams(window.location.search);
    let id_ruta = paramsSearch.get('id_ruta');

    params += "&id_ruta=" + id_ruta;
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
}

function initTableOrdenesPorRutaBusquedaAvanzada()
{
    var params = $("#frm_table_ordenes_por_ruta_busqueda").serialize();
    var action = $("#frm_table_ordenes_por_ruta_busqueda #action").val();
    params += "&language=" + language;
    let paramsSearch = new URLSearchParams(window.location.search);
    let id_ruta = paramsSearch.get('id_ruta');

    params += "&id_ruta=" + id_ruta;
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
}









