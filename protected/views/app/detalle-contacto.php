<div class="modal detalle-contacto-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">

                <h4 id="mySmallModalLabel" class="modal-title">
                    <?php echo t("Identificación") ?> : <span class="identificacion"></span>
                    <input type="hidden" id="contacto_id" class="contacto_id">
                </h4> 
                <button aria-label="Cerrar" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true"><i class="ion-android-close"></i></span>
                </button> 
            </div> 
            <div class="modal-body">




                <ul id="tabs"> 
                    <li class="active"><?php echo Driver::t("Detalle") ?></li>
                </ul>

                <ul id="tab" >
                    <li class="active">    


                        <div class="row">
                            <div class="col-md-12">	
                                <div class="grey-box top10">
                                    <h5 class="bold dropoff_pickup"><?php echo t("Cliente") ?></h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Datos") ?>:</label>
                                                <span class="v nombres"></span>
                                            </div>
                                        </div>
                                    </div>  

                                </div>
                                <div class="grey-box top10">
                                    <h5 class="bold dropoff_pickup"><?php echo t("Datos Remitente") ?></h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Contacto") ?>:</label>
                                                <span class="v contacto"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Teléfono") ?>:</label>
                                                <span class="v telefono"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Email") ?>:</label>
                                                <span class="v email"></span>
                                            </div>
                                        </div> <!--col-->
                                    </div> <!--row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Empresa") ?>:</label>
                                                <span class="v empresa"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Ciudad") ?>:</label>
                                                <span class="v ciudad"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Zona") ?>:</label>
                                                <span class="v sector"></span>
                                            </div>
                                        </div>
                                    </div> <!--row-->

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Dirección") ?>:</label>
                                                <span class="v direccion"></span>
                                            </div>
                                        </div>
                                    </div>  


                                </div>
                            </div> <!--grey-box-->
                        </div><!-- dropoff-wrap-->

                    </li>
                </ul>

                <div class="panel-footer top20 task-action-button">       

                    <div class="action-1">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="#no" class="blue-button edit">
                                    <?php echo t("Editar") ?>
                                </a>
                            </div> <!--row-->
                            <div class="col-md-4">
                                <a href="#no" class="red-button delete">
                                    <?php echo t("Eliminar") ?>
                                </a>
                            </div> <!--row-->
                        </div> <!--row-->

                    </div>  <!--action 1-->

                </div> <!--panel-footer-->

            </div> <!--body-->

        </div> <!--modal-content-->
    </div> <!--modal-dialog-->
</div> <!--modal-->      