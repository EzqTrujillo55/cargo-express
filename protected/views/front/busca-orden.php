<div class="modal fade detalle-orden-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true"><i class="ion-android-close"></i></span>
                </button> 
                <h4 id="mySmallModalLabel" class="modal-title">
                    <?php echo t("Orden ID") ?> : <span class="codigo_orden"></span>
                </h4> 
            </div>  

            <div class="modal-body">

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


            </div> <!--body-->

        </div> <!--modal-content-->
    </div> <!--modal-dialog-->
</div> <!--modal-->            