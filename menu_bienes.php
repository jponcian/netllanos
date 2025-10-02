<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Menú Bienes"
    data-accordion="false" id="menuBienes">
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-exchange-alt"></i>
            <p>Reasignaciones <i class="nav-arrow fas fa-chevron-right"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/4reasignacion_bien.php'); return false;"><i
                        class="nav-icon fas fa-plus"></i>
                    <p>Generar</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/5reasignacion_bien.php'); return false;"><i
                        class="nav-icon fas fa-share-square"></i>
                    <p>Enviar a Bienes</p>
                </a></li>
        </ul>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-people-arrows"></i>
            <p>Movimientos Internos <i class="nav-arrow fas fa-chevron-right"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/11_mov_interno_bien.php'); return false;"><i
                        class="nav-icon fas fa-plus"></i>
                    <p>Generar</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/12_mov_interno_sol_bien.php'); return false;"><i
                        class="nav-icon fas fa-share-square"></i>
                    <p>Enviar a Bienes</p>
                </a></li>
        </ul>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-user-tie"></i>
            <p>Coordinador (Bienes) <i class="nav-arrow fas fa-chevron-right"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/6aceptar_reasignacion.php'); return false;"><i
                        class="nav-icon fas fa-check-double"></i>
                    <p>Aprobar Reasignación</p>
                </a></li>


            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/16_rpu.php'); return false;"><i
                        class="nav-icon fas fa-user-check"></i>
                    <p>Responsabilidad por Uso</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/15_inventariar.php'); return false;"><i
                        class="nav-icon fas fa-boxes"></i>
                    <p>Inventariar</p>
                </a></li>
        </ul>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-cogs"></i>
            <p>Opciones <i class="nav-arrow fas fa-chevron-right"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/3ingreso_bienes.php'); return false;"><i
                        class="nav-icon fas fa-plus-square"></i>
                    <p>Gestión Bien Nacional</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/9historial_bien.php'); return false;"><i
                        class="nav-icon fas fa-search"></i>
                    <p>Localizar Bien Nacional</p>
                </a></li>
            <!-- <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/2areas.php'); return false;"><i
                        class="nav-icon fas fa-map-marker-alt"></i>
                    <p>Registrar Área</p>
                </a></li> -->
            <!-- <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/1categorias.php'); return false;"><i
                        class="nav-icon fas fa-tags"></i>
                    <p>Registrar Categoría</p>
                </a></li> -->
        </ul>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Reportes <i class="nav-arrow fas fa-chevron-right"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/7menu_reportes.php'); return false;"><i
                        class="nav-icon fas fa-clipboard-list"></i>
                    <p>Inventario</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('bienes/8menu_reportes.php'); return false;"><i
                        class="nav-icon fas fa-clipboard-list"></i>
                    <p>Reasignaciones</p>
                </a></li>

        </ul>
    </li>
    <li class="nav-item">
        <a href="salida.php" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Salida</p>
        </a>
    </li>
</ul>