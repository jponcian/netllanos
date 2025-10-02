<option value="0" >--> Seleccione <--</option>
<?php
session_start();
include "../conexion.php";
//--------------
$sede = $_GET[ 'sede' ];
$anno = $_GET[ 'anno' ];
//--------------
if ( $_POST[ 'tipo' ] == 1 ) {
  $consulta_x = 'SELECT anno FROM vista_exp_cobro WHERE status>=6 and sector=0' . $sede . ' GROUP BY anno ORDER BY anno DESC;';
  $tabla_x = mysql_query( $consulta_x );
  while ( $registro_x = mysql_fetch_array( $tabla_x ) ) {
    echo '<option value=' . $registro_x[ 'anno' ] . '>' . ( $registro_x[ 'anno' ] ) . '</option>';
  }
}
//--------------
if ( $_POST[ 'tipo' ] == 2 ) {
  $consulta_x = 'SELECT numero FROM vista_exp_cobro WHERE status>=6 and anno=' . $anno . ' AND sector=0' . $sede . ' ORDER BY numero DESC;';
  $tabla_x = mysql_query( $consulta_x );
  while ( $registro_x = mysql_fetch_array( $tabla_x ) ) {
    echo '<option value=' . $registro_x[ 'numero' ] . '>' . ( $registro_x[ 'numero' ] ) . '</option>';
  }
}
//-------------- 
?>
