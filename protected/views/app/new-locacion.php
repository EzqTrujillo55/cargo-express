
<div class="modal fade new-locacion" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true"><i class="ion-android-close"></i></span>
                </button> 
                <h4 id="mySmallModalLabel" class="modal-title">
                    <?php echo t("Zona") ?>
                </h4> 
            </div>  

            <div class="modal-body">

                <form id="frm2" class="frm2" method="POST" onsubmit="return false;">
                    <?php echo CHtml::hiddenField('action', 'addLocacion') ?>
                    <?php
                    echo CHtml::hiddenField('id_locacion', '', array(
                        'class' => "id_locacion"
                    ))
                    ?>
                    <div class="inner">
                        <p class="alert alert-info">Si desea crear una provincia, deje en blanco el campo provincia</p>
                    </div>
                    <div class="inner top20">
                        <h5 class="top20"><?php echo Driver::t("Nombre") ?></h5>          
                        <div class="top10 row">
                            <div class="col-md-12 ">
                                <?php
                                echo CHtml::textField('nombre', '', array(
                                    'placeholder' => Driver::t("Nombre"),
                                    'required' => true,
                                ))
                                ?>
                            </div>
                        </div>
                        <h5 class="top20"><?php echo Driver::t("Provincia") ?></h5> 
                        <div class="top10 row">
                            <div class="col-md-12">
                                <?php
                                if ($provincia_list = Driver::getLocacionProvinciasList()) {
                                    $provincia_list = Driver::toList($provincia_list, 'id', 'nombre', Driver::t(""));
                                }

                                echo CHtml::dropDownList('id_padre', '', (array) $provincia_list
                                        , array(
                                    'class' => "id_padre chosen"
                                ))
                                ?>
                            </div> <!--col-->
                        </div> <!--col-->
                        <div class="row top20">
                            <div class="col-md-6 col-md-offset-7">
                                <button type="submit" class="green-button medium rounded"><?php echo t("Guardar") ?></button>

                            </div>
                            <div class="col-md-6 col-md-offset-7">
                                <button type="button" data-id=".new-locacion" 
                                        class="close-modal red-button medium rounded"><?php echo t("Cancelar") ?></button>
                            </div>
                        </div>        


                    </div> <!--inner-->  
                </form>  

            </div> <!--body-->

        </div>
    </div>
</div>