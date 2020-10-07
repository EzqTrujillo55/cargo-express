var ajax_request;
$(document).ready(function () {

    var action = "getDashboard";
    ajax_request = $.ajax({
        url: ajax_url + "/" + action,
        type: 'post',
        //async: false,
        dataType: 'json',
        timeout: 6000,
        beforeSend: function () {
            dump("before=>");
            dump(ajax_request);
            if (ajax_request != null) {
                ajax_request.abort();
                dump("ajax abort");
                busy(false);
            } else {
                busy(true);
            }
        },
        complete: function (data) {
            ajax_request = (function () {
                return;
            })();
            dump('Completed');
            dump(ajax_request);
            busy(false);
        },
        success: function (data) {
            dump(data);
            if (data.code == 1) {

                switch (action)
                {
                    case "getDashboard":
                        $('#total_ordenes').append(data.details.TOTAL_ORDENES);
                        $('#total_ordenes_creadas').append(data.details.TOTAL_ORDENES_CREADAS);
                        $('#total_ordenes_completadas').append(data.details.TOTAL_ORDENES_COMPLETADAS);
                        $('#total_ordenes_en_ruta').append(data.details.TOTAL_ORDENES_EN_RUTA);
                        var total_ordenes_creadas_percentage = (data.details.TOTAL_ORDENES_CREADAS * 100) / data.details.TOTAL_ORDENES;
                        var total_ordenes_completadas_percentage = (data.details.TOTAL_ORDENES_COMPLETADAS * 100) / data.details.TOTAL_ORDENES;
                        var total_ordenes_en_ruta = (data.details.TOTAL_ORDENES_EN_RUTA * 100) / data.details.TOTAL_ORDENES;
                        $('#stat_completadas').html("<i class='fa fa-circle-o m-r-10'></i>Completadas " + total_ordenes_completadas_percentage + "%");
                        $('#stat_creadas').html("<i class='fa fa-circle-o m-r-10'></i>Creadas " + total_ordenes_creadas_percentage + "%");
                        $('#stat_en_ruta').html("<i class='fa fa-circle-o m-r-10'></i>En Ruta " + total_ordenes_en_ruta + "%");
                        var doughnutData = {
                            labels: ["Creadas", "Completadas", "En Ruta"],
                            datasets: [{
                                    data: [data.details.TOTAL_ORDENES_CREADAS, data.details.TOTAL_ORDENES_COMPLETADAS, data.details.TOTAL_ORDENES_EN_RUTA],
                                    backgroundColor: ["rgb(255, 99, 132)", "rgb(54, 162, 235)", "rgb(255, 205, 86)"]
                                }]
                        };
                        var doughnutOptions = {
                            responsive: true,
                            legend: {
                                display: false
                            },
                        };
                        var ctx4 = document.getElementById("doughnut_chart").getContext("2d");
                        new Chart(ctx4, {type: 'doughnut', data: doughnutData, options: doughnutOptions});
                        console.log(JSON.stringify(data.details));
                        break;
                    default:
                        nAlert(data.msg, "success");
                        break;
                }

            } else {

                // failed mycon
                switch (action)
                {

                    default :
                        nAlert(data.msg, "warning");
                        break;
                }

            }
        },
        error: function (request, error) {

        }
    });
    $(function () {
        // World Map
        $('#world-map').vectorMap({
            map: 'Ecuador',
            color: '#ffffff',
            hoverOpacity: 0.7,
            selectedColor: '#666666',
            enableZoom: false,
            showTooltip: true,
            scaleColors: ['#E6F2F0', '#149B7E'],
            normalizeFunction: 'polynomial',
            onRegionClick: function (element, code, region) {
                if (region === "Pichincha") {
                    rcPichincha();
                }
                if (region === "Azuay") {
                    rcAzuay();
                }
                if (region === "Bolivar")
                {
                    rcBolivar();
                }
                if (region === "Cañar") {
                    rcCanar();
                }
                if (region === "Carchi") {
                    rcCarchi();
                }
                if (region === "Cotopaxi") {
                    rcCotopaxi();
                }
                if (region === "Chimborazo") {
                    rcChimborazo();
                }
                if (region === "El Oro") {
                    rcEloro();
                }
                if (region === "Esmeraldas") {
                    rcEsmeraldas();
                }
                if (region === "Guayas") {
                    rcGuayas();
                }
                if (region === "Imbabura") {
                    rcImbabura();
                }
                if (region === "Loja") {
                    rcLoja();
                }
                if (region === "Los Rios") {
                    rcLosrios();
                }
                if (region === "Manabi") {
                    rcManabi();
                }
                if (region === "Morona Santiago") {
                    rcMoronasantiago();
                }
                if (region === "Napo") {
                    rcNapo();
                }
                if (region === "Pastaza") {
                    rcPastaza();
                }
                if (region === "Tungurahua") {
                    rcTungurahua();
                }
                if (region === "Zamora Chinchipe") {
                    rcZamorachinchipe();
                }
                if (region === "Galápagos") {
                    rcGalapagos();
                }
                if (region === "Sucumbios") {
                    rcSucumbios();
                }
                if (region === "Orellana") {
                    rcOrellana();
                }
                if (region === "Santo Domingo de los Tsáchilas") {
                    rcSantodomingodelostsachilas();
                }
                if (region === "Santa Elena") {
                    rcSantaelena();
                }
            },
            onLabelShow: function (event, label, code) {
                if (label[0].innerHTML === "Pichincha") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Azuay") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Bolivar") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Cañar") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Carchi") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Cotopaxi") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Chimborazo") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "El Oro") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Esmeraldas") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Guayas") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Imbabura") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Loja") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Los Rios") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Manabi") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Morona Santiago") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Napo") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Pastaza") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Tungurahua") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Zamora Chinchipe") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Galápagos") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Sucumbios") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Orellana") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Santo Domingo de los Tsáchilas") {
                    label[0].innerHTML = label[0].innerHTML;
                }
                if (label[0].innerHTML === "Santa Elena") {
                    label[0].innerHTML = label[0].innerHTML;
                }

            },
        });
    });
});
function dump(data)
{
    console.debug(data);
}

function busy(e)
{
    if (e) {
        $('body').css('cursor', 'wait');
    } else
        $('body').css('cursor', 'auto');
    if (e) {
        dump('busy loading');
        /*NProgress.set(0.0);		
         NProgress.inc(); */
        $(".main-preloader").show();
    } else {
        dump('done loading');
        $(".main-preloader").hide();
        //NProgress.done();  
    }
}
