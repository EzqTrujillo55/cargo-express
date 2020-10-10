
<div class="modal new-orden" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 id="myLargeModalLabel" class="modal-title">
                    <?php echo t("Orden") ?>
                </h4> 
                <button aria-label="Cerrar" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true"><i class="ion-android-close"></i></span>
                </button> 

            </div>  

            <div class="modal-body">

                <form id="frm_orden" class="frm" method="POST" onsubmit="return false;">
                    <?php echo CHtml::hiddenField('action', 'addOrden') ?>
                    <?php
                    echo CHtml::hiddenField('orden_id', '', array(
                        'class' => "orden_id"
                    ));
                    echo CHtml::hiddenField('estado', '', array(
                        'class' => "estado"
                    ));
                    ?>
                    <div class="row">
                        <div class="col-md-12 ">

                            <h5><?php echo Driver::t("Detalle") ?></h5>
                            <div class="top10">
                                <?php
                                echo CHtml::textArea('detalle', '', array(
                                    'class' => "",
                                    'required' => true
                                ))
                                ?>
                            </div>

                            <div class="top10 row">
                                <div class="col-md-12">
                                    <?php
                                    if ($clientes_list = Driver::getClientesList()) {
                                        $clientes_list = Driver::toList($clientes_list, 'id_cliente', 'nombres', Driver::t("Por favor seleccione un cliente de la lista"));
                                    }

                                    echo CHtml::dropDownList('id_cliente', '', (array) $clientes_list
                                            , array(
                                        'class' => "id_cliente chosen",
                                        'required' => true
                                    ))
                                    ?>
                                </div> <!--col-->
                            </div>
                            <div class="top10 row">
                                <div class="col-md-12 ">
                                    <div class="form-group left-form-group">
                                        <label class="font-medium"><?php echo Driver::t("Código") ?> :</label>
                                        <span class="v codigo_orden" id="codigo_orden"></span>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <?php
                                    echo CHtml::radioButton('tipo_servicio', false, array(
                                        'class' => "tipo_servicio",
                                        'value' => 'Envio Xpress',
                                        'required' => true
                                    ));
                                    ?>
                                    <span><?php echo Driver::t("Envio Xpress") ?></span>
                                </div>
                                <div class="col-md-4 ">
                                    <?php
                                    echo CHtml::radioButton('tipo_servicio', false, array(
                                        'class' => "tipo_servicio",
                                        'value' => "Envio Basico"
                                    ));
                                    ?>
                                    <span><?php echo Driver::t("Envio Basico") ?></span>
                                </div> <!--col-->
                                  <div class="col-md-4 ">
                                    <?php
                                    echo CHtml::radioButton('tipo_servicio', false, array(
                                        'class' => "tipo_servicio",
                                        'value' => "Delivery"
                                    ));
                                    ?>
                                    <span><?php echo Driver::t("Delivery") ?></span>
                                </div> <!--col-->
                            </div> <!--row-->
                            <?php
                            if ($provincia_list = Driver::getProvinciaList()) {
                                $provincia_list = Driver::toList($provincia_list, 'id', 'nombre', Driver::t("Por favor seleccione una provincia de la lista"));
                            }
                            
                           
                            
                            ?>

                            <div class="top20">
                                <h5 style="font-weight:bold;" class="dropoff_action_1"><?php echo t("Datos Origen") ?></h5>
                                
                                <div class="row top10">
                                    <div class="col-md-6 ">
                                        <p>Peso</p>
                                        <?php
                                        echo CHtml::textField('peso', '', array(
                                            'placeholder' => Driver::t("Peso"),
                                            'class' => "validate numeric_only",
                                            'data-validation' => "required",
                                        ))
                                        ?>
                                    </div> <!--col-->
                                    <div class="col-md-6 ">
                                        <p>Número de Gestiones</p>
                                        <?php
                                        echo CHtml::textField('no_gestiones', '', array(
                                            'placeholder' => Driver::t("Número de Gestiones"),
                                            'class' => "validate numeric_only",
                                            'data-validation' => "required"
                                        ))
                                        ?>
                                    </div> <!--col-->
                                    <div class="col-md-6">
                                        <?php
                                        echo CHtml::textField('origen', '', array(
                                            'placeholder' => Driver::t("Origen"),
                                            'required' => true
                                        ))
                                        ?>
                                    </div> <!--col-->
                                     <!--col-->
                                     <div class="col-md-6 ">
                                        <select required="true" class="ciudad_origen_id chosen" name="ciudad_origen_id" id="ciudad_origen_id">
                                            <option ><?php echo Driver::t("Por favor seleccione una ciudad de la lista") ?></option>
                                        </select>
                                    </div> <!--col-->
                                </div> <!--row-->

                                
                                <div class="row top10">
                                    <div class="col-md-12 ">
                                        <?php
                                         if ($direccionOrigen_list = Driver::getDireccionOrigenList()) {
                                            $direccionOrigen_list = Driver::toList($direccionOrigen_list, 'id_cliente', 'direccion_origen',  Driver::t("Por favor seleccione una dirección de la lista"));
                                        }
                                        echo CHtml::dropDownList('id_cliente', '', (array) $direccionOrigen_list
                                        , array(
                                    'class' => "id_cliente chosen",
                                    'required' => true
                                ))
                                        ?>
                                    </div> <!--col-->
                                    <div class="col-md-12 mt-2">
                                        <?php
                                        echo CHtml::textField('link_ubicacion_origen', '', array(
                                            'class' => 'link_ubicacion:origen',
                                            'placeholder' => Driver::t("Link de Ubicación(Opcional)"),
                                            'required' => false
                                        ));
                                        ?>
                                    </div> <!--col-->
                                </div>
                                <div class="row top10">
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('remitente', '', array(
                                            'placeholder' => t("Remitente"),
                                            'required' => true
                                        ))
                                        ?>
                                    </div>
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('telefono_remitente', '', array(
                                            'placeholder' => t("Teléfono Remitente"),
                                            'class' => "mobile_inputs"
                                        ))
                                        ?>
                                    </div>
                                </div>
                            </div> <!--delivery-info-wrap-->


                            <div class="top20"> 

                                <h5 style="font-weight:bold;" class="dropoff_action_2"><?php echo t("Datos Destino") ?></h5>

                                <div class="row top10">
                                    <div class="col-md-6">
                                        <?php
                                        echo CHtml::textField('destino', '', array(
                                            'placeholder' => Driver::t("Destino"),
                                            'required' => true
                                        ))
                                        ?>
                                    </div> <!--col-->
                                    <div class="col-md-6 ">
                                       <?php
                                       if ($ciudades_list = Driver::getCiudadesList()) {
                                        $ciudades_list = Driver::toList($ciudades_list, 'id', 'nombre',  Driver::t("Por favor seleccione una ciudad de la lista"));
                                    }
                                    echo CHtml::dropDownList('id', '', (array) $ciudades_list
                                    , array(
                                'class' => "id chosen",
                                'required' => true
                            ))
                            ?>
                                    </div>
                                </div> <!--row-->

                                
                                <div class="row top10">
                                    <div class="col-md-12 ">
                                    <?php
                                         if ($direccionDestino_list = Driver::getDireccionDestinoList()) {
                                            $direccionDestino_list = Driver::toList($direccionDestino_list, 'id_cliente', 'direccion_destino',  Driver::t("Por favor seleccione una dirección de la lista"));
                                        }
                                        echo CHtml::dropDownList('id_cliente', '', (array) $direccionDestino_list
                                        , array(
                                    'class' => "id_cliente chosen",
                                    'required' => true
                                ))
                                        ?>
                                    </div> <!--col-->
                                    <div class="col-md-12 mt-2">
                                        <?php
                                        echo CHtml::textField('link_ubicacion_destino', '', array(
                                            'class' => 'link_ubicacion_destino',
                                            'placeholder' => Driver::t("Link de Ubicación(Opcional)"),
                                            'required' => false
                                        ));
                                        ?>
                                    </div> <!--col-->
                                </div>
                                <div class="row top10">
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('recibe', '', array(
                                            'placeholder' => t("Destinatario"),
                                            'required' => true
                                        ))
                                        ?>
                                    </div>
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('telefono_recibe', '', array(
                                            'placeholder' => t("Teléfono Recipiente"),
                                            'class' => "mobile_inputs"
                                        ))
                                        ?>
                                    </div>
                                </div>
                            </div> <!--dropoff_wrap-->



                        </div> <!--col-->


                    </div> <!--row-->

                    <div class="panel-footer top20">

                        <button type="submit" class="orange-button medium rounded new-orden-submit">
                            <?php echo t("Guardar") ?>
                        </button>

                        <button type="button" data-id=".new-orden" 
                                class="close-modal green-button medium rounded"><?php echo t("Cancelar") ?></button>
                    </div>
                </form>
            </div> <!--panel-footer-->



        </div> <!--body-->

    </div> <!--modal-content-->
</div> <!--modal-dialog-->