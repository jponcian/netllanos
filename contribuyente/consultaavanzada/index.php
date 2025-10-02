<?php
include("conexion.php");

$queryCondition = "";
if (!empty($_POST["search"])) {
	$advance_search_submit = $_POST["advance_search_submit"];
	foreach ($_POST["search"] as $k => $v) {
		if (!empty($v)) {

			$queryCases = array("with_any_one_of", "with_the_exact_of", "without", "starts_with");
			if (in_array($k, $queryCases)) {
				if (!empty($queryCondition)) {
					$queryCondition .= " AND ";
				} else {
					$queryCondition .= " WHERE ";
				}
			}
			switch ($k) {
				case "with_any_one_of":
					$with_any_one_of = $v;
					$wordsAry = explode(" ", $v);
					$wordsCount = count($wordsAry);
					for ($i = 0; $i < $wordsCount; $i++) {
						if (!empty($_POST["search"]["search_in"])) {
							$queryCondition .= $_POST["search"]["search_in"] . " LIKE '%" . $wordsAry[$i] . "%'";
						} else {
							$queryCondition .= "rif LIKE '" . $wordsAry[$i] . "%' OR contribuyente LIKE '" . $wordsAry[$i] . "%'";
						}
						if ($i != $wordsCount - 1) {
							$queryCondition .= " OR ";
						}
					}
					break;
				case "with_the_exact_of":
					$with_the_exact_of = $v;
					if (!empty($_POST["search"]["search_in"])) {
						$queryCondition .= $_POST["search"]["search_in"] . " LIKE '%" . $v . "%'";
					} else {
						$queryCondition .= "rif LIKE '%" . $v . "%' OR contribuyente LIKE '%" . $v . "%'";
					}
					break;
				case "without":
					$without = $v;
					if (!empty($_POST["search"]["search_in"])) {
						$queryCondition .= $_POST["search"]["search_in"] . " NOT LIKE '%" . $v . "%'";
					} else {
						$queryCondition .= "rif NOT LIKE '%" . $v . "%' AND contribuyente NOT LIKE '%" . $v . "%'";
					}
					break;
				case "starts_with":
					$starts_with = $v;
					if (!empty($_POST["search"]["search_in"])) {
						$queryCondition .= $_POST["search"]["search_in"] . " LIKE '" . $v . "%'";
					} else {
						$queryCondition .= "rif LIKE '" . $v . "%' OR contribuyente LIKE '" . $v . "%'";
					}
					break;
				case "search_in":
					$search_in = $_POST["search"]["search_in"];
					break;
			}
		}
	}
}
$orderby = " ORDER BY rif desc";
$sql = "SELECT
dir_estados.descripcion,
contribuyentes.rif,
contribuyentes.Especial,
contribuyentes.fechaespecial,
contribuyentes.contribuyente,
contribuyentes.DescripcionDomicilio
FROM
contribuyentes
INNER JOIN dir_estados ON contribuyentes.id_estado = dir_estados.id_estado" . $queryCondition;
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html>

<head>
	<title>Busqueda avanzada de Contribuyentes</title>
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<meta charset="utf-8">
	<style>
		body {
			font-family: "Segoe UI", Optima, Helvetica, Arial, sans-serif;
			line-height: 25px;
		}

		.caja_busqueda {
			padding: 30px;
			background-color: #EAEAEA;
		}

		#busqueda_avanzada {
			color: #001FFF;
			cursor: pointer;
		}

		.resultado_descripcion {
			margin: 5px 0px 15px;
		}

		.verde {
			color: #84D2A7;
			font-size: 14px;
		}

		b {
			font-size: 12px;
		}

		.InputBox {
			padding: 5px;
			border: 0;
			border-radius: 4px;
			margin: 0px 5px 15px;
			width: 100%;
		}

		.btn {
			width: 100%;
			font-size: 14px;
		}
	</style>
	<script>
		function VerOcultarBusquedaAvanzada() {
			if (document.getElementById("advanced-search-box").style.display == "none") {
				document.getElementById("advanced-search-box").style.display = "block";
				document.getElementById("advance_search_submit").value = "1";
			} else {
				document.getElementById("advanced-search-box").style.display = "none";
				document.getElementById("with_the_exact_of").value = "";
				document.getElementById("without").value = "";
				document.getElementById("starts_with").value = "";
				document.getElementById("search_in").value = "";
				document.getElementById("advance_search_submit").value = "";
			}
		}
	</script>
</head>

<body style="background: transparent !important;">
	<nav class="navbar navbar-default">
		<div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->


			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li><a href="../../index.php">VOLVER<span class="sr-only">(current)</span></a></li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container-fluid -->
	</nav>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h2>::Contribuyentes::</h2>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<form name="frmSearch" method="post" action="index.php">
					<input type="hidden" id="advance_search_submit" name="advance_search_submit" value="<?php echo $advance_search_submit; ?>">
					<div class="caja_busqueda">
						<label class="search-label">Consulta por Rif o Razon Social</label>
						<div>
							<input type="text" name="search[with_any_one_of]" class="form-control" value="<?php echo $with_any_one_of; ?>" />
						</div>


						<div style="margin-top:10px;">
							<input type="submit" name="busqueda" class="btn btn-success" value="Buscar">
						</div>
					</div>
				</form>
				<?php
				if (isset($_POST["busqueda"])) {

					$number = 0;
					while ($row = mysqli_fetch_assoc($result)) {
						$number++; ?>
						<div>
							<div><strong><?php echo $number; ?>. <?php echo $row["rif"]; ?> ( Especial = <?php echo $row["Especial"]; ?> Fecha = <?php echo $row["fechaespecial"]; ?>)</strong></div>
							<div class="verde"><strong><?php echo $row["contribuyente"]; ?></strong></div>
							<div class="resultado_descripcion"><?php echo $row["DescripcionDomicilio"]; ?></div>
							<div class="verde"><strong><?php echo $row["descripcion"]; ?></strong></div>

						</div>
					<?php } ?>
					<?php
					$total = mysqli_num_rows($result);
					if ($total == 0) {
						echo 'No hay resultados encontrados';
					} else {
						echo '<hr><b>Hay un total de ' . $total . ' resultados en su busqueda</b>';
					}
					?>
				<?php } else {
					echo "<div><strong>Ingrese la palabra clave a buscar.</strong></div>";
				} ?>


			</div>
		</div>
	</div>
	<center>
		<div class="panel-footer">
			<div class="container">
				<p>GRTI LOS LLANOS / <a target="_blank">Informatica</a></p>
			</div>
		</div>
	</center>
</body>

</html>