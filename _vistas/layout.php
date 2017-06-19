<?php
class Layout{

	public function main($conten,$data) {

		$this->view = $conten;
		return $this->render('./_vistas/home.phtml',$data);
	}
    
	public function mainB($conten,$id,$site) {
	
		$this->_pageTitle = "FlexZinn";
		$this->view = $conten;
		$this->id = $id;
		$this->site = $site;
		return $this->render('./_vistas/homeb.phtml');

	}
	
	public function loginAdmin($conten) {
	
		$this->_pageTitle = "FlexZinn";
		$this->view = $conten;
		return $this->render('./_vistas/homec.phtml');

	}

	public function dashboard($conten) {
	
		$this->_pageTitle = "FlexZinn";
		$this->view = $conten;
		return $this->render('./_vistas/homed.phtml');

	}	
	
	
		
	public function render($filename, $viewDataArray = '') {
		ob_start();
		if (is_array($viewDataArray)) {
			extract($viewDataArray, EXTR_OVERWRITE);
		}
		include_once $filename;
		$contenido = ob_get_contents();
		ob_get_clean();
		return $contenido;
	}
	
	public function _pageTitle($name) {

		return $name;
	}
	// public function segmento($filename,$viewDataArray) {

		// return $this->render($filename, $viewDataArray = '');
	// }	
	
	public function view($arg) {

		return $arg;
	}	
	public function formContactos($arg) {

		return $arg;
	}	
	
}