
<?php $this->pageTitle = Yii::app()->name . ' - Todos los Mensajeros'; ?>

<?php
$this->renderPartial('/app/new-mensajero', array(
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
                    <b><?php echo t("Todos los Mensajeros") ?></b>
                </div> <!--col-->
                <div class="col-md-6 text-right">
                    <div class="row">
                        <div class="col-6"><a class="green-button left rounded add-new-mensajero" href="#"> <?php echo t("Nuevo Mensajero") ?> </a></div>
                        <div class="col-6"><a class="orange-button left rounded" href="javascript:tableReload();"><?php echo t("Refresh") ?></a></div>
                    </div>




                </div> <!--col-->

            </div> <!--row-->
        </div> <!--nav_option-->

        <div class="inner">
            <form id="frm_table" class="frm_table">
                <?php echo CHtml::hiddenField('action', 'mensajeroList') ?>
                <table id="table_list" class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%"></th>
                            <th width="5%"><?php echo t("Cédula") ?></th>     
                            <th width="10%"><?php echo t("Nombres") ?></th>
                            <th width="10%"><?php echo t("Email") ?></th>
                            <th width="10%"><?php echo t("Teléfono") ?></th>
                            <th width="5%"><?php echo t("Vehículo") ?></th>
                            <th width="5%"><?php echo t("Placa") ?></th>
                            <th width="10%"><?php echo t("CV") ?></th>
                            <th width="5%"><?php echo t("Status") ?></th>
                            <th width="15%"></th>
                        </tr>
                    </thead>
                    <tbody>     
                    </tbody>     
                </table>
            </form>
        </div>
        <?php
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/mensajero.js', CClientScript::POS_END
        );
        ?>
    </div> <!--content_2-->
</div>
