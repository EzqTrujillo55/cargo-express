jQuery.fn.exists = function () {
    return this.length > 0;
}

var data_table;



$(document).ready(function () {


    $(document).on("keyup", "#precio", function () {
        $("#subtotal").val(+$(this).val() + +$("#precio_zona").val());
        $("#total").val(+$("#subtotal").val() - +$("#descuentos").val());
    });

    $(document).on("keyup", "#precio_zona", function () {
        $("#subtotal").val(+$(this).val() + +$("#precio").val());
        $("#total").val(+$("#subtotal").val() - +$("#descuentos").val());
    });

    $(document).on("keyup", "#descuentos", function () {
        $("#total").val(+$("#subtotal").val() - +$("#descuentos").val());
    });



});
/*end docu*/
/* END DOCU*/