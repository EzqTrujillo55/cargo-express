<!-- START SIDEBAR-->
<nav class="page-sidebar" id="sidebar">
    <div id="sidebar-collapse">

        <ul class="side-menu metismenu">
            <li>
                <a class="active" href="<?php echo Yii::app()->createUrl('/app/dashboard') ?>"><i class="sidebar-item-icon fa fa-th-large"></i>
                    <span class="nav-label">Dashboard</span>
                </a>
            </li>
            <li class="heading">PÁGINAS</li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('/app/rutas') ?>"><i class="sidebar-item-icon fa fa-map-pin"></i>
                    <span class="nav-label"> Rutas de Órdenes</span></a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('/app/todasOrdenes') ?>"><i class="sidebar-item-icon fa fa-edit"></i>
                    <span class="nav-label"> Todas las Órdenes</span></a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('/app/seleccionMultipleOrdenes') ?>"><i class="sidebar-item-icon fa fa-edit"></i>
                    <span class="nav-label"> Registro Movimientos</span></a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('/app/Mensajeros') ?>"><i class="sidebar-item-icon fa fa-user-secret"></i>
                    <span class="nav-label"> Mensajeros</span></a>
            </li>

            <li>
                <a href="<?php echo Yii::app()->createUrl('/app/clientes') ?>"><i class="sidebar-item-icon fa fa-users"></i>
                    <span class="nav-label"> Clientes</span></a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('/app/contactos') ?>"><i class="sidebar-item-icon fa fa-book"></i>
                    <span class="nav-label"> Direcciones</span></a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('/app/cargaMasiva') ?>"><i class="sidebar-item-icon fa fa-bars"></i>
                    <span class="nav-label"> Carga Masiva</span></a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('/app/usuarios') ?>"><i class="sidebar-item-icon fa fa-user-md"></i>
                    <span class="nav-label"> Usuarios</span></a>
            </li>
             <li>
                <a href="<?php echo Yii::app()->createUrl('/app/zonas') ?>"><i class="sidebar-item-icon fa fa-map-pin"></i>
                    <span class="nav-label"> Zonas</span></a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('/app/logout') ?>"><i class="sidebar-item-icon fa fa-power-off"></i>
                    <span class="nav-label"> Salir</span></a>
            </li>

        </ul>
    </div>
</nav>
<!-- END SIDEBAR-->
