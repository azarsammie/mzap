<?php
session_start();
//error_reporting(E_ALL);
require_once("../data/dataEvento.php");
require_once("../data/dataComentarios.php");


if (isset($_POST["action"])) {
    $action = $_POST["action"];
} else {
    $action = $_REQUEST["action"];
}


switch ($action) {

    case "mapMarkers":
        $pais = "CR"; //$_POST["pais"];
        $dataEvento = new Evento();
        $result = $dataEvento->map_markers();


        echo $result;

        break;

    default :
        break;
}