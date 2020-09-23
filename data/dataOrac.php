<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));


require_once ('conexion.php');

class Orac
{
        var  $id;
        var  $tipo_alimento;
        var  $alimentos;
        var  $bendicion_previa;
        var  $bendicion_posterior;
        var $conexion;

        function __construct()
        {
            $this->conexion = new Conexion();
        }

        public function ObtenerListadoTipoAlimento()
        {
            $consulta = "SELECT id, tipo_alimento FROM mzapcr_oraciones where id<>3";
            $result = $this->conexion->consulta($consulta);
            $list_array = Array();
            while ($row = mysqli_fetch_array($result))
            {
                $list_array[] = $row;
            }
            return $list_array;
        }

        public function ObtenerOracion()
        {
            $consulta = "SELECT tipo_alimento,alimentos,bendicion_previa,bendicion_posterior FROM mzapcr_oraciones where id = ".$this->id."";
            $result = $this->conexion->consulta($consulta);
            while ($row = mysqli_fetch_array($result))
            {
                $this->tipo_alimento = $row["tipo_alimento"];
                $this->alimentos = $row["alimentos"];
                $this->bendicion_previa = $row["bendicion_previa"];
                $this->bendicion_posterior = $row["bendicion_posterior"];
            } 
        }
}
?>