

<?php $this->pageTitle = Yii::app()->name . ' - Carga Masiva Órdenes'; ?>

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
                <div class="col-md-6 border">
                    <b><?php echo t("Carga Masiva") ?></b>
                </div> <!--col-->        
            </div> <!--row-->
        </div> <!--nav_option-->

        <div class="inner">

            <?php if (isset($_POST) && $_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <?php if (!empty($msg)): ?>
                    <p class="text-danger"><?php echo $msg ?></p>
                <?php else : ?>
                    <?php //dump($error);dump($data);?>
                    <?php if (is_array($data) && count($data) >= 1): ?>
                        <?php
                        $total_inserted = 0;
                        $db = new DbExt;
                        foreach ($data as $val) {
                            if ($error == 0) {
                                $params = $val['data'];
                                if ($db->insertData("{{secuencial}}", array('prefijo' => $params['prefijo']))) {
                                    $secuencial = Yii::app()->db->getLastInsertID();

                                    $params['codigo_orden'] = $params['prefijo'] . $secuencial;
                                    unset($params['prefijo']);
                                    if ($db->insertData("{{orden}}", $params)) {
                                        $total_inserted++;
                                    }
                                }
                            }
                            foreach ($val['line'] as $val2) {
                                echo '<p class="text-muted">' . $val2 . '</p>';
                            }
                        }
                        if ($error > 0) {
                            echo '<p class="text-danger">' . t("CSV no procesado, por favor arregle los errores y vuelva a cargar") . '...</p>';
                        } else {
                            if ($total_inserted > 0) {
                                echo '<p class="text-success">' . t("CSV procesado exitosamente") . '</p>';
                                echo '<p class="text-success">' . t("Total registros insertados") . " : $total_inserted" . '</p>';
                            }
                        }
                        ?>
                        <p class="top20">
                            <a href="<?php echo Yii::app()->createUrl('/app/cargaMasiva') ?>">
                                <?php echo t("Atrás") ?></a>
                        </p>
                    <?php else : ?>
                        <p class="text-danger"><?php echo t("CSV está vacío") ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>

                <p><?php echo t("Cargue las órdenes en formato csv") ?></p>

                <form class="form-horizontal" method="post" enctype="multipart/form-data"  >

                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo Driver::t("CSV") ?></label>
                        <div class="col-sm-6">
                            <input type="file" name="file" id="file" />
                        </div>
                    </div>	  	 

                    <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-md-5">
                            <button type="submit" class="orange-button medium rounded"><?php echo t("Enviar") ?></button>    
                        </div>
                    </div>	  	

                <?php endif; ?>

            </form> 

            <?php if (isset($_POST) && $_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <?php else : ?>
                <p class="top30" style="margin-top:100px;">
                    <a href="<?php echo websiteUrl() . "/sample.csv" ?>">
                        <?php echo t("click aquí para descargar formato csv") ?>
                    </a>
                </p>

                <p class="text-muted to20"">
                    <span class="text-success"><?php echo t("Formato CSV") ?> : </span>
                    <?php echo t("tipo_servicio,origen,direccion_origen,zona_origen,remitente,telefono_remitente,destino,direccion_destino,zona_destino,recibe,telefono_recibe,PREFIJO_CLIENTE,detalle") ?>
                    <br/>
                    <br/>
                    <span class="text-info"><?php echo t("zona_origen y zona_destino deben ir de acuerdo al catálogo de base de datos, referirse al archivo de ejemplo") ?></span>
                    <br/>
                    <span class="text-info"><?php echo t("PREFIJO_CLIENTE debe ir de acuerdo al prefijo del cliente que debe ser único") ?></span>
                </p>
            <?php endif; ?>

        </div> <!--inner-->

    </div> <!--content_2-->
</div>
