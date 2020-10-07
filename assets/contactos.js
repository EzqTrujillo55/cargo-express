$(document).ready(function () {

    if ($("#frm_table_contactos_user").exists()) {
        initTableContactosUser();
    }

    $(document).on("click", ".detalle", function () {
        var id = $(this).data('id');
        var hidden_id = $(this).data('hidden_id');
        var modal_detalle = $(this).data('modal_detalle');
        dump('modal show');
        $("." + hidden_id).val(id);
        $("." + modal_detalle).modal({
    		backdrop: 'static',
    		keyboard: false
		});
    });

    $.validate({
        language: jsLanguageValidator,
        form: '#frm_contacto',
        onError: function () {
        },
        onSuccess: function () {
            var params = $("#frm_contacto").serialize();
            var action = $("#frm_contacto #action").val();
            var button = $('#frm_contacto button[type="submit"]');
            dump(button);
            callAjax(action, params, button);
            return false;
        }
    });

    $.validate({
        language: jsLanguageValidator,
        form: '#frm_table_contactos_user_busqueda',
        onError: function () {
        },
        onSuccess: function () {
            initTableContactosUserBusquedaAvanzada();
            $("#table_busqueda").modal('hide');
            return false;
        }
    });

    $('.detalle-contacto-modal').on('show.bs.modal', function (e) {
        dump('modal totally loaded');
        dump($(".contacto_id").val());
        callAjax("getDetalleContacto", "contacto_id=" + $(".contacto_id").val());
    });

    $('.new-contacto').on('show.bs.modal', function (e) {
        var contacto_id = $(".contacto_id").val();
        dump(contacto_id);
        if (!empty(contacto_id)) {
            dump("contacto_id=>" + contacto_id);
            callAjax("getEditContacto", "contacto_id=" + contacto_id);
        }
    });
    $('.new-contacto').on('hide.bs.modal', function (e) {
        dump('hide modal');
        clearFormElements("#frm_contacto");
    });

    $(document).on("change", ".provincia", function () {
        var id = $(this).val();

        if (id == "0") {
            $("#zona").val('0');
            $("#ciudad_id").val('0');
        } else {
            callAjaxCiudad("getCiudadList", "id_padre=" + id, "ciudad_id", "0");

        }
    });

    $(document).on("change", ".ciudad_id", function () {
        var id = $(this).val();
        if (id == "0") {
            $("#zona").val('0');
        } else {
            callAjaxZona("getZonaList", "id_padre=" + id, "zona", "0");
        }
    });


    function initTableContactosUser()
    {
        var params = $("#frm_table_contactos_user").serialize();
        var action = $("#frm_table_contactos_user #action").val();
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

    function initTableContactosUserBusquedaAvanzada()
    {
        var params = $("#frm_table_contactos_user_busqueda").serialize();
        var action = $("#frm_table_contactos_user_busqueda #action").val();
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


    $(document).on("click", ".add-new-contacto", function () {

        if (account_status == "expired") {
            nAlert(jslang.account_expired, "warning");
        } else {
            $(".contacto_id").val('');
            $(".new-contacto").modal({
    		backdrop: 'static',
    		keyboard: false
		});
        }
    });



});
/*end docu*/
/* END DOCU*/