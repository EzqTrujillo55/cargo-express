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
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-success color-white widget-stat">
                    <div class="ibox-body">
                        <h2 id='total_ordenes' class="m-b-5 font-strong"></h2>
                        <div class="m-b-5">TOTAL ÓRDENES</div><i class="ti-shopping-cart widget-stat-icon"></i>
                        <div><i class="fa fa-level-up m-r-5"></i><small></small></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-info color-white widget-stat">
                    <div class="ibox-body">
                        <h2 id='total_ordenes_creadas' class="m-b-5 font-strong"></h2>
                        <div class="m-b-5">TOTAL ÓRDENES CREADAS</div><i class="ti-bar-chart widget-stat-icon"></i>
                        <div><i class="fa fa-level-up m-r-5"></i><small></small></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-warning color-white widget-stat">
                    <div class="ibox-body">
                        <h2 id='total_ordenes_completadas' class="m-b-5 font-strong"></h2>
                        <div class="m-b-5">TOTAL ÓRDENES COMPLETADAS</div><i class="fa fa-money widget-stat-icon"></i>
                        <div><i class="fa fa-level-up m-r-5"></i><small></small></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-danger color-white widget-stat">
                    <div class="ibox-body">
                        <h2 id='total_ordenes_en_ruta'  class="m-b-5 font-strong"></h2>
                        <div class="m-b-5">TOTAL ÓRDENES EN RUTA</div><i class="ti-user widget-stat-icon"></i>
                        <div><i class="fa fa-level-up m-r-5"></i><small></small></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Provincias</div>
                    </div>
                    <div class="ibox-body">
                        <div id="world-map" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Estadísticas</div>
                    </div>
                    <div class="ibox-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <canvas id="doughnut_chart" style="height:160px;"></canvas>
                            </div>
                            <div class="col-md-6">
                                <div id='stat_completadas' class="m-b-20 text-success"><i class="fa fa-circle-o m-r-10"></i></div>
                                <div id='stat_creadas' class="m-b-20 text-info"><i class="fa fa-circle-o m-r-10"></i></div>
                                <div id='stat_en_ruta' class="m-b-20 text-warning"><i class="fa fa-circle-o m-r-10"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .visitors-table tbody tr td:last-child {
                display: flex;
                align-items: center;
            }

            .visitors-table .progress {
                flex: 1;
            }

            .visitors-table .progress-parcent {
                text-align: right;
                margin-left: 10px;
            }
        </style>
        <?php
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/dashboard/vendors/jvectormap/jquery.vmap.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/dashboard/vendors/jvectormap/maps/jquery.vmap.ecuador.js', CClientScript::POS_END
        );


        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/dashboard/js/scripts/dashboard.js', CClientScript::POS_END
        );
        ?>
    </div>
    <!-- END PAGE CONTENT-->
</div>