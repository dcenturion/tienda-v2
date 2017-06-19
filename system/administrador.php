<?php
session_start();
require_once('_librerias/disenoVisual/layout.php');
error_reporting(E_ERROR);

$UsuarioCod =$_SESSION['Usuario']['string'];
$cnPDO = PDOConnection();
$enlace = "./_vistas/adminTablasForms.php";

if ($UsuarioCod != '') {
	    if (get("var") != '') {
			rd(get("var")."&solicitud=interna");
	    }
		$s = documentoHtmlApp("");
		W($s);
		
} else {

    rd("./admin.php");
    WE("");
}
?>

<link href="./_estilos/calendario.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="_librerias/js/calendar.js"></script>
<script type="text/javascript" src="_librerias/js/calendar-es.js"></script>
<script type="text/javascript" src="_librerias/js/calendar-setup.js"></script>
<script type="text/javascript" src="_librerias/js/slider.js"></script>
<script type=text/javascript>
    //$("#cuerpo").html("");
    //controlaActivacionPaneles("<?php echo $sUrlPanelesA; ?>",true);
</script>     

<style type="text/css">
    .PanelA{ width:100%;}
    .PanelB{width:100%;}
</style>
