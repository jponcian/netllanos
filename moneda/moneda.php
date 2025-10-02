<?php
$conn = new PDO('mysql:host=localhost; dbname=losllanos', 'root', '') or die(mysql_error());

?>
<html>

<head>
	<title>Moneda EUROS</title>
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="screen">
	<link rel="stylesheet" type="text/css" href="css/DT_bootstrap.css">
</head>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/bootstrap.js" type="text/javascript"></script>


<script type="text/javascript" charset="utf-8" language="javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf-8" language="javascript" src="js/DT_bootstrap.js"></script>

<style>
</style>

<body style="background: transparent !important;">
	<div class="row-fluid">
		<div class="span12">
			<div class="container">
				<br />
				<h1>
					<p>.</p>
				</h1>
				<h1>
					<p>.</p>
				</h1>
				<br />
				<br />

				<br />
				<br />
				<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example">
					<thead>
						<tr>
							<th width="20%" align="center">FECHA</th>
							<th width="20%" align="center">DESCRIPCION</th>
							<th width="20%" align="center">UNIDAD</th>
							<th width="20%" align="center">VALOR</th>
							<th width="20%" align="center">USUARIO</th>
						</tr>

					</thead>
					<?php
					$query = $conn->query("select * from a_moneda_cambio order by FechaAplicacion desc ");
					while ($row = $query->fetch()) {
						$fecha = $row['FechaAplicacion'];
						$desc = $row['descripcion'];
						$val = $row['valor'];
						$uni = 1;
						$usuario = $row['usuario'];
					?>
						<tr>

							<td>
								&nbsp;<?php echo $fecha; ?>
							</td>
							<td>
								&nbsp;<?php echo $desc; ?>
							</td>
							<td>
								&nbsp;<?php echo $uni; ?>
							<td>
								&nbsp;<?php echo $val; ?> .Bs
							</td>
							<td>
								&nbsp;<?php echo $usuario; ?>
							</td>
							</td>

						</tr>

					<?php } ?>
				</table>
			</div>
		</div>
	</div>
</body>

</html>