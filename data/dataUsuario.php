<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

require_once('conexion.php');

class Usuario
{
	public $id;
	public $prefijo;
	public $telefono;
	public $nombre;
	public $correo;
	public $foto;
	public $sexo;
	public $fecha_nacimiento;
	public $codigo;
	public $estado;
	public $iso;
	public $conexion;

	function __construct() {
		$this->conexion = new Conexion();
	}

	public function GeneraCodigo() {
		$this->codigo = $this->unique_randoms(0, 9, 6);
	}

	public function CreaUsuario() {
		//$val = $this->exist();
		//echo "Devuelve exist: ".$val."-";
		if ($this->exist() == false) {
			$sql = "INSERT INTO mzapcr_usuario (prefijo,telefono,codigo,estado,correo) 
                values ('$this->prefijo','$this->telefono','$this->codigo','$this->estado','$this->correo')";
		} else {
			$sql = "UPDATE mzapcr_usuario SET codigo='$this->codigo' , estado='$this->estado', correo='$this->correo' 
                WHERE prefijo='$this->prefijo' AND telefono='$this->telefono'";
		}

		//echo $sql;
		$result = $this->conexion->consulta($sql);
		if ($result == 0) {
			return 0;
		}
		return 1;
	}

	public function exist() {
		$query = "SELECT
            mzapcr_usuario.id
            FROM
            mzapcr_usuario
            WHERE
            mzapcr_usuario.prefijo = '" . $this->prefijo . "' AND
            mzapcr_usuario.telefono = '" . $this->telefono . "'";
		//echo  $query;
		$result = $this->conexion->consulta($query);
		return (mysqli_num_rows($result)) ? true : false;
	}

	public function SeleccionaIdUsuario() {
		$sql = "SELECT id from mzapcr_usuario WHERE prefijo='$this->prefijo' and telefono='$this->telefono'";
		//echo $sql;
		$result = $this->conexion->consulta($sql);
		while ($row = mysqli_fetch_array($result)) {
			$this->id = $row['id'];
		}
	}

