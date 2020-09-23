<?php
//session_start();
//error_reporting(E_ALL);
//require_once("../data/dataEvento.php");
//require_once("../data/dataComentarios.php");
//
//
//if (isset($_POST["action"])) {
//    $action = $_POST["action"];
//} else {
//    $action = $_REQUEST["action"];
//}
//
//
//switch ($action) {
//
//    case "mapMarkers":
//        $pais = "CR"; //$_POST["pais"];
//        $dataEvento = new Evento();
//        $result = $dataEvento->map_markers();
//
////        if (empty($listaProductos)) {
////            $categoria = $dataProductos->ObtenerCategoriaxEvento();
////            $datos = '<strong><span style="color:#fff;">No hay Productos en esta categoría. Ayuda a la comunidad agregando un producto.</span></strong> |' . utf8_encode($categoria) . '';
////        } else {
////        }
//
//        echo $result;
//
//        break;
//
//    default :
//        break;
//}

session_start();
error_reporting(E_ALL);
require_once("../data/dataEncuesta.php");

var_dump($_POST);

if (isset($_POST['user_id'])) {
//    $con = mysqli_connect("", "test", "test", "Flashcards");

	$dataEncuesta = new Encuesta();

	$values = array();
	$columns = array();
	foreach($_POST as $key => $value) {
		if (!empty($key) && $key != "submit") {
			$values[] = $value;
			$columns[] = $key;
		}
	}
	$colStr = implode(",",$columns);
	$valStr = implode("','",$values);
	$sql = "INSERT INTO mzapcr_encuesta ($colStr) VALUES ('$valStr')";

	$result = $this->conexion->consulta($sql);
	var_dump($result);
//	$this->id = mysqli_insert_id();
//	$this->insertaNotificacionComentarioPadre($this->id);
	if ($result==0)
	{
		return 0;
	}
//	return $this->id;

} else {
	echo "wtf!";
}



$data  = $myQuery;

echo $data;
