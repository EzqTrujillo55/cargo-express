<?php $this->pageTitle = Yii::app()->name . ' - Órdenes por ruta'; ?>
<?php
$this->renderPartial('/app/asignar-ruta-orden', array(
));
?> 
<?php
$this->renderPartial('/app/detalle-orden-ruta', array(
));
?> 
<?php
$this->renderPartial('/app/orden-change-status', array(
));
?> 
<?php
$this->renderPartial('/app/migrar-de-ruta-orden', array(
));
?>
<?php
$this->renderPartial('/app/cambiar-fecha-envio', array(
));
?>
<?php
$this->renderPartial('/app/cambiar-fecha-entrega', array(
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
                    <b><?php echo t("Órdenes por Ruta") ?></b>
                </div> <!--col-->
                <div class="col-md-6 text-right">
                    <div class="row">
                        <div class="col-3"><a class="green-button left rounded asignar-orden" href="#"> <?php echo t("Asignar Orden") ?> </a></div>
                        <div class="col-3"><a class="orange-button left rounded" href="javascript:tableReload();"><?php echo t("Refresh") ?></a></div>
                        <div class="col-3"><a class="green-button left rounded" href="<?php echo Yii::app()->createUrl('/app/export_pedidos_por_ruta') ?>"><?php echo t("Export") ?></a>  </div>
                        <div class="col-3"><a class="blue-button left rounded" id="btn_busqueda_avanzada" href="#"> <i class="fa fa-search"></i> </a></div>
                    </div>




                </div> <!--col-->
                <?php
                Yii::app()->clientScript->registerScriptFile(
                        Yii::app()->baseUrl . '/assets/ordenes-por-ruta.js', CClientScript::POS_END
                );
                ?>
            </div> <!--row-->
        </div> <!--nav_option-->

        <div class="inner">
            <div class="row">
                <div class="col-md-6">
                    <div class="grey-box top10">
                        <form id="frm_busca_orden"  class="frm_table">
                            <?php echo CHtml::hiddenField('action', 'buscarOrden') ?>
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="form-group left-form-group">
                                        <label class="font-medium"><?php echo Driver::t("Buscar Orden") ?> :</label>
                                    </div>
                                </div> <!--col-->
                                <div class="col-md-6">
                                    <?php
                                    echo CHtml::textField('codigo_orden_busqueda', '', array(
                                        'placeholder' => t("Orden"),
                                        'required' => true
                                    ))
                                    ?>    
                                </div> <!--col-->
                                <div class="col-md-2">
                                    <button type="submit" class="blue-button medium rounded"><?php echo t("Buscar") ?></button>

                                </div>
                            </div><!-- white-box-->
                        </form>
                    </div>
                    <div class="col-md-6">

                    </div> <!--col-->
                </div> <!--row-->
                <div class="col-md-6">
                    <div class="grey-box top10">
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group left-form-group">
                                    <label class="font-medium"><?php echo Driver::t("Mensajero Asignado") ?> :</label>
                                    <span class="v mensajero"></span>
                                </div>
                            </div> <!--col-->
                            <div class="col-md-6">
                                <div class="v datos"></div>           
                            </div> <!--col-->
                            <div class="col-md-12">
                                <div class="form-group left-form-group">
                                    <label class="font-medium"><?php echo Driver::t("Fecha Ruta") ?> :</label>
                                    <span class="v fecha_ruta"></span>
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

            <div class="row">

                <form id="frm_table_ordenes_por_ruta" class="frm_table">
                    <?php echo CHtml::hiddenField('action', 'pedidosPorRutaList') ?>
                    <table id="table_list" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="10%"><?php echo t("Código") ?></th>
                                <th><?php echo t("Persona o Empresa Origen") ?></th>
                                <th><?php echo t("Dirección Origen") ?></th>
                                <th><?php echo t("Persona o Empresa Destino") ?></th>
                                <th><?php echo t("Dirección Destino") ?></th>
                                <th><?php echo t("Tipo Servicio") ?></th>
                                <th><?php echo t("Fecha Creación") ?></th>
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
                    Yii::app()->baseUrl . '/assets/ordenes-por-ruta.js', CClientScript::POS_END
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
                        <form  id="frm_table_ordenes_por_ruta_busqueda" class="frm_table">
                            <?php echo CHtml::hiddenField('action', 'pedidosPorRutaListFilteredList') ?>
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
                                        <div class="col-3" >
                                            <p style="padding-left: 15px;"> Código Orden</p>

                                        </div>
                                        <div class="col-3">
                                            <div class="input-select">
                                                <?php
                                                echo CHtml::textField('codigo_orden', '', array(
                                                    'required' => false
                                                ))
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-3">

                                        </div>
                                        <div class="col-3">
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

                                            <p style="padding-left: 15px;"> Fecha Envío Desde</p>

                                        </div>
                                        <div class="col-3">
                                            <input autocomplete="off" id="fecha_envio_desde" name="fecha_envio_desde" type="text" />
                                        </div>

                                        <div class="col-3" style="padding-left: -15px;">

                                            <p style="padding-left: 15px;"> Fecha Envío Hasta</p>
                                        </div>
                                        <div class="col-3">
                                            <input autocomplete="off" id="fecha_envio_hasta" name="fecha_envio_hasta" type="text" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3" style="padding-left: -15px;">

                                            <p style="padding-left: 15px;"> Fecha Entrega Desde</p>

                                        </div>
                                        <div class="col-3">
                                            <input autocomplete="off" id="fecha_envio_desde" name="fecha_entrega_desde" type="text" />
                                        </div>

                                        <div class="col-3" style="padding-left: -15px;">

                                            <p style="padding-left: 15px;"> Fecha Entrega Hasta</p>
                                        </div>
                                        <div class="col-3">
                                            <input autocomplete="off" id="fecha_envio_hasta" name="fecha_entrega_hasta" type="text" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3" >
                                            <p style="padding-left: 15px;"> Tipo Servicio</p>

                                        </div>
                                        <div class="col-3">
                                            <div class="input-select">
                                                <?php
                                                echo CHtml::dropDownList('tipo_servicio', '', (array) Driver::tipoServicioList(), array(
                                                    'class' => "status tipo_servicio"
                                                ))
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <p style="padding-left: 15px;">  Estado</p>
                                        </div>
                                        <div class="col-3">
                                            <div class="input-select">
                                                <?php
                                                echo CHtml::dropDownList('estado', '', (array) Driver::statusordenList(), array(
                                                    'class' => "status estado"
                                                ))
                                                ?>
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
                            </div>
                        </form>
                    </div>
                </div> <!--panel-footer-->



            </div> <!--body-->

        </div> <!--modal-content-->
    </div> <!--modal-dialog-->