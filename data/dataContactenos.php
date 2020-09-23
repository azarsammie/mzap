<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));


require_once ('conexion.php');

class Contactenos 
{
		var  $id;
		var  $nombre;
        var  $email;
        var  $mensaje;
		
		function __construct()
		{
			$this->conexion = new Conexion();
		}
		
    
        public function InsertaNuevoMensaje()
		{
			$consulta = "INSERT INTO mzapcr_contactenos (nombre, email, mensaje) VALUES ('".utf8_decode($this->nombre)."','".utf8_decode($this->email)."','".utf8_decode($this->mensaje)."')";
			$result = $this->conexion->consulta($consulta);

            return $result;
		}

		
		function cerrarC()
		{ 
			$this->conexion->cerrar();
		}

}

?>