<div class="modal detalle-orden-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">

                <h4 id="mySmallModalLabel" class="modal-title">
                    <?php echo t("Código Orden") ?> : <span class="codigo_orden"></span>
                    <input type="hidden" id="orden_id" class="orden_id">
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
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Estado") ?> :</label>
                                                <span class="v estado"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Código") ?> :</label>
                                                <span class="v codigo_orden"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Tipo Servicio") ?> :</label>
                                                <p class="v tipo_servicio"></p>
                                            </div>    
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Fecha Creación") ?> :</label>
                                                <p class="v date_created"></p>
                                            </div>    
                                        </div> <!--col-->
                                        <div class="col-md-12">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Detalle") ?> :</label>
                                                <span class="v detalle"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Fecha Envío") ?> :</label>
                                                <p class="v fecha_envio"></p>
                                            </div>    	           
                                        </div> <!--col-->
                                    </div>
                                </div><!-- white-box-->
                            </div> <!--col-->
                        </div> <!--row-->

                        <div class="row">
                            <div class="col-md-12">	   
                                <div class="grey-box top10">
                                    <h5 class="bold dropoff_pickup"><?php echo t("Datos Remitente") ?></h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Remitente") ?>:</label>
                                                <span class="v remitente"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Teléfono") ?>:</label>
                                                <span class="v telefono_remitente"></span>
                                            </div>
                                        </div> <!--col-->
                                    </div> <!--row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Persona o Empresa Origen") ?>:</label>
                                                <span class="v origen"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Ciudad") ?>:</label>
                                                <span class="v ciudad_origen"></span>
                                            </div>
                                        </div> <!--col-->
                                    </div> <!--row-->

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Dirección") ?>:</label>
                                                <span class="v direccion_origen"></span>
                                            </div>
                                        </div>
                                    </div>  

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Zona") ?>:</label>
                                                <span class="v zona_origen_nombre"></span>
                                            </div>
                                        </div>
                                    </div>  

                                </div>
                            </div> <!--grey-box-->
                        </div><!-- dropoff-wrap-->

                        <div class="row">
                            <div class="col-md-12">   
                                <div class="grey-box top10">
                                    <h5 class="bold dropoff_pickup"><?php echo t("Datos Destino") ?></h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Destinatario") ?>:</label>
                                                <span class="v recibe"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Teléfono") ?>:</label>
                                                <span class="v telefono_recibe"></span>
                                            </div>
                                        </div> <!--col-->
                                    </div> <!--row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Persona o Empresa Destino") ?>:</label>
                                                <span class="v destino"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Ciudad") ?>:</label>
                                                <span class="v ciudad_destino"></span>
                                            </div>
                                        </div> <!--col-->
                                    </div> <!--row-->

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Dirección") ?>:</label>
                                                <span class="v direccion_destino"></span>
                                            </div>
                                        </div>
                                    </div>  

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo t("Zona") ?>:</label>
                                                <span class="v zona_destino_nombre"></span>
                                            </div>
                                        </div>
                                    </div>  
                                </div>

                            </div> <!--grey-box-->
                        </div><!-- dropoff-wrap-->

                    </li>
                    <li>
                        <div class="top10" id="orden-history">

                            <!--<div class="grey-box top10">
                            <div class="row">
                              <div class="col-md-7">
                              Status updated from Unassigned to Assigned
                              </div>
                              <div class="col-md-5">
                                <i class="ion-ios-clock-outline"></i> 5/4/2016 07:52 am <br/>
                                <i class="ion-ios-location"></i>  <a href="#">Location on Map</a>
                              </div>
                            </div>  
                            </div>--> <!--box-->

                        </div> <!--task-history-->
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
                                <a href="#no" class="green-button pagar">
                                    <?php echo t("Pagar") ?>
                                </a>
                            </div> <!--row-->
                        </div> <!--row-->

                    </div>  <!--action 1-->

                </div> <!--panel-footer-->

            </div> <!--body-->

        </div> <!--modal-content-->
    </div> <!--modal-dialog-->
</div> <!--modal-->      