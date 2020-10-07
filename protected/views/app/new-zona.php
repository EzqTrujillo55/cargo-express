
<div class="modal fade new-zona" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
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

                <form id="frm" class="frm" method="POST" onsubmit="return false;">
                    <?php echo CHtml::hiddenField('action', 'addZona') ?>
                    <?php
                    echo CHtml::hiddenField('id_zona', '', array(
                        'class' => "id_zona"
                    ))
                    ?>
                    <div class="inner">
                        <h5 class="top20"><?php echo Driver::t("Zona") ?></h5>          
                        <div class="top10 row">
                            <div class="col-md-12 ">
                                <?php
                                echo CHtml::textField('zona', '', array(
                                    'placeholder' => Driver::t("Zona"),
                                    'required' => true,
                                ))
                                ?>
                            </div>
                        </div>
                        <h5 class="top20"><?php echo Driver::t("Zona Padre") ?></h5> 
                        <div class="top10 row">
                            <div class="col-md-12">
                                <?php
                                echo CHtml::dropDownList('zona_padre', '', Driver::zonaPadreList(), array(
                                    'class' => "zona_padre",
                                    'required' => true
                                ))
                                ?>
                            </div> <!--col-->
                        </div>
                        <h5 class="top20"><?php echo Driver::t("Ciudad") ?></h5> 
                        <div class="top10 row">
                            <div class="col-md-12">
                                <?php
                                if ($ciudad_list = Driver::getLocacionCiudadesList()) {
                                    $ciudad_list = Driver::toList($ciudad_list, 'id', 'nombres', Driver::t("Por favor seleccione una ciudad de la lista"));
                                }

                                echo CHtml::dropDownList('id_locacion', '', (array) $ciudad_list
                                        , array(
                                    'class' => "id_locacion chosen",
                                    'required' => true
                                ))
                                ?>
                            </div> <!--col-->
                        </div> <!--col-->
                        <div class="row top20">
                            <div class="col-md-6 col-md-offset-7">
                                <button type="submit" class="green-button medium rounded"><?php echo t("Guardar") ?></button>

                            </div>
                            <div class="col-md-6 col-md-offset-7">
                                <button type="button" data-id=".new-zona" 
                                        class="close-modal red-button medium rounded"><?php echo t("Cancelar") ?></button>
                            </div>
                        </div>        


                    </div> <!--inner-->  
                </form>  

            </div> <!--body-->

        </div>
    </div>
</div>