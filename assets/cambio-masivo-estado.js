
$(document).ready(function () {

    $("#codigo_orden_busqueda").val("");
    $('#codigo_orden_busqueda').focus();
    // Force focus
//    $("#codigo_orden_busqueda").blur(function () {
//        setTimeout(function () {
//            $("#codigo_orden_busqueda").focus();
//        }, 2);
//    });

    var buttonpressed;

    $('.submitbutton').click(function () {
        buttonpressed = $(this).attr('name')
    })

    if ($("#frm_table_todas_ordenes_masivo_estado").exists()) {

        jQuery.datetimepicker.setLocale('es');
        var hoy = moment(new Date()).format('YYYY/MM/DD');
        var ayer = moment(new Date()).add(-1, 'days').format('YYYY/MM/DD');

        $('#fecha_creacion_hasta').val(hoy);
        $('#fecha_creacion_desde').val(ayer);


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
    }


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
            var codigo_orden_busqueda = $('input[id="codigo_orden_busqueda"]').val();
            if ($('#seleccionado_' + codigo_orden_busqueda).exists()) {
                $('input:checkbox[id="seleccionado_' + codigo_orden_busqueda + '"]').prop('checked', true);
            } else
            {
                $('#no_encontrados').append("<p>" + codigo_orden_busqueda + "</p>");
            }
            $("#codigo_orden_busqueda").val("");
            $('#codigo_orden_busqueda').focus();
            return false;
        }
    });

    $.validate({
        language: jsLanguageValidator,
        form: '#frm_table_todas_ordenes_masivo_estado_busqueda',
        onError: function () {
        },
        onSuccess: function () {
            initTableMasivoEstadoBusquedaAvanzada();
            $("#table_busqueda").modal('hide');
            clearFormElements("#frm_table_todas_ordenes_masivo_estado_busqueda");
            return false;
        }
    });

    $.validate({
        language: jsLanguageValidator,
        form: '#frm_table_todas_ordenes_masivo_estado',
        onError: function () {
        },
        onSuccess: function () {
            if (buttonpressed == 'guardar')
            {
                Swal.fire({title: 'Está seguro?',
                    html: "<p>Desea guardar los datos para las órdenes seleccionadas? </p>"
                    , icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Sí'}).then((result) => {
                    if (result.value) {
                        var params = $("#frm_table_todas_ordenes_masivo_estado").serialize();
                        var action = $("#frm_table_todas_ordenes_masivo_estado #action2").val();
                        var button = $('#frm_table_todas_ordenes_masivo_estado button[type="submit"');
                        callAjax(action, params, button);
                        clearFormElements("#frm_table_todas_ordenes_masivo_estado");
                    }
                });
            }
            if (buttonpressed == 'imprimir-comprobantes')
            {
                var params = $("#frm_table_todas_ordenes_masivo_estado").serialize();
                window.open("/app/Comprobantes?" + params, '_blank');
            }

            buttonpressed = '';
            $("#codigo_orden_busqueda").val("");
            $('#codigo_orden_busqueda').focus();
            return false;
        }
    });

    function initTableTodasOrdenesMasivoEstado()
    {
        var params = $("#frm_table_todas_ordenes_masivo_estado").serialize();
        var action = $("#frm_table_todas_ordenes_masivo_estado #action").val();
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
    }

    function initTableMasivoEstadoBusquedaAvanzada()
    {
        var params = $("#frm_table_todas_ordenes_masivo_estado_busqueda").serialize();
        var action = $("#frm_table_todas_ordenes_masivo_estado_busqueda #action").val();
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


    function reinitTableMasivoEstadoBusquedaAvanzada()
    {
        var params = "";
        var action = "repedidosTodosMasivoEstadoFilteredList";
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
        window.open("/app/Comprobante?orden_id=" + orden_id, '_blank');
    });



    $(document).on("click", ".edit2", function () {
        var orden_id = $(this).data('id');
        var hidden_id = $(this).data('hidden_id');
        var modal_detalle = $(this).data('modal_detalle');
        var modal_new = $(this).data('modal_new');
        console.log(JSON.stringify($(this)));
        dump('modal show');
        $("." + hidden_id).val(orden_id);
        $("." + modal_detalle).modal("hide");
        $("." + modal_new).modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    $(document).on("click", ".clipboard", function () {
        var orden_id = $(this).data('id');
        callAjax("getClipboardOrden", "orden_id=" + orden_id);

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





});









