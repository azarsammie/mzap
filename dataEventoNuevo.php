<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));


require_once('conexion.php');

class EventoNuevo
{
    var $id;
    var $id_categoria;
    var $nombre;
    var $imagen;
    var $descripcion;
    var $miIP;
    var $latitude;
    var $longitude;
    var $pais;
    var $id_usuario;
    var $nombre_usuario;
    var $foto_usuario;
    var $nombre_categoria;
    var $cantidadVotos;

    function __construct()
    {
        $this->conexion = new Conexion();
    }


    public function ObtenerListadoEventoNuevos()
    {
        $consulta = "SELECT mzapcr_eventos.id, nombre_producto, descripcion, imagen64, id_categ, categoria, nombre, foto, DATE(fecha_creacion) AS fechaP FROM mzapcr_eventos 
            INNER JOIN mzapcr_categorias ON mzapcr_eventos.id_categoria = mzapcr_categorias.id_categ
            INNER JOIN mzapcr_usuario ON mzapcr_eventos.id_usuario = mzapcr_usuario.id
            WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 15 DAY) AND mzapcr_eventos.iso = '" . $this->pais . "' AND mzapcr_eventos.estado = 1";
        //echo $consulta;
        $result = $this->conexion->consulta($consulta);
        $list_array = Array();
        while ($row = mysqli_fetch_array($result)) {
            $list_array[] = $row;
        }
        return $list_array;
    }

    public function ObtenerEstrellasEvento()
    {
        $consulta = "SELECT IFNULL((5*(SUM(valido)/COUNT(valido))),0) AS CantidadEstrellas, IFNULL(ROUND(100*(SUM(valido)/COUNT(valido))),0) AS Porcentaje, COUNT(valido) AS CantidadVotos FROM mzapcr_votacion WHERE id_producto = " . $this->id . "";
        $result = $this->conexion->consulta($consulta);
        while ($row = mysqli_fetch_array($result)) {
            $this->totalEstrellas = $row["CantidadEstrellas"];
            $this->ProcentajeEstrellas = $row["Porcentaje"];
            $this->cantidadVotos = $row["CantidadVotos"];
        }
    }


    function cerrarP()
    {
        $this->conexion->cerrar();
    }

}

?>