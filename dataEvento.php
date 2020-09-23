<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));


require_once('conexion.php');

class Evento
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
    var $foto_usuario;
    var $nombre_categoria;
    var $nombre_usuario;
    var $views;
    var $totalEstrellas;
    var $ProcentajeEstrellas;
    var $cantidadVotosUsuario;
    var $esValido;
    var $esNoValido;
    var $cantidadVotos;
    var $fecha_creacion;

    function __construct()
    {
        $this->conexion = new Conexion();
    }


    public function ObtenerListadoEvento()
    {
        $consulta = "SELECT mzapcr_eventos.id, nombre_producto, descripcion, imagen64, categoria, nombre, foto, DATE(fecha_creacion) AS fechaP FROM mzapcr_eventos 
            INNER JOIN mzapcr_categorias ON mzapcr_eventos.id_categoria = mzapcr_categorias.id_categ
            INNER JOIN mzapcr_usuario ON mzapcr_eventos.id_usuario = mzapcr_usuario.id
            WHERE id_categoria=" . $this->id_categoria . " AND mzapcr_eventos.iso = '" . $this->pais . "' AND mzapcr_eventos.estado = 1 ORDER BY RAND()";
        //echo $consulta;
        $result = $this->conexion->consulta($consulta);
        $list_array = Array();
        while ($row = mysqli_fetch_array($result)) {
            $list_array[] = $row;
        }
        return $list_array;
    }

    public function ObtenerListadoBusquedaEvento()
    {
        $consulta = "SELECT mzapcr_eventos.id, nombre_producto, descripcion, imagen64, categoria, nombre, foto, fecha_creacion FROM mzapcr_eventos 
            INNER JOIN mzapcr_categorias ON mzapcr_eventos.id_categoria = mzapcr_categorias.id_categ
            INNER JOIN mzapcr_usuario ON mzapcr_eventos.id_usuario = mzapcr_usuario.id
            WHERE id_categoria=" . $this->id_categoria . " AND mzapcr_eventos.iso = '" . $this->pais . "' AND nombre_producto like '%" . $this->nombre . "%' AND mzapcr_eventos.estado = 1";
        echo $consulta;
        $result = $this->conexion->consulta($consulta);
        $list_array = Array();
        while ($row = mysqli_fetch_array($result)) {
            $list_array[] = $row;
        }
        return $list_array;
    }

    public function InsertaNuevoEvento()
    {
        $consulta = "INSERT INTO mzapcr_eventos (id_categoria, nombre_producto, imagen64, descripcion, ip, latitude, longitude, iso , id_usuario, fecha_creacion) VALUES (" . $this->id_categoria . ",'" . utf8_decode($this->nombre) . "','" . $this->imagen . "','" . utf8_decode($this->descripcion) . "','" . utf8_decode($this->miIP) . "','" . utf8_decode($this->latitude) . "','" . utf8_decode($this->longitude) . "','" . utf8_decode($this->pais) . "'," . $this->id_usuario . ",NOW())";
        $result = $this->conexion->consulta($consulta);
//echo $this->imagen;
        return $result;
    }

    public function ObtenerCategoriaxEvento()
    {
        $consulta = "SELECT categoria FROM mzapcr_categorias WHERE id_categ=" . $this->id_categoria;
        $result = $this->conexion->consulta($consulta);
        while ($row = mysqli_fetch_array($result)) {
            $categoria = $row["categoria"];
        }
        return $categoria;
    }

    public function ObtenerDetalleEvento()
    {
        $consulta = "SELECT mzapcr_eventos.id, nombre_producto, descripcion, imagen64, categoria, nombre, foto, views, DATE(fecha_creacion) AS fechaP FROM mzapcr_eventos 
            INNER JOIN mzapcr_categorias ON mzapcr_eventos.id_categoria = mzapcr_categorias.id_categ
            INNER JOIN mzapcr_usuario ON mzapcr_eventos.id_usuario = mzapcr_usuario.id
            WHERE mzapcr_eventos.id=" . $this->id;
        //echo $consulta;
        $result = $this->conexion->consulta($consulta);
        while ($row = mysqli_fetch_array($result)) {
            $this->nombre = $row["nombre_producto"];
            $this->imagen = $row["imagen64"];
            $this->descripcion = $row["descripcion"];
            $this->id = $row["id"];
            $this->views = $row["views"];
            $this->nombre_usuario = $row["nombre"];
            $this->foto_usuario = $row["foto"];
            $this->fecha_creacion = $row["fechaP"];
        }
    }

    public function ObtenerDetalleUsuarioEvento()
    {
        $consulta = "SELECT
            mzapcr_usuario.nombre,
            mzapcr_usuario.foto
            FROM
            mzapcr_usuario
            INNER JOIN mzapcr_eventos ON mzapcr_usuario.id = mzapcr_eventos.id_usuario
            WHERE
            mzapcr_eventos.id =" . $this->id . "";
        //echo $consulta;
        $result = $this->conexion->consulta($consulta);
        while ($row = mysqli_fetch_array($result)) {
            $this->nombre_usuario = $row["nombre"];
            $this->foto_usuario = $row["foto"];
        }
    }

    public function SumaView()
    {
        $consulta = "UPDATE mzapcr_eventos SET views = (views+1) WHERE id = " . $this->id . " AND estado = 1 ";
        $result = $this->conexion->consulta($consulta);

        return $result;
    }

    public function MuestraView()
    {
        $consulta = "SELECT views FROM mzapcr_eventos WHERE id = " . $this->id . "";
        $result = $this->conexion->consulta($consulta);
        while ($row = mysqli_fetch_array($result)) {
            $this->views = $row["views"];
        }
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

    public function ObtenerVotoUsuario()
    {
        $consulta = "SELECT COUNT(id) AS cantidad, IFNULL(valido,0) AS isvalido, IFNULL(novalido,0) AS isnovalido
            FROM mzapcr_votacion   
            WHERE id_producto = " . $this->id . " AND id_usuario = " . $this->id_usuario . " ";
        $result = $this->conexion->consulta($consulta);
        while ($row = mysqli_fetch_array($result)) {
            $this->cantidadVotosUsuario = $row["cantidad"];
            $this->esValido = $row["isvalido"];
            $this->esNoValido = $row["isnovalido"];
        }
    }

    public function ObtenerListadoEventoSinVoto()
    {
        $consulta = "SELECT mzapcr_eventos.id, nombre_producto, descripcion, imagen64, id_categoria, nombre, foto, DATE(fecha_creacion) AS fechaP FROM mzapcr_eventos 
            INNER JOIN mzapcr_usuario ON mzapcr_eventos.id_usuario = mzapcr_usuario.id
            WHERE mzapcr_eventos.iso = '" . $this->pais . "' AND mzapcr_eventos.estado = 1 AND mzapcr_eventos.id NOT IN (SELECT id_producto FROM mzapcr_votacion)
            ORDER BY RAND()
            LIMIT 20";
        //echo $consulta;
        $result = $this->conexion->consulta($consulta);
        $list_array = Array();
        while ($row = mysqli_fetch_array($result)) {
            $list_array[] = $row;
        }
        return $list_array;
    }

    public function map_markers()
    {
        $consulta = "SELECT nombre_producto name, concat('evento_detalle.html?id_prod=',id,'&id_categ=',id_categoria) url, 
                longitude lng, latitude lat 
            FROM mzapcr_eventos
            WHERE LENGTH(latitude) > 0 AND LENGTH(longitude) > 0";

        $consulta = "SELECT ev.nombre_producto name, concat('evento_detalle.html?id_prod=',ev.id,'&id_categ=',ev.id_categoria) url, 
                ev.longitude lng, ev.latitude lat, ct.marker cat 
            FROM mzapcr_eventos ev
			LEFT JOIN mzapcr_categorias ct ON ct.id_categ = ev.id_categoria
            WHERE LENGTH(ev.latitude) > 0 AND LENGTH(ev.longitude) > 0 AND ev.estado = 1";


        if ($result = $this->conexion->consulta($consulta)) {
        	$myArray = Array();
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $myArray[] = $row;
            }
            return json_encode($myArray, JSON_PRETTY_PRINT);
        }

    }

    function cerrarP()
    {
        $this->conexion->cerrar();
    }

}

?>