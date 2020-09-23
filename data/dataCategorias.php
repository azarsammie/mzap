<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));


require_once ('conexion.php');

class Categorias 
{
		var  $id;
		var  $id_categoria;
		var  $nombre;
        var  $iso;
        var  $cantidad;
		
		function __construct()
		{
			$this->conexion = new Conexion();
		}
		
    	
		public function ObtenerListadoCategorias()
		{
			$consulta = "SELECT mzapcr_categorias.id, 
                            mzapcr_categorias.id_categ, 
                            mzapcr_categorias.categoria, 
                            COUNT(mzapcr_eventos.id) AS cantidad
                        FROM mzapcr_eventos 
                        RIGHT JOIN mzapcr_categorias ON mzapcr_eventos.id_categoria = mzapcr_categorias.id_categ
                        WHERE mzapcr_categorias.estado = 1
                        GROUP BY mzapcr_categorias.id";
			$result = $this->conexion->consulta($consulta);
			$list_array = Array();
            while ($row = mysqli_fetch_array($result))
			{
				$list_array[] = $row;
            }
//            var_dump($list_array);
            return $list_array;
		}
    
        public function ObtenerCantidadxCategorias()
		{
			$consulta = "SELECT COUNT(mzapcr_eventos.id) AS cantidad
                        FROM mzapcr_categorias 
                        INNER JOIN mzapcr_eventos ON mzapcr_categorias.id_categ = mzapcr_eventos.id_categoria
                        WHERE id_categ = ".$this->id_categoria." AND iso = '".$this->iso."' AND mzapcr_eventos.estado = 1";
            //echo $consulta;
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