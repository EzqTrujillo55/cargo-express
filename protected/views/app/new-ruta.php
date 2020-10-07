
<div class="modal fade new-ruta" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true"><i class="ion-android-close"></i></span>
                </button> 
                <h4 id="mySmallModalLabel" class="modal-title">
                    <?php echo t("Ruta") ?>
                </h4> 
            </div>  

            <div class="modal-body">

                <form id="frm" class="frm" method="POST" onsubmit="return false;">
                    <?php echo CHtml::hiddenField('action', 'addRuta') ?>
                    <?php echo CHtml::hiddenField('id_ruta', '', array(
                                    'class' => "id_ruta"
                                )) ?>
                    <div class="inner">
                        <h5 class="top20"><?php echo Driver::t("Fecha Ruta") ?></h5>          
                        <div class="top10 row">
                            <div class="col-md-12 ">
                                <?php
                                echo CHtml::textField('fecha_ruta', '', array(
                                    'placeholder' => Driver::t("Fecha Ruta"),
                                    'required' => true,
                                    'class' => "datepicker"
                                ))
                                ?>
                            </div>
                        </div>
                        <h5 class="top20"><?php echo Driver::t("Mensajero") ?></h5> 
                        <div class="top10 row">
                            <div class="col-md-12">
                                <?php
                                if ($mensajeros_list = Driver::getMensajerosDisponiblesList()) {
                                    $mensajeros_list = Driver::toList($mensajeros_list, 'id_mensajero', 'nombres', Driver::t("Por favor seleccione un mensajero de la lista"));
                                }

                                echo CHtml::dropDownList('id_mensajero', '', (array) $mensajeros_list
                                        , array(
                                    'class' => "id_mensajero chosen",
                                    'required' => true
                                ))
                                ?>
                            </div> <!--col-->
                        </div>

                        <h5 class="top20"><?php echo Driver::t("Detalle") ?></h5>
                        <div class="top10">
                            <?php
                            echo CHtml::textArea('detalle', '', array(
                                'class' => "",
                                'required' => true
                            ))
                            ?>
                        </div>

                        <div class="row top20">
                            <div class="col-md-12">
                                <p><?php echo t("Status") ?></p>
                                <?php
                                echo CHtml::dropDownList('status', '', Driver::rutaStatus(), array(
                                    'required' => true
                                ));
                                ?>
                            </div>
                        </div>




                        <div class="row top20">
                            <div class="col-md-6 col-md-offset-7">
                                <button type="submit" class="green-button medium rounded"><?php echo t("Guardar") ?></button>

                            </div>
                            <div class="col-md-6 col-md-offset-7">
                                <button type="button" data-id=".new-ruta" 
                                        class="close-modal red-button medium rounded"><?php echo t("Cancelar") ?></button>
                            </div>
                        </div>        


                    </div> <!--inner-->  
                </form>  

            </div> <!--body-->

        </div>
    </div>
</div>