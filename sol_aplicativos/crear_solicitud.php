<?php
session_start();
include "../conexion.php";
include "../auxiliar.php";

// Determinar si puede ver todos los funcionarios
$verTodos = (!empty($_SESSION['ADMINISTRADOR']) && intval($_SESSION['ADMINISTRADOR']) > 0) ||
    ($_SESSION['SEDE_USUARIO'] == 1 && $_SESSION['DIVISION_USUARIO'] == 8);

// Conectar a la BD losllanos en localhost puerto 3306 para funcionarios
try {
    $pdo_func = new PDO('mysql:host=localhost;port=3306;dbname=illanos;charset=utf8', 'root', '');
    $pdo_func->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error conectando a la BD losllanos en localhost puerto 3306: ' . $e->getMessage() . '<br>';
    $pdo_func = null; // Set to null to avoid queries
}

// // Conectar a la BD losllanos en puerto 3307 para funcionarios
// try {
//     $pdo_func = new PDO('mysql:host=rellanif023;port=3307;dbname=illanos;charset=utf8', 'jsaez', 'Seniat7980');
//     $pdo_func->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die('Error conectando a la BD losllanos en puerto 3307: ' . $e->getMessage());
// }

// Obtener funcionarios de la BD losllanos
$funcionarios = [];
if ($pdo_func) {
    try {
        if ($verTodos) {
            $stmt = $pdo_func->query("SELECT f.cedula, CONCAT(f.nombre, ' ', f.apellido) as nombre, d.id_divi as division, dv.descripcion as area FROM funcionarios f INNER JOIN designacion d ON f.id = d.id_func INNER JOIN division dv ON d.id_divi = dv.id WHERE d.estatus = 'activo' ORDER BY f.nombre");
            $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo_func->prepare("SELECT f.cedula, CONCAT(f.nombre, ' ', f.apellido) as nombre, d.id_divi as division, dv.descripcion as area FROM funcionarios f INNER JOIN designacion d ON f.id = d.id_func INNER JOIN division dv ON d.id_divi = dv.id WHERE d.estatus = 'activo' AND d.id_divi = ? ORDER BY f.nombre");
            $stmt->execute([$_SESSION['DIVISION_USUARIO']]);
            $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        echo 'Error querying funcionarios: ' . $e->getMessage() . '<br>';
    }
} else {
    echo "No connection to losllanos<br>";
}

// Obtener aplicativos
$aplicativos = [];
$sql = "SELECT id, aplicativo, rol, id_div FROM a_matriz_aplicativos ORDER BY aplicativo";
if ($stmt = $_SESSION['conexionsqli']->prepare($sql)) {
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $aplicativos[] = $row;
    }
    $stmt->close();
}
?>
<html>

