<?php

function  cuerposite () {
	$conexiones = new conexiones();
	$this->conexionUsuarios = $conexiones->conexionUsuarios();
	$this->funciones = new funciones();
 }
 
function vistaColumnaUnica($valor) {
	$html = '<div class="cuerpoPrincipal">';
	$html = $html.'<div class="ContPanelA001">';
	$html = $html.'<div class="PanelTransparenteTransition">';
	$html = $html.'<div id="cuerpo" class="cuerpo">';
	$html = $html.$valor;	
	$html = $html.' </div>';
	$html = $html.' </div>';
	$html = $html.' </div>';
	$html = $html.' </div>';
	return  $html;
}

function CuerpoPage($valor) {
	$s .= '<div class="emp_cuerpoB">';
	$s .= '<div class="empresaB"  id="cuerpo">';
	$s .=  $valor;	
	$s .= ' </div>';
	$s .= ' </div>';
	return  $s;
}

function CuerpoAdmin($Arg) {
	$s .= '<div class="emp_cuerpoC">';
	$s .= '<div class="empresaC"  id="cuerpo">';
	$s .= $Arg;
	$s .= ' </div>';
	$s .= ' </div>';
	return  $s;
}


function vistaColumnaDoble($valor) {
	// $vSQL = " SELECT FechReg FROM agenda ";
	// echo $this->funciones->leeRegistro($this->conexionUsuarios,$vSQL,0);	
	$html = '<div id="cuerpo"  class="cuerpo_A001">';
	$html = $html.$valor;	
	$html = $html.' </div>';
	return  $html;
}	 

function functionAccion($valor) {
	// $vSQL = " SELECT FechReg FROM agenda ";
	 // echo $this->funciones->leeRegistro($this->conexionUsuarios,$vSQL,0);	
	$html = '<div id="cuerpo"  class="cuerpo_A001">';
	$html = $html.$valor;	
	$html = $html.' </div>';
	return  $html;
}