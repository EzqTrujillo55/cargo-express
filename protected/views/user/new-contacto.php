
<div class="modal new-contacto" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 id="myLargeModalLabel" class="modal-title">
                    <?php echo t("Contacto") ?>
                </h4> 
                <button aria-label="Cerrar" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true"><i class="ion-android-close"></i></span>
                </button> 

            </div>  

            <div class="modal-body">

                <form id="frm_contacto" class="frm" method="POST" onsubmit="return false;">
                    <?php echo CHtml::hiddenField('action', 'addContacto') ?>
                    <?php
                    echo CHtml::hiddenField('contacto_id', '', array(
                        'class' => "contacto_id"
                    ));
                    ?>
                    <div class="row">
                        <div class="col-md-12 ">

                            <h5><?php echo Driver::t("Detalle") ?></h5>

                            <?php
                            if ($provincia_list = Driver::getProvinciaList()) {
                                $provincia_list = Driver::toList($provincia_list, 'id', 'nombre', Driver::t("Por favor seleccione una provincia de la lista"));
                            }
                            ?>

                            <div class="top20">

                                <div class="row top10">
                                    <div class="col-md-6">
                                        <?php
                                        echo CHtml::textField('empresa', '', array(
                                            'placeholder' => Driver::t("Empresa"),
                                            'required' => true
                                        ))
                                        ?>
                                    </div> <!--col-->
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::dropDownList('provincia', '', (array) $provincia_list
                                                , array(
                                            'class' => "provincia chosen",
                                            'required' => true
                                        ))
                                        ?>
                                    </div> <!--col-->
                                </div> <!--row-->

                                <div class="row top10">
                                    <div class="col-md-6 ">
                                        <select required="true" class="ciudad_id chosen" name="ciudad_id" id="ciudad_id">
                                            <option ><?php echo Driver::t("Por favor seleccione una ciudad de la lista") ?></option>
                                        </select>
                                    </div> <!--col-->
                                    <div class="col-md-6">
                                        <select required="true" class="zona chosen" name="zona" id="zona">
                                            <option ><?php echo Driver::t("Por favor seleccione una zona de la lista") ?></option>
                                        </select>
                                    </div> <!--col-->
                                </div> <!--row-->

                                <div class="row top10">
                                    <div class="col-md-12 ">
                                        <?php
                                        echo CHtml::textField('direccion', '', array(
                                            'class' => 'direccion_origen',
                                            'placeholder' => Driver::t("Dirección"),
                                            'required' => true
                                        ));
                                        ?>
                                    </div> <!--col-->
                                </div>
                                <div class="row top10">
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('identificacion', '', array(
                                            'placeholder' => t("Identificación"),
                                            'required' => true
                                        ))
                                        ?>
                                    </div>
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('contacto', '', array(
                                            'placeholder' => t("contacto"),
                                            'required' => true
                                        ))
                                        ?>
                                    </div>


                                </div> <!--row-->   
                                <div class="row top10">
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('telefono', '', array(
                                            'placeholder' => t("Teléfono"),
                                            'class' => "mobile_inputs"
                                        ))
                                        ?>
                                    </div>
                                    <div class="col-md-6 ">
                                        <?php
                                        echo CHtml::textField('email', '', array(
                                            'placeholder' => t("Email"),
                                            'data-validation' => 'email',
                                            'required' => true
                                        ))
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div> <!--delivery-info-wrap-->


                    </div> <!--col-->


            </div> <!--row-->

            <div class="panel-footer top20">

                <button type="submit" class="orange-button medium rounded new-contacto-submit">
                    <?php echo t("Guardar") ?>
                </button>

                <button type="button" data-id=".new-contacto" 
                        class="close-modal green-button medium rounded"><?php echo t("Cancelar") ?></button>
            </div>
            </form>
        </div> <!--panel-footer-->



    </div> <!--body-->

</div> <!--modal-content-->
</div> <!--modal-dialog-->