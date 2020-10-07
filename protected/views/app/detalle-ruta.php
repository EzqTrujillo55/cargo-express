<div class="modal detalle-ruta-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">

                <h4 id="mySmallModalLabel" class="modal-title">
                    <?php echo t("Fecha Ruta") ?> : <span class="fecha_ruta"></span>
                    <input type="hidden" id="id_ruta" class="id_ruta">
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
                                                <label class="font-medium"><?php echo Driver::t("Fecha Ruta") ?> :</label>
                                                <span class="v fecha_ruta"></span>
                                            </div>
                                        </div> <!--col-->
                                        <div class="col-md-6">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Mensajero Asignado") ?> :</label>
                                                <p class="v nombres"></p>
                                            </div>    	           
                                        </div> <!--col-->
                                        <div class="col-md-12">
                                            <div class="form-group left-form-group">
                                                <label class="font-medium"><?php echo Driver::t("Detalle") ?> :</label>
                                                <span class="v detalle"></span>
                                            </div>
                                        </div> <!--col-->
                                       
                                       
                                    </div>
                                </div><!-- white-box-->
                            </div> <!--col-->
                        </div> <!--row-->

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
                                <a href="#no" class="blue-button administrar-ordenes">
                                    <?php echo t("Administrar Órdenes") ?>
                                </a>
                            </div> <!--row-->
                            <div class="col-md-4">
                                <a href="#no" class="green-button cambiar-estado">
                                    <?php echo t("Cambiar Estado") ?>
                                </a>
                            </div> <!--row-->
                        </div> <!--row-->

                    </div>  <!--action 1-->

                </div> <!--panel-footer-->

            </div> <!--body-->

        </div> <!--modal-content-->
    </div> <!--modal-dialog-->
</div> <!--modal-->      