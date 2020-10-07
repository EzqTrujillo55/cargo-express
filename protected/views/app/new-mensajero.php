
<div class="modal fade new-mensajero" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true"><i class="ion-android-close"></i></span>
                </button> 
                <h4 id="mySmallModalLabel" class="modal-title">
                    <?php echo t("Mensajero") ?>
                </h4> 
            </div>  

            <div class="modal-body">

                <form id="frm" class="frm" method="POST" onsubmit="return false;">
                    <?php echo CHtml::hiddenField('action', 'addMensajero') ?>
                    <?php echo CHtml::hiddenField('id_mensajero', '') ?>
                    <?php echo CHtml::hiddenField('foto_perfil', '') ?>
                    <?php echo CHtml::hiddenField('cv', '') ?>
                    <div class="inner">
                        <div class="row top10">
                            <div class="col-md-9">

                                <?php
                                echo CHtml::textField('cedula', '', array(
                                    'placeholder' => t("Cédula"),
                                    'required' => true
                                ))
                                ?>

                            </div>
                            <div class="col-md-3">


                                <div class="profile-photo" id="upload-mensajero-photo">
                                    <p><?php echo t("Foto perfil") ?></p>
                                </div>   

                            </div>
                        </div> <!--row-->

                        <div class="row top10">
                            <div class="col-md-9">

                                <?php
                                echo CHtml::textField('nombre', '', array(
                                    'placeholder' => t("Nombre"),
                                    'required' => true
                                ))
                                ?>

                            </div>
                            <div class="col-md-3" style="text-align: right;"></div>

                        </div> <!--row-->

                        <div class="row top10">
                            <div class="col-md-9">

                                <?php
                                echo CHtml::textField('apellido', '', array(
                                    'placeholder' => t("Apellido"),
                                    'required' => true
                                ))
                                ?>

                            </div>
                            <div class="col-md-3" style="text-align: right;"></div>

                        </div> <!--row-->
                        <div class="row top10">
                            <div class="col-md-6 ">
                                <?php
                                echo CHtml::textField('email', '', array(
                                    'placeholder' => t("Email"),
                                    'data-validation' => 'email',
                                    'required' => true
                                ))
                                ?>
                            </div>
                            <div class="col-md-6 ">
                                <?php
                                echo CHtml::textField('telefono', '', array(
                                    'placeholder' => t("Teléfono"),
                                    //'class'=>"mobile_inputs",
                                    'required' => true,
                                    'maxlength' => 15
                                ))
                                ?>
                            </div>
                        </div> <!--row-->        


                        <div class="row top10">
                            <div class="col-md-12">
                                <p><?php echo t("Tipo de Transporte") ?></p>
                                <?php
                                echo CHtml::dropDownList('tipo_vehiculo', '', Driver::tipoTransporte(), array(
                                ));
                                ?>
                            </div>
                        </div>

                        <div class="transport_option">

                            <div class="row top10">
                                <div class="col-md-12"> 
                                    <p class="description"><?php echo t("Descripción Vehículo (Año,Modelo)") ?></p>
                                          <?php echo CHtml::textField('descripcion_vehiculo') ?>
                                </div> 
                            </div> <!--row-->

                            <div class="row top10">
                                <div class="col-md-6 ">
                                    <?php
                                    echo CHtml::textField('placa', '', array(
                                        'placeholder' => t("Placa")
                                    ))
                                    ?>
                                </div>
                                <div class="col-md-6 ">
                                    <?php
                                    echo CHtml::textField('color', '', array(
                                        'placeholder' => t("Color")
                                    ))
                                    ?>
                                </div>
                            </div> <!--row-->          
                        </div> <!--transport_option_1--> 
                        <div class="row top20">
                            <div class="col-md-12">
                                <p><?php echo t("Hoja de Vida") ?></p>
                                <div class="cv">

                                </div>  
                                <br/>
                                <a href="#"  id="upload-mensajero-cv" class="green-button medium rounded"><?php echo t("Subir CV") ?></a>
                            </div>
                        </div>
                        <div class="row top20">
                            <div class="col-md-12">
                                <p><?php echo t("Status") ?></p>
                                <?php
                                echo CHtml::dropDownList('status', '', Driver::mensajeroStatus(), array(
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
                                <button type="button" data-id=".new-mensajero" 
                                        class="close-modal red-button medium rounded"><?php echo t("Cancelar") ?></button>
                            </div>
                        </div>        


                    </div> <!--inner-->  
                </form>  

            </div> <!--body-->

        </div>
    </div>
</div>