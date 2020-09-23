<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

require_once('conexion.php');

class Encuesta
{
	var $conexion;

	function __construct() {
		$this->conexion = new Conexion();
	}

	public function GuardarEncuesta($consulta) {
		$result = $this->conexion->consulta($consulta);
	}

}

?>