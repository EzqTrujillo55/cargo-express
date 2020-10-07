<?php $this->pageTitle = Yii::app()->name . ' - Mis Contactos'; ?>
//<?php
$this->renderPartial('/user/detalle-contacto', array(
));
?>
<?php
$this->renderPartial('/user/new-contacto', array(
));
?>
<?php
$this->renderPartial('/tpl/layout1_top_User', array(
));
?> 
<?php
$this->renderPartial('/tpl/menuUser', array(
));
?> 
<div class="content-wrapper">
    <!-- START PAGE CONTENT-->
    <div class="page-content fade-in-up">

        <div class="nav_option">
            <div class="row">
                <div class="col-md-6 ">
                    <b><?php echo t("Mis Contactos") ?></b>
                </div> <!--col-->
                <div class="col-md-6 text-right">
                    <div class="row">
                        <div class="col-3"><a class="green-button left rounded add-new-contacto" href="#"> <?php echo t("Nuevo Contacto") ?> </a></div>
                        <div class="col-3"><a class="orange-button left rounded" href="javascript:tableReload();"><?php echo t("Refresh") ?></a></div>
                        <div class="col-3">  <a class="blue-button left rounded" id="btn_busqueda_avanzada" href="#"> <i class="fa fa-search"></i> </a></div>
                    </div>




                </div> <!--col-->

            </div> <!--row-->
        </div> <!--nav_option-->

        <div class="inner">
            <form id="frm_table_contactos_user" class="frm_table">
                <?php echo CHtml::hiddenField('action', 'misContactosList') ?>
                <table id="table_list" class="table table-hover">
                    <thead>
                        <tr>     
                            <th><?php echo t("Identificación") ?></th>
                            <th><?php echo t("Empresa") ?></th>
                            <th><?php echo t("Email") ?></th>
                            <th><?php echo t("Ciudad") ?></th>
                            <th><?php echo t("Dirección") ?></th>
                            <th><?php echo t("Sector") ?></th>
                            <th><?php echo t("Contacto") ?></th>    
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>     
                    </tbody>     
                </table>
            </form>
        </div>

    </div> <!--content_2-->

</div> <!--parent-wrapper-->



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
                    <form  id="frm_table_ordenes_user_busqueda" class="frm_table">
                        <?php echo CHtml::hiddenField('action', 'misContactosFilteredList') ?>
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

                                <div class="row">

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