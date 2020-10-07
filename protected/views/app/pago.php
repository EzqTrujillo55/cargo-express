<div class="modal pagar-orden" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 id="myLargeModalLabel" class="modal-title">
                    <?php echo t("Pagar Orden") ?>
                </h4> 
                <button aria-label="Cerrar" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true"><i class="ion-android-close"></i></span>
                </button> 

            </div>  

            <div class="modal-body">

                <form id="frm_pago" class="frm" method="POST" onsubmit="return false;">
                    <?php echo CHtml::hiddenField('action', 'pagarOrden') ?>
                    <?php
                    echo CHtml::hiddenField('orden_id_f', '', array(
                        'class' => "orden_id"
                    ));
                    echo CHtml::hiddenField('id_cliente_f', '', array(
                        'class' => "id_cliente"
                    ));
                    echo CHtml::hiddenField('codigo_orden_f', '', array(
                        'class' => "codigo_orden"
                    ));
                    ?>
                    <div class="row">
                        <div class="col-md-12 ">

                            <h5><?php echo Driver::t("Detalle Pago") ?></h5>


                            <div class="top20">
                                <div class="row top10">
                                    <div class="col-md-6">
                                        <div class="form-group left-form-group">
                                            <label class="font-medium"><?php echo Driver::t("Cliente") ?> :</label>
                                            <p class="v nombres"></p>
                                        </div>    
                                    </div> <!--col-->
                                    <div class="col-md-6">
                                        <div class="form-group left-form-group">
                                            <label class="font-medium"><?php echo Driver::t("Tipo Servicio") ?> :</label>
                                            <p class="v tipo_servicio"></p>
                                        </div>    
                                    </div> <!--col-->
                                </div>

                                <div class="row top10">
                                    <div class="col-md-6">
                                        <div class="form-group left-form-group">
                                            <label class="font-medium"><?php echo Driver::t("Ciudad") ?> :</label>
                                            <p class="v ciudad_destino"></p>
                                        </div>    
                                    </div> <!--col-->
                                    <div class="col-md-6">
                                        <div class="form-group left-form-group">
                                            <label class="font-medium"><?php echo Driver::t("Zona") ?> :</label>
                                            <p class="v zona_destino_nombre"></p>
                                        </div>    
                                    </div> <!--col-->
                                </div>

                            </div>
                            <div class="top20">
                                <div class="row top10">
                                    <div class="col-md-6">
                                        <h5> Peso</h5>
                                    </div> <!--col-->
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('peso', '', array(
                                            'class' => "validate numeric_only",
                                            'data-validation' => "required"
                                        ))
                                        ?>
                                    </div> <!--col-->
                                </div>
                                <div class="row top10">
                                    <div class="col-md-6">
                                        <h5> Unidad Peso</h5>
                                    </div> <!--col-->
                                    <div class="col-md-6 ">
                                        <select required class="chosen" id="unidad_peso" name="unidad_peso" >
                                            <option    ><?php echo Driver::t("Por favor seleccione un valor de la lista") ?></option>
                                            <option  value="kgs"   ><?php echo Driver::t("kgs") ?></option>
                                            <option  value="lbs"   ><?php echo Driver::t("lbs") ?></option>
                                        </select>
                                    </div> <!--col-->
                                </div>
                                <div class="row top10">
                                    <div class="col-md-6">
                                        <h5> Dimensiones</h5>
                                    </div> <!--col-->
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('dimensiones', '', array(
                                            'class' => "",
                                            'required' => true
                                        ))
                                        ?>
                                    </div> <!--col-->
                                </div>
                                <div class="row top10">
                                    <div class="col-md-6">
                                        <h5> Precio Flete</h5>
                                    </div> <!--col-->
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('precio', '', array(
                                            'class' => "validate numeric_only",
                                            'data-validation' => "required"
                                        ))
                                        ?>
                                    </div> <!--col-->
                                </div>
                                <div class="row top10">
                                    <div class="col-md-6">
                                        <h5> Precio Envío Zona</h5>
                                    </div> <!--col-->
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('precio_zona', '', array(
                                            'class' => "validate numeric_only",
                                            'data-validation' => "required"
                                        ))
                                        ?>
                                    </div> <!--col-->
                                </div>
                                <div class="row top10">
                                    <div class="col-md-6">
                                        <h4> Subtotal</h4>
                                    </div> <!--col-->
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('subtotal', '', array(
                                            'class' => "validate numeric_only",
                                            'data-validation' => "required",
                                        ))
                                        ?>
                                    </div> <!--col-->
                                </div>
                                <div class="row top10">
                                    <div class="col-md-6">
                                        <h5> Descuentos</h5>
                                    </div> <!--col-->
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('descuentos', '', array(
                                            'class' => "validate numeric_only",
                                            'data-validation' => "required"
                                        ))
                                        ?>
                                    </div> <!--col-->
                                </div> <!--row-->
                                <div class="row top20">
                                    <div class="col-md-6">
                                        <h4> Total</h4>
                                    </div> <!--col-->
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('total', '', array(
                                            'class' => "validate numeric_only",
                                            'data-validation' => "required",
                                        ))
                                        ?>
                                    </div> <!--col-->
                                </div>
                            </div> <!--row-->
                        </div>
                    </div> <!--delivery-info-wrap-->



                    <div class="panel-footer top20">

                        <button type="submit" class="orange-button medium rounded new-pago-submit">
                            <?php echo t("Guardar") ?>
                        </button>

                        <button type="button" data-id=".pagar-orden" 
                                class="close-modal green-button medium rounded"><?php echo t("Cancelar") ?></button>
                    </div>
                </form>

        </div> <!--row-->
    </div> <!--panel-footer-->



</div> <!--body-->
<?php
Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/assets/pagos.js', CClientScript::POS_END
);
?>
</div> <!--modal-dialog-->

