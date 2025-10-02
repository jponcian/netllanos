<?php
if ($_SESSION['MOSTRAR']=='SI')
{
?>
<div class="modal fade" id="modal_mensaje" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title"><?php echo $_SESSION['MENSAJE']; ?></h3>
			  <?php if ($_SESSION['BOTON']<>'') { ?>
			  <h3 class="modal-title"><?php echo $_SESSION['BOTON']; ?></h3>
			  <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div><?php
$_SESSION['MOSTRAR']='NO';
$_SESSION['BOTON']='';
echo "<script>$(function() { $('#modal_mensaje').modal('show'); });</script>";
}
?>