	public function ObtieneIdUser() {
		$this->id = 0;
		$sql = "SELECT id, nombre, foto, iso FROM mzapcr_usuario WHERE telefono='$this->telefono' AND codigo='$this->codigo'";
		$result = $this->conexion->consulta($sql);

		if ($result = $this->conexion->consulta($sql)) {
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$myArray[] = $row;
			}
			return $myArray;
		}
	}

	public function VerificaUsuario() {
		if ($this->VerificaCodigo()) {
			$sql = "UPDATE mzapcr_usuario set estado='ACTIVO' , iso = '" . $this->iso . "' WHERE prefijo='" . $this->prefijo . "' AND telefono='" . $this->telefono . "'";
			//echo $sql;
			$result = $this->conexion->consulta($sql);
			if ($result == 0) {
				return 0;
			}
			return 1;
		} else {
			return 0;
		}

	}

	public function VerificaCodigo() {
		$query = "SELECT
            mzapcr_usuario.id
            FROM
            mzapcr_usuario
            WHERE
            mzapcr_usuario.prefijo = '" . $this->prefijo . "' AND
            mzapcr_usuario.telefono = '" . $this->telefono . "' AND
            mzapcr_usuario.codigo = '" . $this->codigo . "'";
		$result = $this->conexion->consulta($query);
		return (mysqli_num_rows($result)) ? true : false;
	}

	public function GuardaInfoUsuario() {
		$sql = "UPDATE mzapcr_usuario set nombre='" . $this->nombre . "' , correo = '" . $this->correo . "' , foto = '" . $this->foto . "' , sexo = '" . $this->sexo . "' , fecha_nacimiento = '" . $this->fecha_nacimiento . "'   
            WHERE prefijo='" . $this->prefijo . "' AND telefono='" . $this->telefono . "'";
//            echo $sql;
		$result = $this->conexion->consulta($sql);
		if ($result == 0) {
			return 0;
		}
		return 1;
	}

	public function ObtieneEvento() {
		$sql = "SELECT
            mzapcr_eventos.id,
            mzapcr_eventos.nombre_producto,
            mzapcr_eventos.imagen64,
            mzapcr_eventos.descripcion,
            mzapcr_eventos.estado
            FROM
            mzapcr_eventos
            WHERE
            mzapcr_eventos.id_usuario = $this->id ";
		//echo $sql;
		$result = $this->conexion->consulta($sql);
		$html = "";
		while ($row = mysqli_fetch_array($result)) {
			$nombre_producto = $row['nombre_producto'];
			$imagen_producto = $row['imagen64'];
			$descripcion_producto = $row['descripcion'];
			$estado_producto = $row['estado'];
			switch ($estado_producto) {
				case "0":
					$estado = "PENDIENTE";
					$color = "#2196F3";
					break;
				case "1":
					$estado = "APROBADO";
					$color = "#009688";
					break;
				case "2":
					$estado = "RECHAZADO";
					$color = "#F44336";
					break;
			}
			$id_producto = $row['id'];
			$html .= "         
                  <li>
                  <div class='feat_small_icon'><img src='" . $imagen_producto . "' alt='' title='" . utf8_encode($nombre_producto) . "' /></div>
                  <div class='feat_small_details'>
                  <h4>" . utf8_encode($nombre_producto) . "</h4>
                  <div class='chip' style='background-color: " . $color . " !important;'>
                  " . $estado . "
                  </div>" . utf8_encode($descripcion_producto) . "
                  </div>
                  ";
			if ($estado_producto == 1) {
				$html .= "   
                  <div class='view_more'><a href='evento_detalle.html?id_prod=" . $id_producto . "'><img src='images/load_posts_disabled.png' alt='' title='' /></a></div>
                  </li> ";
			} else {
				$html .= "   
                  </li> ";
			}


		}
		return $html;
	}

	public function ObtieneNotificaciones() {
		$sql = "SELECT
            mzapcr_usuario.nombre,
            mzapcr_usuario.foto,
            mzapcr_eventos.nombre_producto,
            mzapcr_eventos.imagen64,
            mzapcr_notificacion_comentario.id AS id_notificacion,
            mzapcr_eventos.id AS id_producto,
            mzapcr_notificacion_comentario.id_comentario,
            Count(DISTINCT mzapcr_notificacion_comentario.id_usuario_comentario) AS total,
            mzapcr_notificacion_comentario.id_usuario_comentario
            FROM
                        mzapcr_eventos
                        INNER JOIN mzapcr_notificacion_comentario ON mzapcr_eventos.id = mzapcr_notificacion_comentario.id_producto
                        INNER JOIN mzapcr_usuario ON mzapcr_notificacion_comentario.id_usuario_comentario = mzapcr_usuario.id
            WHERE
                        mzapcr_notificacion_comentario.id_usuario_producto = $this->id AND mzapcr_notificacion_comentario.estado=0
                        AND mzapcr_notificacion_comentario.id_usuario_producto <> mzapcr_notificacion_comentario.id_usuario_comentario
            GROUP BY mzapcr_eventos.id ";
		//echo $sql;
		$result = $this->conexion->consulta($sql);
		$html = "";
		if (mysqli_num_rows($result) == 0) {
			$html = "No hay notificaciones";
		} else {
			while ($row = mysqli_fetch_array($result)) {
				$nombre_usuario_notifica = $row['nombre'];
				$imagen_usuario_notifica = $row['foto'];
				$nombre_producto = $row['nombre_producto'];
				$imagen_producto = $row['imagen64'];
				$id_notificacion = $row['id_notificacion'];
				$id_producto = $row['id_producto'];
				$id_comentario = $row['id_comentario'];
				$total = $row['total'];
				$html .= "         
                      <li>
                      <div class='feat_small_icon'><img src='" . $imagen_usuario_notifica . "' alt='' title='" . $nombre_usuario_notifica . "' /></div>
                      <div class='feat_small_details'>";
				if ($total > 1) {
					$html .= "<h4>" . $nombre_usuario_notifica . "</h4> y " . ($total - 1) . " persona más han comentado tu producto <h2>" . utf8_encode($nombre_producto) . "</h2>
                        </div>
                        <div class='view_more'><a href='evento_detalle.html?id_prod=" . $id_producto . "&tipo=notificacion&id_comentario=" . $id_comentario . "'><img src='images/load_posts_disabled.png' alt='' title='' /></a></div>
                        </li> ";
				} else {
					$html .= "<h4>" . $nombre_usuario_notifica . "</h4>  ha comentado tu producto <h2>" . utf8_encode($nombre_producto) . "</h2>
                        </div>
                        <div class='view_more'><a href='evento_detalle.html?id_prod=" . $id_producto . "&tipo=notificacion&id_comentario=" . $id_comentario . "'><img src='images/load_posts_disabled.png' alt='' title='' /></a></div>
                        </li> ";
				}

			}
		}


		$sql = "SELECT
            mzapcr_usuario.nombre,
            mzapcr_usuario.foto,
            mzapcr_eventos.nombre_producto,
            mzapcr_eventos.imagen64,
            mzapcr_notificacion_respuestas.id AS id_notificacion,
            mzapcr_eventos.id AS id_producto,
            mzapcr_notificacion_respuestas.id_padre,
            Count(DISTINCT mzapcr_notificacion_respuestas.id_usuario_hijo) AS total,
            mzapcr_notificacion_respuestas.id_usuario_padre
            FROM
                        mzapcr_eventos
                        INNER JOIN mzapcr_notificacion_respuestas ON mzapcr_eventos.id = mzapcr_notificacion_respuestas.id_producto
                        INNER JOIN mzapcr_usuario ON mzapcr_notificacion_respuestas.id_usuario_hijo = mzapcr_usuario.id
            WHERE
                        mzapcr_notificacion_respuestas.id_usuario_padre = $this->id AND mzapcr_notificacion_respuestas.estado=0
                        AND mzapcr_notificacion_respuestas.id_usuario_padre <> mzapcr_notificacion_respuestas.id_usuario_hijo
            GROUP BY mzapcr_eventos.id ";
		//echo $sql;
		$result = $this->conexion->consulta($sql);
		if (mysqli_num_rows($result) == 0) {
			// $html = "No hay notificaciones";
		} else {
			if ($html == "No hay notificaciones") {
				$html = "";
			}
			while ($row = mysqli_fetch_array($result)) {
				$nombre_usuario_notifica = $row['nombre'];
				$imagen_usuario_notifica = $row['foto'];
				$nombre_producto = $row['nombre_producto'];
				$imagen_producto = $row['imagen64'];
				$id_notificacion = $row['id_notificacion'];
				$id_producto = $row['id_producto'];
				$id_comentario = $row['id_padre'];
				$total = $row['total'];
				$html .= "         
                      <li>
                      <div class='feat_small_icon'><img src='" . $imagen_usuario_notifica . "' alt='' title='" . $nombre_usuario_notifica . "' /></div>
                      <div class='feat_small_details'>";
				if ($total > 1) {
					$html .= "<h4>" . $nombre_usuario_notifica . "</h4> y " . ($total - 1) . " persona más han comentado tu respuesta en el producto <h2>" . utf8_encode($nombre_producto) . "</h2>
                        </div>
                        <div class='view_more'><a href='evento_detalle.html?id_prod=" . $id_producto . "&tipo=notificacionrespuesta&id_comentario=" . $id_comentario . "'><img src='images/load_posts_disabled.png' alt='' title='' /></a></div>
                        </li> ";
				} else {
					$html .= "<h4>" . $nombre_usuario_notifica . "</h4>  ha comentado tu respuesta en el producto <h2>" . utf8_encode($nombre_producto) . "</h2>
                        </div>
                        <div class='view_more'><a href='evento_detalle.html?id_prod=" . $id_producto . "&tipo=notificacionrespuesta&id_comentario=" . $id_comentario . "'><img src='images/load_posts_disabled.png' alt='' title='' /></a></div>
                        </li> ";
				}

			}
		}


		$sql = "SELECT
            mzapcr_usuario.nombre,
            mzapcr_usuario.foto,
            mzapcr_eventos.nombre_producto,
            mzapcr_eventos.imagen64,
            mzapcr_notificacion_respuestas.id AS id_notificacion,
            mzapcr_eventos.id AS id_producto,
            mzapcr_notificacion_respuestas.id_padre,
            Count(DISTINCT mzapcr_notificacion_respuestas.id_usuario_hijo) AS total,
            mzapcr_notificacion_respuestas.id_usuario_padre
            FROM
            mzapcr_relacion_notificacion
            INNER JOIN mzapcr_notificacion_respuestas ON mzapcr_relacion_notificacion.id_notificacion = mzapcr_notificacion_respuestas.id
            INNER JOIN mzapcr_usuario ON mzapcr_notificacion_respuestas.id_usuario_hijo = mzapcr_usuario.id
            INNER JOIN mzapcr_eventos ON mzapcr_notificacion_respuestas.id_producto = mzapcr_eventos.id
            WHERE
            mzapcr_relacion_notificacion.id_usuario = $this->id AND
            mzapcr_relacion_notificacion.estado = 0
            GROUP BY mzapcr_eventos.id ";
		//echo $sql;
		$result = $this->conexion->consulta($sql);
		if (mysqli_num_rows($result) == 0) {
			// $html = "No hay notificaciones";
		} else {
			if ($html == "No hay notificaciones") {
				$html = "";
			}
			while ($row = mysqli_fetch_array($result)) {
				$nombre_usuario_notifica = $row['nombre'];
				$imagen_usuario_notifica = $row['foto'];
				$nombre_producto = $row['nombre_producto'];
				$imagen_producto = $row['imagen64'];
				$id_notificacion = $row['id_notificacion'];
				$id_producto = $row['id_producto'];
				$id_comentario = $row['id_padre'];
				$total = $row['total'];
				$html .= "         
                      <li>
                      <div class='feat_small_icon'><img src='" . $imagen_usuario_notifica . "' alt='' title='" . $nombre_usuario_notifica . "' /></div>
                      <div class='feat_small_details'>";
				if ($total > 1) {
					$html .= "<h4>" . $nombre_usuario_notifica . "</h4> y " . ($total - 1) . " persona más han comentado tu respuesta en el producto <h2>" . utf8_encode($nombre_producto) . "</h2>
                        </div>
                        <div class='view_more'><a href='evento_detalle.html?id_prod=" . $id_producto . "&tipo=notificacionrespuestarelacion&id_comentario=" . $id_comentario . "'><img src='images/load_posts_disabled.png' alt='' title='' /></a></div>
                        </li> ";
				} else {
					$html .= "<h4>" . $nombre_usuario_notifica . "</h4>  ha comentado tu respuesta en el producto <h2>" . utf8_encode($nombre_producto) . "</h2>
                        </div>
                        <div class='view_more'><a href='evento_detalle.html?id_prod=" . $id_producto . "&tipo=notificacionrespuestarelacion&id_comentario=" . $id_comentario . "'><img src='images/load_posts_disabled.png' alt='' title='' /></a></div>
                        </li> ";
				}

			}
		}


		return $html;
	}

	public function ObtieneCantidadProductos() {
		$sql = "SELECT
            Count(mzapcr_eventos.id) as cuenta
            FROM
            mzapcr_eventos
            INNER JOIN mzapcr_usuario ON mzapcr_usuario.id = mzapcr_eventos.id_usuario
            WHERE
            mzapcr_usuario.id = $this->id ";
		//echo $sql;
		$result = $this->conexion->consulta($sql);
		$cantidad = 0;
		while ($row = mysqli_fetch_array($result)) {
			$cantidad = $row['cuenta'];

		}
		return $cantidad;
	}

	public function ObtieneCantidadAlertas() {
		$sql = "SELECT
            mzapcr_usuario.nombre,
            mzapcr_eventos.nombre_producto,
            mzapcr_eventos.id AS id_producto,
            Count(DISTINCT mzapcr_notificacion_comentario.id_usuario_comentario) AS total
            FROM
                        mzapcr_eventos
                        INNER JOIN mzapcr_notificacion_comentario ON mzapcr_eventos.id = mzapcr_notificacion_comentario.id_producto
                        INNER JOIN mzapcr_usuario ON mzapcr_notificacion_comentario.id_usuario_comentario = mzapcr_usuario.id
            WHERE
                        mzapcr_notificacion_comentario.id_usuario_producto = $this->id AND mzapcr_notificacion_comentario.estado=0
                        AND mzapcr_notificacion_comentario.id_usuario_producto <> mzapcr_notificacion_comentario.id_usuario_comentario
            GROUP BY mzapcr_eventos.id  ";
		//echo $sql;
		$result = $this->conexion->consulta($sql);
		$cantidad = mysqli_num_rows($result);
		$contador = 0;
		while ($row = mysqli_fetch_array($result)) {
			$contador++;

		}


		$sql = "SELECT
            mzapcr_usuario.nombre,
            mzapcr_eventos.nombre_producto,
            mzapcr_eventos.id AS id_producto,
            Count(DISTINCT mzapcr_notificacion_respuestas.id_usuario_hijo) AS total
            FROM
                        mzapcr_eventos
                        INNER JOIN mzapcr_notificacion_respuestas ON mzapcr_eventos.id = mzapcr_notificacion_respuestas.id_producto
                        INNER JOIN mzapcr_usuario ON mzapcr_notificacion_respuestas.id_usuario_hijo = mzapcr_usuario.id
            WHERE
                        mzapcr_notificacion_respuestas.id_usuario_padre = $this->id AND mzapcr_notificacion_respuestas.estado= 0
                        AND mzapcr_notificacion_respuestas.id_usuario_padre <> mzapcr_notificacion_respuestas.id_usuario_hijo
            GROUP BY mzapcr_eventos.id";
		//echo $sql;
		$result = $this->conexion->consulta($sql);
		$cantidad = $cantidad + mysqli_num_rows($result);
		while ($row = mysqli_fetch_array($result)) {
			$contador++;

		}


		$sql = "SELECT
            Count(mzapcr_relacion_notificacion.id_notificacion) as cuenta
            FROM
            mzapcr_relacion_notificacion
            WHERE
            mzapcr_relacion_notificacion.id_usuario = $this->id AND
            mzapcr_relacion_notificacion.estado = 0";
		//echo $sql;
		$result = $this->conexion->consulta($sql);
		$cuenta = 0;
		while ($row = mysqli_fetch_array($result)) {
			$cuenta = $row['cuenta'];

		}
		$cantidad = $cantidad + $cuenta;

		return $cantidad;
	}

	public function EliminaAlertas($id_producto) {
		$sql = "UPDATE mzapcr_notificacion_comentario set estado=1 WHERE id_usuario_producto = $this->id AND id_producto = $id_producto ";
		echo $sql;
		$result = $this->conexion->consulta($sql);
		$cantidad = mysqli_num_rows($result);
	}

	public function EliminaAlertasRespuestas($id_comentario) {
		$sql = "UPDATE mzapcr_notificacion_respuestas SET estado=1 WHERE id_usuario_padre = $this->id AND id_padre = $id_comentario ";
		//echo $sql;
		$result = $this->conexion->consulta($sql);
		$cantidad = mysqli_num_rows($result);
	}

	public function EliminaAlertasRelacionRespuestas($id_producto) {
		$sql = "UPDATE mzapcr_relacion_notificacion SET estado=1 WHERE id_usuario = $this->id AND id_producto = $id_producto ";
		echo $sql;
		$result = $this->conexion->consulta($sql);
		$cantidad = mysqli_num_rows($result);
	}

	public function ObtieneISO() {
		$sql = "SELECT
            mzapcr_paises.iso
            FROM
            mzapcr_paises
            WHERE
            mzapcr_paises.prefijo = $this->prefijo ";
		//echo $sql;
		$result = $this->conexion->consulta($sql);
		while ($row = mysqli_fetch_array($result)) {
			$iso = $row['iso'];

		}
		return $iso;
	}

	private function unique_randoms($min, $max, $count) {
		$numbers = array();
		while (count($numbers) < $count) {
			do {
				$test = rand($min, $max);
			} while (in_array($test, $numbers));
			$numbers[] = $test;
		}
		$codigo = implode("", $numbers);
		return $codigo;
	}


}

?>