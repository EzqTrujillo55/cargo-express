


$(document).ready(function () {



    if ($("#form_invoice").exists()) {
        if (data != null)
        {
            var codigos = 0;
            $.each(data, function (index, value) {
                var codigo_orden = $('#codigo_orden_' + value).val();
                codigos = codigos + 1;
                if ($("#codigo_orden_barcode_" + value).exists())
                {

                    var settings = {
                        bgColor: "#f1f1f1",

                    };
                    $("#codigo_orden_barcode_" + value).barcode(
                            codigo_orden, // Value barcode (dependent on the type of barcode)

                            "code128", // type (string)
                            settings
                            );
                    $("#codigo_orden_barcode2_" + value).barcode(
                            codigo_orden, // Value barcode (dependent on the type of barcode)

                            "code128", // type (string)
                            settings
                            );
                    $("#codigo_orden_barcode3_" + value).barcode(
                            codigo_orden, // Value barcode (dependent on the type of barcode)

                            "code128", // type (string)
                            settings
                            );
                }

            });

            //generatePDF();

        }
    }



});

async function generatePDF() {
    var imgData;
    var forPDF = document.querySelectorAll(".invoice-box");
    var len = forPDF.length;
    console.log(len);
    var doc = new jsPDF('p', 'mm', 'a4');
    doc.setFont("helvetica");
    doc.setFontType("bold");
    doc.setFontSize(9);
    busy(true, $('#form_invoice'));
    for (var i = 0; i < forPDF.length; i++) {
        await html2canvas(forPDF[i], {allowTaint: true}).then(function (canvas) {

            imgData = canvas.toDataURL('image/jpg', 1.0);
            doc.addImage(imgData, 'jpg', 0, 0, 595.28, 841.89);
            if (parseInt(i + 1) === len) {
                doc.save('reporte.pdf');
                busy(false, $('#form_invoice'));
            } else {
                doc.addPage(595.28, 841.89);
            }
        });
    }
}






