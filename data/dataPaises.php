<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

require_once ('conexion.php');

class Pais 
{
        public  $id;
        public  $nombre;
        public  $bandera;
        public  $iso;
        public  $conexion;
        
        
        function __construct()
        {
            $this->conexion = new Conexion();
        }

        public function ObtienePaises()
        {
            $sql = "SELECT
            mzapcr_paises.id,
            mzapcr_paises.iso,
            mzapcr_paises.nombre,
            mzapcr_paises.prefijo,
            mzapcr_paises.bandera
            FROM
            mzapcr_paises";
            $result = $this->conexion->consulta($sql);
            $list_array = Array();
            while ($row = mysqli_fetch_array($result))
            {
                $list_array[] = $row;
            }
            return $list_array;
        }
    
        public function ObtenerPaisActual()
		{
			$consulta = "SELECT id, nombre, bandera FROM mzapcr_paises WHERE iso = '".$this->iso."'";
			$result = $this->conexion->consulta($consulta);
            while ($row = mysqli_fetch_array($result))
			{
				$this->id = $row["id"];
                $this->nombre = $row["nombre"];
                $this->bandera = $row["bandera"];
            }
		}
    
        public function ObtenerListadoPaises()
		{
			$consulta = "SELECT id, iso, nombre, bandera FROM mzapcr_paises";
			$result = $this->conexion->consulta($consulta);
			$list_array = Array();
            while ($row = mysqli_fetch_array($result))
			{
				$list_array[] = $row;
            }
            return $list_array;
		}
        
}

?>