<head>
    <title>Crear Solicitud de Aplicativos</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../lib/jquery/jquery.min.js"></script>
    <script src="../lib/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../lib/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../lib/select2/select2.min.css">
    <script src="../lib/select2/select2.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #1e7e34;
            border-color: #1e7e34;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #545b62;
            border-color: #545b62;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <h4>Crear Nueva Solicitud de Aplicativos</h4>
        <div id="funcionarios-container">
            <!-- Aquí se agregarán los funcionarios -->
        </div>
        <button class="btn btn-secondary" onclick="agregarFuncionario()">Agregar Funcionario</button>
        <button class="btn btn-success" onclick="generarSolicitud()">Generar Solicitud</button>
    </div>

    <script>
        let funcionariosAgregados = [];
        const listaFuncionarios = <?php echo json_encode($funcionarios); ?>;
        const listaAplicativos = <?php echo json_encode($aplicativos); ?>;

        function agregarFuncionario() {
            const container = document.getElementById('funcionarios-container');
            const index = funcionariosAgregados.length;
            funcionariosAgregados.push({
                cedula: '',
                aplicativos: []
            });

            const div = document.createElement('div');
            div.className = 'card mb-3';
            div.innerHTML = `
        <div class="card-header">
          Funcionario ${index + 1}
          <button class="btn btn-sm btn-danger float-end" onclick="removerFuncionario(${index})">X</button>
        </div>
        <div class="card-body">
          <select id="select-func-${index}" class="form-select mb-3" onchange="seleccionarFuncionario(${index}, this.value)">
            <option value="">Seleccionar Funcionario</option>
            ${listaFuncionarios.map(f => `<option value="${f.cedula}">${f.nombre} (${f.area})</option>`).join('')}
          </select>
          <div id="aplicativos-${index}"></div>
        </div>
      `;
            container.appendChild(div);
            // Inicializar select2
            $(`#select-func-${index}`).select2();
        }

        function seleccionarFuncionario(index, cedula) {
            funcionariosAgregados[index].cedula = cedula;
            const func = listaFuncionarios.find(f => f.cedula == cedula);
            if (func) {
                const div = document.getElementById(`aplicativos-${index}`);
                const aplicativosFiltrados = listaAplicativos.filter(app => app.id_div.includes('-' + func.division + '-'));
                div.innerHTML = `
          <h6 class="mt-3">Aplicativos Disponibles</h6>
          <table class="table table-striped table-hover table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Aplicativo</th>
                <th>Rol</th>
                <th>Seleccionar</th>
              </tr>
            </thead>
            <tbody>
              ${aplicativosFiltrados.map((app, idx) => `
                <tr>
                  <td>${idx + 1}</td>
                  <td>${app.aplicativo}</td>
                  <td>${app.rol}</td>
                  <td><input type="checkbox" onchange="toggleAplicativo(${index}, ${app.id}, this.checked)" class="form-check-input"></td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
            }
        }

        function toggleAplicativo(index, appId, checked) {
            if (checked) {
                if (!funcionariosAgregados[index].aplicativos.includes(appId)) {
                    funcionariosAgregados[index].aplicativos.push(appId);
                }
            } else {
                funcionariosAgregados[index].aplicativos = funcionariosAgregados[index].aplicativos.filter(id => id != appId);
            }
        }

        function removerFuncionario(index) {
            funcionariosAgregados.splice(index, 1);
            const container = document.getElementById('funcionarios-container');
            container.innerHTML = '';
            funcionariosAgregados.forEach((_, i) => {
                // Re-agregar los restantes
                const div = document.createElement('div');
                div.className = 'card mb-3';
                div.innerHTML = `
          <div class="card-header">
            Funcionario ${i + 1}
            <button class="btn btn-sm btn-danger float-end" onclick="removerFuncionario(${i})">X</button>
          </div>
          <div class="card-body">
            <select id="select-func-${i}" class="form-select mb-3" onchange="seleccionarFuncionario(${i}, this.value)">
              <option value="">Seleccionar Funcionario</option>
              ${listaFuncionarios.map(f => `<option value="${f.cedula}">${f.nombre} (${f.area})</option>`).join('')}
            </select>
            <div id="aplicativos-${i}"></div>
          </div>
        `;
                container.appendChild(div);
                $(`#select-func-${i}`).select2();
                // Restaurar selección si había
                if (funcionariosAgregados[i].cedula) {
                    document.querySelector(`#select-func-${i}`).value = funcionariosAgregados[i].cedula;
                    seleccionarFuncionario(i, funcionariosAgregados[i].cedula);
                }
            });
        }

        function generarSolicitud() {
            // Validación previa
            if (!Array.isArray(funcionariosAgregados) || funcionariosAgregados.length === 0) {
                alert('Debe agregar al menos un funcionario.');
                return;
            }

            // Enviar datos al servidor con manejo robusto de respuesta
            fetch('guardar_solicitud.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        funcionarios: funcionariosAgregados
                    })
                })
                .then(async (response) => {
                    const contentType = response.headers.get('Content-Type') || '';
                    const raw = await response.text();
                    if (!contentType.includes('application/json')) {
                        // El backend devolvió HTML/texto (probablemente un error PHP)
                        throw new Error(raw.substring(0, 500));
                    }
                    let data;
                    try {
                        data = JSON.parse(raw);
                    } catch (e) {
                        throw new Error('Respuesta no es JSON válido: ' + raw.substring(0, 500));
                    }
                    if (!response.ok || data.success === false) {
                        throw new Error(data.message || 'Error en la solicitud');
                    }
                    return data;
                })
                .then((data) => {
                    alert('Solicitud generada exitosamente. ID: ' + data.id);
                    window.location.href = 'menuprincipal.php';
                })
                .catch((error) => {
                    console.error('Error detallado:', error);
                    alert('Error al generar la solicitud: ' + error.message);
                });
        }
    </script>
</body>

</html>