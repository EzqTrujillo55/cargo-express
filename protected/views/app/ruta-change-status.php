<div class="modal fade change-status-ruta-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
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

                <form id="frm_changes_status_ruta" class="frm" method="POST" onsubmit="return false;">

                    <?php echo CHtml::hiddenField('action', 'changeStatusRuta') ?>
                    <?php
                    echo CHtml::hiddenField('id_ruta', '', array(
                        'class' => "id_ruta"
                    ))
                    ?>

                    <h5 class="top20"><?php echo Driver::t("Status") ?></h5>          
                    <div class="top10 row">
                        <div class="col-md-12 ">
                            <?php
                            echo CHtml::dropDownList('status', '', (array) Driver::statusrutaList(), array(
                                'class' => "status status_ruta_change"
                            ))
                            ?>
                        </div>
                    </div>



                    <div class="panel-footer top20">       
                        <button type="submit" class="orange-button medium rounded">
                            <?php echo t("Guardar") ?>
                        </button>

                        <button type="button" data-id=".change-status-ruta-modal" 
                                class="close-modal green-button medium rounded"><?php echo t("Cancelar") ?></button>
                    </div>


                </form>

            </div> <!--body-->

        </div> <!--modal-content-->
    </div> <!--modal-dialog-->
</div> <!--modal-->            