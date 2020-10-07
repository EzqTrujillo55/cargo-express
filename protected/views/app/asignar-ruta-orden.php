<div class="modal fade asignar-ruta-orden-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true"><i class="ion-android-close"></i></span>
                </button> 
                <h4 id="mySmallModalLabel" class="modal-title">
                    <?php echo t("Ruta ID") ?> : <span class="ruta-id"></span>
                </h4> 
            </div>  

            <div class="modal-body">

                <form id="frm_asignar_ruta_orden" class="frm" method="POST" onsubmit="return false;">

                    <?php echo CHtml::hiddenField('action', 'changeAsignarOrden') ?>
                    <?php
                    echo CHtml::hiddenField('id_ruta', '', array(
                        'class' => "id_ruta"
                    ))
                    ?>

                    <h5 class="top20"><?php echo Driver::t("Órdenes Disponibles") ?></h5>          
                    <div class="top10 row">
                        <div class="col-md-12 ">
                            <?php
                            if ($ordenes_disponibles_list = Driver::getOrdenesDisponiblesList()) {
                                $ordenes_disponibles_list = Driver::toList($ordenes_disponibles_list, 'orden_id', 'nombre_ordenes', Driver::t("Por favor seleccione una orden de la lista"));
                            }

                            echo CHtml::dropDownList('orden_id', '', (array) $ordenes_disponibles_list
                                    , array(
                                'class' => "status chosen",
                                'required' => true
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

                        <button type="button" data-id=".asignar-ruta-orden-modal" 
                                class="close-modal green-button medium rounded"><?php echo t("Cancelar") ?></button>
                    </div>


                </form>

            </div> <!--body-->

        </div> <!--modal-content-->
    </div> <!--modal-dialog-->
</div> <!--modal-->            