<div class="modal fade cambiar-fecha-envio-orden-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true"><i class="ion-android-close"></i></span>
                </button> 
                <h4 id="mySmallModalLabel" class="modal-title">
                    <?php echo t("Orden ID") ?> : <span class="codigo_orden"></span>
                </h4> 
            </div>  

            <div class="modal-body">

                <form id="frm_change_fecha_envio_orden" class="frm" method="POST" onsubmit="return false;">

                    <?php echo CHtml::hiddenField('action', 'changeFechaEnvioOrden') ?>
                    <?php
                    echo CHtml::hiddenField('orden_id', '', array(
                        'class' => "orden_id"
                    ))
                    ?>

                    <h5 class="top20"><?php echo Driver::t("Fecha Envío") ?></h5>          
                    <div class="top10 row">
                        <div class="col-md-12 ">
                            <?php
                            echo CHtml::textField('fecha_envio', '', array(
                                'placeholder' => Driver::t("Fecha Envío"),
                                'required' => true,
                                'autocomplete' => "off",
                                'class' => "datetimepicker"
                            ))
                            ?>
                        </div>
                    </div>
                    <div>
                        <h5 class="top20"><?php echo Driver::t("Comentario") ?></h5>          
                        <div class="top10 row">
                            <div class="col-md-12 ">
                                <?php
                                echo CHtml::textArea('detalle', '', array(
                                ));
                                ?>
                            </div>
                        </div>
                    </div> 


                    <div class="panel-footer top20">       
                        <button type="submit" class="orange-button medium rounded">
                            <?php echo t("Guardar") ?>
                        </button>

                        <button type="button" data-id=".cambiar-fecha-envio-orden-modal" 
                                class="close-modal green-button medium rounded"><?php echo t("Cancelar") ?></button>
                    </div>


                </form>

            </div> <!--body-->

        </div> <!--modal-content-->
    </div> <!--modal-dialog-->
</div> <!--modal-->            