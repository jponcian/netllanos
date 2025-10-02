<?php
$errorusuario = trim ($_GET['errorusuario']);		 
switch ($errorusuario) 
	{
	case "cc":
		echo "<strong> Debe cambiar la Contraseña de Acceso al Sistema!!! </strong>"; 
		break;	
	case "cv":
		echo "<strong> Por Favor llene todos los Campos!!! </strong>"; 
		break;	
	case "si":
		echo "<strong> Registros Actualizados Exitosamente!!! </strong>"; 
		break;	
	case "no":
		echo "<strong> No Existen Registros para Actualizar!!! </strong>";
		break;
	case "nt":
		echo "<strong> No Existen Registros para Transferir!!! </strong>";
		break;	
	case "tsa":
		echo "<strong> Todas las Sanciones estan Aprobadas!!! </strong>";
		break;	
	case "sist":
		echo "<strong> Usuario o Contraseña Inválida </strong>";
		break;
	case "sis":
		echo "<strong> Usted No Posee Acceso al Sistema </strong>";
		break;
	case "val":
		echo "<strong> Usted No se ha Validado en el Sistema </strong>"; 
		break;	
	case "rep":
		echo "<strong> Ya fué registrada esta Planilla en otro Contribuyente!!! </strong>"; 
		break;	
	case "nsa":
		echo "<strong> No existen Sanciones por Aprobar!!! </strong>";
		break;	
	case "ner":
		echo "<strong> No existen Resoluciones por Generar!!! </strong>";
		break;		
	case "np":
		echo "<strong> Existe Notificacion Pendiente!!! </strong>";
		break;	
	case "yp":
		echo "<strong> Ya se ha procesado esta actualizacion!!! </strong>";
		break;	
	case "cd":
		echo "<strong> Verifique que sea la misma Contraseña en ambos Campos!!! </strong>";
		break;		
	case "nep":
		echo "<strong> No Existe la Providencia!!! </strong>";
		break;	
	case "cpi":
		echo "<strong> Contribuyente por Asignar!!! </strong>";
		break;
	case "fna":
		echo "<strong> Supervisor o Fiscal no Autorizado!!! </strong>";
		break;
	case "sna":
		echo "<strong> Supervisor no Autorizado!!! </strong>";
		break;	
	case "e91":
		echo "<strong> No deben existir Sanciones aprobadas o transferidas!!! </strong>";
		break;	
	case "tsa":
		echo "<strong> Todas las Sanciones estan Aprobadas!!! </strong>";
		break;		
	case "ppn":
		echo "<strong> La Providencia no ha sido Notificada por el Fiscal!!! </strong>";
		break;
	case "ppa":
		echo "<strong> La Providencia no ha sido Asignada por el Supervisor!!! </strong>";
		break;			
	case "ppi":
		echo "<strong> La Providencia no ha sido Impresa!!! </strong>";
		break;	
	case "pt":
		echo "<strong> La Providencia está Concluida!!! </strong>";
		break;	
	case "pa":
		echo "<strong> La Providencia está Anulada!!! </strong>";
		break;				
	case "pcs":
		echo "<strong> Providencia Concluida Exitosamente!!! </strong>";
		break;		
	case "er":
		echo "<strong> Expediente Rechazado Exitosamente!!! </strong>";
		break;	
	case "nert":
		echo "<strong> No Existen Expedientes Transferidos!!! </strong>";
		break;																												
	case "ners":
		echo "<strong> No Existen Expedientes Sancionados!!! </strong>";
		break;																												
	case "pnpf":
		echo "<strong> La Providencia está por Concluir por el Fiscal!!! </strong>";
		break;	
	case "pae":
		echo "<strong> Providencia Aprobada Exitosamente!!! </strong>";
		break;	
	case "ppc":
		echo "<strong> La Providencia no ha sido Concluida por el Fiscal!!! </strong>";
		break;			
	case "pnas":
		echo "<strong> La Providencia no ha sido Aprobada por el Supervisor!!! </strong>";
		break;			
	case "paex":
		echo "<strong> Providencia Actualizada Exitosamente!!! </strong>";
		break;		
	case "pre":
		echo "<strong> Providencia devuelta a Estatus 91 Exitosamente!!! </strong>";
		break;	
	case "pres":
		echo "<strong> Providencia devuelta al Supervisor!!! </strong>";
		break;						
	case "papc":
		echo "<strong> Providencia Aprobada por el Coordinador!!! </strong>";
		break;	
	case "paps":
		echo "<strong> Providencia Aprobada por el Supervisor!!! </strong>";
		break;	
	case "ece":
		echo "<strong> Expediente Cargado Exitosamente!!! </strong>";
		break;					
	case "neexp":
		echo "<strong> No Existe el Expediente!!! </strong>";
		break;					
	case "una":
		echo "<strong> Usuario no Autorizado!!! </strong>";
		break;					
	case "ea":
		echo "<strong> Expediente Anulado!!! </strong>";
		break;	
	case "expa":
		echo "<strong> Expediente Aprobado!!! </strong>";
		break;
	case "na":
		echo "<strong> No está autorizado a modificar Contribuyentes Naturales!!! </strong>";
		break;	
	case "sect":
		echo "<strong> No está autorizado a trabajar en esta Sede!!! </strong>";
		break;				
	case "rexi":
		echo "<strong> La Contraseña fue enviada a su correo exitosamente!!! </strong>";
		break;				
	}
?>
