 <!-- START SIDEBAR-->
        <nav class="page-sidebar" id="sidebar">
            <div id="sidebar-collapse">
                
                <ul class="side-menu metismenu">
                    <li>
                        <a class="active" href="<?php echo Yii::app()->createUrl('/user/index') ?>"><i class="sidebar-item-icon fa fa-th-large"></i>
                            <span class="nav-label">Dashboard</span>
                        </a>
                    </li>
                     <li class="heading">PÁGINAS</li>
                    <li>
                        <a href="<?php echo Yii::app()->createUrl('/user/misOrdenes') ?>"><i class="sidebar-item-icon fa fa-edit"></i>
                            <span class="nav-label"> Mis Órdenes</span></a>
                    </li>
                    <li>
                        <a href="<?php echo Yii::app()->createUrl('/user/misContactos') ?>"><i class="sidebar-item-icon fa fa-table"></i>
                            <span class="nav-label"> Mis Contactos</span></a>
                    </li>
                     <li>
                        <a href="<?php echo Yii::app()->createUrl('/user/logout') ?>"><i class="sidebar-item-icon fa fa-power-off"></i>
                            <span class="nav-label"> Salir</span></a>
                    </li>
                
                </ul>
            </div>
        </nav>
        <!-- END SIDEBAR-->
