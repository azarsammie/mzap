<?php
session_start();
//error_reporting(E_ALL);
require_once("../data/dataVotaciones.php");


if (isset($_POST["action"])) {
    $action = $_POST["action"];
} else {
    $action = $_REQUEST["action"];
}


switch ($action) {

    case "VotoLike":
        $id_usuario = $_POST["id_usuario"];
        $id_producto = $_POST["id_producto"];
        $dataVotaciones = new Votaciones();
        $dataVotaciones->id_producto = $id_producto;
        $dataVotaciones->id_usuario = $id_usuario;
        $dataVotaciones->eventoValido = 1;
        $dataVotaciones->eventoNoValido = 0;
        $dataVotaciones->ObtenerVotacionUsuarioProducto();

        if ($dataVotaciones->cantidad == 0) {
            $result = $dataVotaciones->AgregaVoto();
        } else {
            $result = $dataVotaciones->ActualizaVoto();
        }
        echo $result;

        break;

    case "VotoDiskike":
        $id_usuario = $_POST["id_usuario"];
        $id_producto = $_POST["id_producto"];
        $dataVotaciones = new Votaciones();
        $dataVotaciones->id_producto = $id_producto;
        $dataVotaciones->id_usuario = $id_usuario;
        $dataVotaciones->eventoValido = 0;
        $dataVotaciones->eventoNoValido = 1;
        $dataVotaciones->ObtenerVotacionUsuarioProducto();

        if ($dataVotaciones->cantidad == 0) {
            $result = $dataVotaciones->AgregaVoto();
        } else {
            $result = $dataVotaciones->ActualizaVoto();
        }
        echo $result;

        break;

    default :
        break;
}