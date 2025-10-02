<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Menú Almacén"
    data-accordion="false" id="menuAlmacen">
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>Solicitudes <i class="nav-arrow fas fa-chevron-right"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/2solicitud_index.php'); return false;"><i
                        class="nav-icon fas fa-plus"></i>
                    <p>Registrar</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/8anular_solicitudes.php'); return false;"><i
                        class="nav-icon fas fa-ban"></i>
                    <p>Anular Solicitud</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/4aprobar_solicitudes.php'); return false;"><i
                        class="nav-icon fas fa-check"></i>
                    <p>Autorizar</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/9anular_autorizaciones.php'); return false;"><i
                        class="nav-icon fas fa-times"></i>
                    <p>Anular Autorización</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/6despachar_solicitudes.php'); return false;"><i
                        class="nav-icon fas fa-truck"></i>
                    <p>Despachar</p>
                </a></li>
        </ul>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-sign-in-alt"></i>
            <p>Ingresos <i class="nav-arrow fas fa-chevron-right"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/10ingreso_registrar.php'); return false;"><i
                        class="nav-icon fas fa-plus-square"></i>
                    <p>Registrar</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/12anular_ingresos.php'); return false;"><i
                        class="nav-icon fas fa-minus-square"></i>
                    <p>Anular</p>
                </a></li>
        </ul>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-archive"></i>
            <p>Registros <i class="nav-arrow fas fa-chevron-right"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/15articulos.php'); return false;"><i
                        class="nav-icon fas fa-box"></i>
                    <p>Artículos</p>
                </a></li>
        </ul>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-search"></i>
            <p>Consultas <i class="nav-arrow fas fa-chevron-right"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/3consulta_solicitudes.php'); return false;"><i
                        class="nav-icon fas fa-file-export"></i>
                    <p>Solicitudes</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/11consulta_ingresos.php'); return false;"><i
                        class="nav-icon fas fa-file-import"></i>
                    <p>Ingresos</p>
                </a></li>
        </ul>
    </li>
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Reportes <i class="nav-arrow fas fa-chevron-right"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/reportes/inventario.php'); return false;"><i
                        class="nav-icon fas fa-clipboard-list"></i>
                    <p>Inventario</p>
                </a></li>
            <li class="nav-item ms-4"><a href="#" class="nav-link"
                    onclick="parent.cargarEnDashboard('almacen/16despachos.php'); return false;"><i
                        class="nav-icon fas fa-truck"></i>
                    <p>Despachos</p>
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