<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));


require_once ('conexion.php');

class Votaciones 
{
		var  $id;
		var  $id_producto;
		var  $id_usuario;
        var  $eventoValido;
        var  $eventoNoValido;
        var  $cantidad;
        var  $total;
        var  $totalEstrellas;
		
		function __construct()
		{
			$this->conexion = new Conexion();
		}
		
    	
		public function AgregaVoto()
		{
			$consulta = "INSERT INTO mzapcr_votacion (id_producto, id_usuario,valido,novalido,fecha_registro) VALUES(".$this->id_producto.",".$this->id_usuario.",".$this->eventoValido.",".$this->eventoNoValido.",NOW())";
			$result = $this->conexion->consulta($consulta);
            
            return $result;
		}
    
        public function ActualizaVoto()
		{
			$consulta = "UPDATE mzapcr_votacion SET valido = ".$this->eventoValido.", novalido = ".$this->eventoNoValido.", fecha_registro = NOW() WHERE id_producto = ".$this->id_producto." AND id_usuario = ".$this->id_usuario."";
			$result = $this->conexion->consulta($consulta);
            
            return $result;
		}
    
        public function ObtenerVotacionUsuarioProducto()
		{
			$consulta = "SELECT COUNT(id) AS cantidad FROM mzapcr_votacion WHERE id_producto = ".$this->id_producto." AND id_usuario = ".$this->id_usuario."";
			$result = $this->conexion->consulta($consulta);
			$list_array = Array();
            while ($row = mysqli_fetch_array($result))
			{
				$this->cantidad = $row["cantidad"];
            }
		}

		
		function cerrar()
		{ 
			$this->conexion->cerrar();
		}

}

?>