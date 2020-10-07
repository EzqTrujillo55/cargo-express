
$(document).ready(function () {

    if ($("#frm_table_ciudad").exists()) {
        initTableLocacion();

    }

    $(document).on("click", ".edit-zona", function () {
        var id_zona = $(this).data('id_zona');
        dump('modal show');
        $("#id_zona").val(id_zona);
        $(".new-zona").modal({
    		backdrop: 'static',
    		keyboard: false
		});
    });


    $('.new-zona').on('hide.bs.modal', function (e) {
        $("#id_zona").val('');
        clearFormElements("#frm");
    });
    $('.new-zona').on('show.bs.modal', function (e) {

        //$(".modal-title").html(jslang.add_driver);
        if (!empty($("#id_zona").val())) {
            //$(".modal-title").html(jslang.update_driver);
            callAjax("getEditZona", "id_zona=" + $("#id_zona").val(), $("#frm .orange-button"));
        }
    });


    $(document).on("click", ".add-new-zona", function () {


        $(".id_zona").val('');
        $("#id_locacion").val('0');
        $("#id_locacion").trigger("chosen:updated");
        $(".new-zona").modal({
    		backdrop: 'static',
    		keyboard: false
		});
    });

    //////////////////locacion////



    $.validate({
        language: jsLanguageValidator,
        form: '#frm2',
        onError: function () {
        },
        onSuccess: function () {
            var params = $("#frm2").serialize();
            var action = $("#frm2 #action").val();
            var button = $('#frm2 button[type="submit"]');
            dump(button);
            callAjax(action, params, button);
            return false;
        }
    });

    $(document).on("click", ".edit-locacion", function () {
        var id_locacion = $(this).data('id_locacion');
        dump('modal show');
        $("#frm2 #id_locacion").val(id_locacion);
        $(".new-locacion").modal({
    		backdrop: 'static',
    		keyboard: false
		});
    });


    $('.new-locacion').on('hide.bs.modal', function (e) {
        $("#frm2 #id_locacion").val('');
        clearFormElements("#frm2");
    });
    $('.new-locacion').on('show.bs.modal', function (e) {

        //$(".modal-title").html(jslang.add_driver);
        if (!empty($("#frm2 #id_locacion").val())) {
            //$(".modal-title").html(jslang.update_driver);
            callAjax("getEditLocacion", "id_locacion=" + $("#frm2 #id_locacion").val(), $("#frm2 .orange-button"));
        }
    });


    $(document).on("click", ".add-new-locacion", function () {


        $("#frm2 id_locacion").val('');
        $("#frm2 #id_padre").val('0');
        $("#frm2 #id_padre").trigger("chosen:updated");
        $(".new-locacion").modal({
    		backdrop: 'static',
    		keyboard: false
		});
    });







});

function initTableLocacion()
{
    var params = $("#frm_table_ciudad").serialize();
    var action = $("#frm_table_ciudad #action").val();
    params += "&language=" + language;

    if ($.fn.dataTable.isDataTable('#table_list1')) {
        table = $('#table_list1').DataTable();
        table.destroy();
    }

    data_table = $('#table_list1').dataTable({
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








