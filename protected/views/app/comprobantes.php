<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Cargo Xpress</title>

        <style>
            @media print {
                body{
                    width: 21cm;
                    height: 29.7cm;
                    margin: 0; 

                    /* change the margins as you want them to be. */
                } 
            }
            

            .invoice-box {
                width: 21cm;
                height: 29.7cm;
                margin: 0px;
                border: 1px solid #eee;
                box-shadow: 0 0 0px rgba(0, 0, 0, .15);
                font-size: 8px;
                line-height: 14px;
                font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                color: #555;
            }

            .invoice-box table {
                width: 100%;
                line-height: inherit;
                text-align: left;
            }

            .invoice-box table td {
                padding-left: 5px;
                vertical-align: top;
            }

            .invoice-box table tr td:nth-child(2) {
                text-align: right;
            }

            .invoice-box table tr.top table td {
                padding-bottom: 20px;
                text-align: center;
            }

            .invoice-box table tr.top table td.title {
                font-size: 20px;
                line-height: 45px;
                color: #333;
            }

            .invoice-box table tr.information table td {
                padding-bottom: 5px;
            }

            .invoice-box table tr.heading td {
                background: #eee;
                border-bottom: 1px solid #ddd;
                font-weight: bold;
            }

            .invoice-box table tr.details td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.item td{
                border-bottom: 1px solid #000;
                border-top:  1px solid #000;
                border-left: 1px solid #000;
                border-right:  1px solid #000;

                text-align: left;
            }

            .invoice-box table tr.item.last td {
                border-bottom: none;
            }

            .invoice-box table tr.total td:nth-child(2) {
                border-top: 2px solid #eee;
                font-weight: bold;
            }

            @media only screen and (max-width: 600px) {
                .invoice-box table tr.top table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }

                .invoice-box table tr.information table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }
            }

            /** RTL **/
            .rtl {
                direction: rtl;
                font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            }

            .rtl table {
                text-align: right;
            }

            .rtl table tr td:nth-child(2) {
                text-align: left;
            }
        </style>
    </head>
    <body> 
        <form id="form_invoice">
            <?php if (is_array($data) && count($data) >= 1): ?>
                <script> var data = <?php echo json_encode($data); ?>;</script>
                <?php foreach ($data as $val) { ?>
                    <?php if ($res = Driver::getOrdenId($val)) { ?>
                        <input type="hidden"  id="codigo_orden_<?php echo $res['orden_id'] ?>"  value="<?php echo $res['codigo_orden'] ?>">
                        <div class="invoice-box" id="invoice-box_<?php echo $res['orden_id'] ?>">
                            <table cellpadding="0" cellspacing="0">
                                <tr class="top">
                                    <td colspan="4" >
                                        <table>
                                            <tr>

                                                <td>
                                                    Cargo Xpress<br>
                                                    Telf: 0999979075 02-2443178 - www.web-cargoxpress.com <br>
                                                    ÓRDENES DE ENVÍO
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr class="information">
                                    <td colspan="2">
                                        <table>
                                            <tr>
                                                <td>
                                                    <div id="codigo_orden_barcode_<?php echo $res['orden_id'] ?>"></div>
                                                </td>

                                                <td>

                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table cellpadding="0" cellspacing="0">

                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Fecha</label>
                                    </td>

                                    <td>
                                        <p class="v date_created"><?php echo $res['date_created'] ?></p>
                                    </td>
                                    <td>
                                        <label class="font-medium">Servicio</label>
                                    </td>

                                    <td>
                                        <p class="v tipo_servicio"><?php echo $res['tipo_servicio'] ?></p>
                                    </td>
                                </tr>

                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Origen</label>
                                    </td>

                                    <td>
                                        <span class="v origen"><?php echo $res['origen'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Destino</label>
                                    </td>

                                    <td>
                                        <span class="v destino"><?php echo $res['destino'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Dirección</label>
                                    </td>

                                    <td>
                                        <span class="v direccion_origen"><?php echo $res['direccion_origen'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Dirección</label>
                                    </td>

                                    <td>
                                        <span class="v direccion_destino"><?php echo $res['direccion_destino'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Envía</label>
                                    </td>

                                    <td>
                                        <span class="v remitente"><?php echo $res['remitente'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Recibe</label>
                                    </td>

                                    <td>
                                        <span class="v recibe"><?php echo $res['recibe'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Teléfono</label>
                                    </td>

                                    <td>
                                        <span class="v telefono_remitente"><?php echo $res['telefono_remitente'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Teléfono</label>
                                    </td>

                                    <td>
                                        <span class="v telefono_recibe"><?php echo $res['telefono_recibe'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Ciudad</label>
                                    </td>

                                    <td>
                                        <span class="v ciudad_origen"><?php echo $res['ciudad_origen'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Ciudad</label>
                                    </td>

                                    <td>
                                        <span class="v ciudad_destino"><?php echo $res['ciudad_destino'] ?></span>
                                    </td>
                                </tr>

                            </table>
                            <table cellpadding="0" cellspacing="0">
                                <tr class="item" >
                                    <td colspan="4" style="text-align: center">
                                        GESTIONES
                                    </td>

                                </tr>
                                <tr class="item">
                                    <td colspan="4">
                                        <span class="v detalle"><?php echo $res['detalle'] ?></span>
                                    </td>

                                </tr>
                            </table>
                            <br/>
                            <table cellpadding="0" cellspacing="0">


                                <tr class="information">
                                    <td colspan="2">
                                        <table>
                                            <tr>
                                                <td>
                                                    <div id="codigo_orden_barcode2_<?php echo $res['orden_id'] ?>"></div>
                                                </td>

                                                <td>

                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table cellpadding="0" cellspacing="0">

                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Fecha</label>
                                    </td>

                                    <td>
                                        <p class="v date_created"><?php echo $res['date_created'] ?></p>
                                    </td>
                                    <td>
                                        <label class="font-medium">Servicio</label>
                                    </td>

                                    <td>
                                        <p class="v tipo_servicio"><?php echo $res['tipo_servicio'] ?></p>
                                    </td>
                                </tr>

                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Origen</label>
                                    </td>

                                    <td>
                                        <span class="v origen"><?php echo $res['origen'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Destino</label>
                                    </td>

                                    <td>
                                        <span class="v destino"><?php echo $res['destino'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Dirección</label>
                                    </td>

                                    <td>
                                        <span class="v direccion_origen"><?php echo $res['direccion_origen'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Dirección</label>
                                    </td>

                                    <td>
                                        <span class="v direccion_destino"><?php echo $res['direccion_destino'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Envía</label>
                                    </td>

                                    <td>
                                        <span class="v remitente"><?php echo $res['remitente'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Recibe</label>
                                    </td>

                                    <td>
                                        <span class="v recibe"><?php echo $res['recibe'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Teléfono</label>
                                    </td>

                                    <td>
                                        <span class="v telefono_remitente"><?php echo $res['telefono_remitente'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Teléfono</label>
                                    </td>

                                    <td>
                                        <span class="v telefono_recibe"><?php echo $res['telefono_recibe'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Ciudad</label>
                                    </td>

                                    <td>
                                        <span class="v ciudad_origen"><?php echo $res['ciudad_origen'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Ciudad</label>
                                    </td>

                                    <td>
                                        <span class="v ciudad_destino"><?php echo $res['ciudad_destino'] ?></span>
                                    </td>
                                </tr>

                            </table>
                            <table cellpadding="0" cellspacing="0">
                                <tr class="item" >
                                    <td colspan="4" style="text-align: center">
                                        GESTIONES
                                    </td>

                                </tr>
                                <tr class="item">
                                    <td colspan="4">
                                        <span class="v detalle"><?php echo $res['detalle'] ?></span>
                                    </td>

                                </tr>
                            </table>
                            <br/>
                            <table cellpadding="0" cellspacing="0">


                                <tr class="information">
                                    <td colspan="2">
                                        <table>
                                            <tr>
                                                <td>
                                                    <div id="codigo_orden_barcode3_<?php echo $res['orden_id'] ?>"></div>

                                                </td>

                                                <td>

                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table cellpadding="0" cellspacing="0">

                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Fecha</label>
                                    </td>

                                    <td>
                                        <p class="v date_created"><?php echo $res['date_created'] ?></p>
                                    </td>
                                    <td>
                                        <label class="font-medium">Servicio</label>
                                    </td>

                                    <td>
                                        <p class="v tipo_servicio"><?php echo $res['tipo_servicio'] ?></p>
                                    </td>
                                </tr>

                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Origen</label>
                                    </td>

                                    <td>
                                        <span class="v origen"><?php echo $res['origen'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Destino</label>
                                    </td>

                                    <td>
                                        <span class="v destino"><?php echo $res['destino'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Dirección</label>
                                    </td>

                                    <td>
                                        <span class="v direccion_origen"><?php echo $res['direccion_origen'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Dirección</label>
                                    </td>

                                    <td>
                                        <span class="v direccion_destino"><?php echo $res['direccion_destino'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Envía</label>
                                    </td>

                                    <td>
                                        <span class="v remitente"><?php echo $res['remitente'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Recibe</label>
                                    </td>

                                    <td>
                                        <span class="v recibe"><?php echo $res['recibe'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Teléfono</label>
                                    </td>

                                    <td>
                                        <span class="v telefono_remitente"><?php echo $res['telefono_remitente'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Teléfono</label>
                                    </td>

                                    <td>
                                        <span class="v telefono_recibe"><?php echo $res['telefono_recibe'] ?></span>
                                    </td>
                                </tr>
                                <tr class="item">
                                    <td>
                                        <label class="font-medium">Ciudad</label>
                                    </td>

                                    <td>
                                        <span class="v ciudad_origen"><?php echo $res['ciudad_origen'] ?></span>
                                    </td>
                                    <td>
                                        <label class="font-medium">Ciudad</label>
                                    </td>

                                    <td>
                                        <span class="v ciudad_destino"><?php echo $res['ciudad_destino'] ?></span>
                                    </td>
                                </tr>

                            </table>
                            <table cellpadding="0" cellspacing="0">
                                <tr class="item" >
                                    <td colspan="4" style="text-align: center">
                                        GESTIONES
                                    </td>

                                </tr>
                                <tr class="item">
                                    <td colspan="4">
                                        <span class="v detalle"><?php echo $res['detalle'] ?></span>
                                    </td>

                                </tr>
                            </table>
                        </div>


                    <?php } ?>
                <?php } ?>
            <?php endif; ?>
        </form>

    </body>
    <?php
    Yii::app()->clientScript->registerScriptFile(
            Yii::app()->baseUrl . '/assets/barcode/jquery-barcode.min.js', CClientScript::POS_END
    );
    Yii::app()->clientScript->registerScriptFile(
            Yii::app()->baseUrl . '/assets/comprobante/js/jspdf.min.js', CClientScript::POS_END
    );
    Yii::app()->clientScript->registerScriptFile(
            Yii::app()->baseUrl . '/assets/comprobante/js/html2canvas.min.js', CClientScript::POS_END
    );
    Yii::app()->clientScript->registerScriptFile(
            Yii::app()->baseUrl . '/assets/comprobantes.js', CClientScript::POS_END
    );
    ?>
</html>