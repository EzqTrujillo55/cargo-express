<?php $this->pageTitle = Yii::app()->name . ' - Zonas'; ?>
<?php
$this->renderPartial('/app/new-zona', array(
));
?>
<?php
$this->renderPartial('/app/new-locacion', array(
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

                </div> <!--col-->
                <div class="col-md-6 text-right">
                    <div class="row">
                        <div class="col-3"><a class="green-button left rounded add-new-locacion" href="#"> <?php echo t("Nueva Locación") ?> </a></div>
                        <div class="col-3"><a class="green-button left rounded add-new-zona" href="#"> <?php echo t("Nueva Zona") ?> </a></div>
                        <div class="col-3"><a class="orange-button left rounded" href="javascript:tableReload();"><?php echo t("Refresh") ?></a></div>
                    </div>




                </div> <!--col-->

            </div> <!--row-->
        </div> <!--nav_option-->
        <div class="inner">
            <b><?php echo t("Locaciones") ?></b>
            <form id="frm_table_ciudad" class="frm_table">
                <?php echo CHtml::hiddenField('action', 'locacionList') ?>
                <table id="table_list1" class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo t("Nombre") ?></th> 
                            <th><?php echo t("Padre") ?></th>  
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>     
                    </tbody>     
                </table>
            </form>
        </div>
        <div class="inner top20">
            <b><?php echo t("Rutas") ?></b>
            <form id="frm_table" class="frm_table">
                <?php echo CHtml::hiddenField('action', 'zonasList') ?>
                <table id="table_list" class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo t("Zona") ?></th> 
                            <th><?php echo t("Zona Padre") ?></th>
                            <th><?php echo t("Ciudad") ?></th>     
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
                Yii::app()->baseUrl . '/assets/zona.js', CClientScript::POS_END
        );
        ?>
    </div> <!--content_2-->
</div>







</div> <!--body-->

</div> <!--modal-content-->
</div> <!--modal-dialog-->