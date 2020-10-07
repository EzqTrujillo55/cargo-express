<?php $this->pageTitle = Yii::app()->name . ' - Rutas'; ?>
<?php
$this->renderPartial('/app/detalle-ruta', array(
));
?>
<?php
$this->renderPartial('/app/new-ruta', array(
));
?>
<?php
$this->renderPartial('/app/ruta-change-status', array(
));
?> 
<?php
$this->renderPartial('/tpl/layout1_top', array(
));
?> 
<?php
$this->renderPartial('/tpl/menu', array(
));
?> 

<div class="content-wrapper">
    <!-- START PAGE CONTENT-->
    <div class="page-content fade-in-up">

        <div class="nav_option">
            <div class="row">
                <div class="col-md-6 ">
                    <b><?php echo t("Rutas") ?></b>
                </div> <!--col-->
                <div class="col-md-6 text-right">
                    <div class="row">
                        <div class="col-3"><a class="green-button left rounded add-new-ruta" href="#"> <?php echo t("Nueva Ruta") ?> </a></div>
                        <div class="col-3"><a class="orange-button left rounded" href="javascript:tableReload();"><?php echo t("Refresh") ?></a></div>
                        <div class="col-3"><a class="blue-button left rounded" id="btn_busqueda_avanzada" href="#"> <i class="fa fa-search"></i> </a></div>
                    </div>




                </div> <!--col-->

            </div> <!--row-->
        </div> <!--nav_option-->

        <div class="inner">
            <form id="frm_table" class="frm_table">
                <?php echo CHtml::hiddenField('action', 'rutasList') ?>
                <table id="table_list" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="10%"><?php echo t("Fecha") ?></th> 
                            <th><?php echo t("Detalle") ?></th>
                            <th><?php echo t("Mensajero") ?></th>
                            <th><?php echo t("Estado") ?></th>      
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>     
                    </tbody>     
                </table>
            </form>
        </div>
        <?php
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/ruta.js', CClientScript::POS_END
        );
        ?>
    </div> <!--content_2-->
</div>



<div class="modal table_busqueda" id="table_busqueda" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">

                <h4 id="mySmallModalLabel" class="modal-title">
                    <?php echo t("Búsqueda Avanzada") ?>
                </h4> 
                <button aria-label="Cerrar" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true"><i class="ion-android-close"></i></span>
                </button> 
            </div>  

            <div class="modal-body">
                <div class="s009">
                    <form  id="frm_table_rutas_busqueda" class="frm_table">
                        <?php echo CHtml::hiddenField('action', 'rutasFilteredList') ?>
                        <div class="inner-form">
                            <div class="basic-search">
                                <div class="input-field">
                                    <input id="search" name="search" type="text" placeholder="Palabras clave..." />
                                    <div class="icon-wrap">
                                        <svg class="svg-inline--fa fa-search fa-w-16" fill="#ccc" aria-hidden="true" data-prefix="fas" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                        <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="advance-search">
                                <span class="desc">BÚSQUEDA AVANZADA</span>
                                <div class="row">

                                    <div class="col-6">
                                        <p style="padding-left: 15px;"> Mensajero</p>
                                    </div>
                                    <div class="col-6">
                                        <?php
                                        if ($mensajeros_disponibles_list = Driver::getMensajerosDisponiblesList()) {
                                            $mensajeros_disponibles_list = Driver::toList($mensajeros_disponibles_list, 'id_mensajero', 'nombres', Driver::t("Por favor seleccione un mensajero encargado"));
                                        }

                                        echo CHtml::dropDownList('id_mensajero', '', (array) $mensajeros_disponibles_list
                                                , array(
                                            'class' => "id_mensajero "
                                        ))
                                        ?>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-3" style="padding-left: -15px;">


                                        <p style="padding-left: 15px;"> Fecha Creación Desde</p>

                                    </div>
                                    <div class="col-3">
                                        <input autocomplete="off" id="fecha_creacion_desde" name="fecha_creacion_desde" type="text" />

                                    </div>

                                    <div class="col-3" style="padding-left: -15px;">


                                        <p style="padding-left: 15px;"> Fecha Creación Hasta</p>
                                    </div>
                                    <div class="col-3">
                                        <input autocomplete="off" id="fecha_creacion_hasta" name="fecha_creacion_hasta" type="text"  />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-3" style="padding-left: -15px;">


                                        <p style="padding-left: 15px;"> Fecha Ruta Desde</p>

                                    </div>
                                    <div class="col-3">
                                        <input autocomplete="off" id="fecha_ruta_desde" name="fecha_ruta_desde" type="text" />

                                    </div>

                                    <div class="col-3" style="padding-left: -15px;">


                                        <p style="padding-left: 15px;"> Fecha Ruta Hasta</p>
                                    </div>
                                    <div class="col-3">
                                        <input autocomplete="off" id="fecha_ruta_hasta" name="fecha_ruta_hasta" type="text"  />
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-3">
                                        <p style="padding-left: 15px;">  Estado</p>
                                    </div>
                                    <div class="col-3">
                                        <div class="input-select">
                                            <?php
                                            echo CHtml::dropDownList('status', '', Driver::rutaStatus(), array(
                                                'required' => false
                                            ));
                                            ?>
                                        </div>
                                    </div>

                                </div>
                                <div class="row third">
                                    <div class="input-field">
                                        <div class="group-btn">
                                            <button class="btn-limpiar" id="limpiar">LIMPIAR</button>
                                            <button type="submit" class="btn-search">BUSCAR</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div> <!--panel-footer-->



        </div> <!--body-->

    </div> <!--modal-content-->
</div> <!--modal-dialog-->