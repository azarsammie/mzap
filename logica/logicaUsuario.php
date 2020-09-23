<?php
session_start();
//error_reporting(E_ALL);
require_once("../data/dataUsuario.php");


if (isset($_POST["action"])) {
    $action = $_POST["action"];
} else {
    $action = $_REQUEST["action"];
}


switch ($action) {

    case "Registro":

        $usuario = new Usuario();

        if (isset($_POST["prefijo"]) && isset($_POST["telefono"])) {
            $usuario->prefijo = $_POST["prefijo"];
            $usuario->telefono = $_POST["telefono"];
            $usuario->correo = $_POST["correo"];
        } else {
            $usuario->prefijo = $_REQUEST["prefijo"];
            $usuario->telefono = $_REQUEST["telefono"];
            $usuario->correo = $_REQUEST["correo"];
        }

        $usuario->GeneraCodigo();
	    $usuario->estado = "PENDIENTE";
	    $result = $usuario->CreaUsuario();

	    $subject = "MZAPCR :: Registro";
	    $messages = "Utilice el código " . $usuario->codigo . " para registrarse.";
	    $from = "mzapcr@juanbarrios.com";
	    $Reply = "mzapcr@juanbarrios.com";
	    $mail = mail($usuario->correo, $subject, $messages, "from: $from <$Reply>\nReply-To: $Reply \nContent-type: text/html");

	    echo $result;

        break;


    case "ReCodigo":

        $usuario = new Usuario();

        if (isset($_POST["prefijo"]) && isset($_POST["telefono"])) {
            $usuario->prefijo = $_POST["prefijo"];
            $usuario->telefono = $_POST["telefono"];
        } else {
            $usuario->prefijo = $_REQUEST["prefijo"];
            $usuario->telefono = $_REQUEST["telefono"];
        }

        $usuario->GeneraCodigo();
        $usuario->estado = "PENDIENTE";
        $result = $usuario->CreaUsuario();

        echo $result;

        break;

    case "ValidaUP":

        $usuario = new Usuario();

        if (isset($_POST["telefono"])) {
            $usuario->telefono = $_POST["telefono"];
            $usuario->codigo = $_POST["codigo"];
        } else {
            $usuario->telefono = $_REQUEST["telefono"];
            $usuario->codigo = $_REQUEST["codigo"];
        }

        $result = $usuario->ObtieneIdUser();
        $result = ($result == NULL) ? 0 : $result;
//        echo json_encode($result);
        echo json_encode($result, JSON_PRETTY_PRINT);

        break;

    case "ObtieneISO":

        $usuario = new Usuario();

        if (isset($_POST["prefijo"])) {
            $usuario->prefijo = $_POST["prefijo"];
        } else {
            $usuario->prefijo = $_REQUEST["prefijo"];
        }

        $result = $usuario->ObtieneISO();

        echo $result;

        break;

    case "VerificaUsuario":

        $usuario = new Usuario();

        if (isset($_POST["prefijo"]) && isset($_POST["telefono"]) && isset($_POST["codigo"]) && isset($_POST["iso"])) {
            $usuario->prefijo = $_POST["prefijo"];
            $usuario->telefono = $_POST["telefono"];
            $usuario->codigo = $_POST["codigo"];
            $usuario->iso = $_POST["iso"];
        } else {
            $usuario->prefijo = $_REQUEST["prefijo"];
            $usuario->telefono = $_REQUEST["telefono"];
            $usuario->codigo = $_REQUEST["codigo"];
            $usuario->iso = $_REQUEST["iso"];
        }

        $result = $usuario->VerificaUsuario();
        if ($result == 1) {
            $usuario->SeleccionaIdUsuario();
            echo $usuario->id;
        } else {
            echo $result;
        }


        break;

    case "InfoUsuario":

        $usuario = new Usuario();

        if (isset($_POST["telefono"]) && isset($_POST["prefijo"]) && isset($_POST["foto"]) && isset($_POST["nombre"]) && isset($_POST["correo"]) && isset($_POST["fecha_nacimiento"]) && isset($_POST["sexo"])) {
            $usuario->prefijo = $_POST["prefijo"];
            $usuario->telefono = $_POST["telefono"];
//            $usuario->nombre = $_POST["nombre"];
            $usuario->nombre = $_POST["telefono"];
            $usuario->correo = $_POST["correo"];
//            $usuario->foto = ""; //eregi_replace("[\n|\r|\n\r ]", '+', $_POST["foto"]);
	        $usuario->foto = "data:image/jpeg;base64,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";
            $usuario->fecha_nacimiento = $_POST["fecha_nacimiento"];
            $usuario->sexo = $_POST["sexo"];
        } else {
            $usuario->prefijo = $_REQUEST["prefijo"];
            $usuario->telefono = $_REQUEST["telefono"];
//            $usuario->nombre = $_REQUEST["nombre"];
            $usuario->nombre = $_REQUEST["telefono"];
            $usuario->correo = $_REQUEST["correo"];
//            $usuario->foto = ""; //eregi_replace("[\n|\r|\n\r ]", '+', $_REQUEST["foto"]);
            $usuario->foto = "data:image/jpeg;base64,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";
            $usuario->fecha_nacimiento = $_REQUEST["fecha_nacimiento"];
            $usuario->sexo = $_REQUEST["sexo"];
        }

        $result = $usuario->GuardaInfoUsuario();

        echo $result;

        break;

    case "MyProducts":

        $usuario = new Usuario();

        if (isset($_POST["idusuario"])) {
            $usuario->id = $_POST["idusuario"];

        } else {
            $usuario->id = $_REQUEST["idusuario"];
        }

        $result = $usuario->ObtieneEvento();

        echo $result;

        break;

    case "CantidadMyProducts":

        $usuario = new Usuario();

        if (isset($_POST["idusuario"])) {
            $usuario->id = $_POST["idusuario"];

        } else {
            $usuario->id = $_REQUEST["idusuario"];
        }

        $result = $usuario->ObtieneCantidadProductos();

        echo $result;

        break;

    case "EliminaNotificaciones":

        $usuario = new Usuario();

        if (isset($_POST["idusuario"]) && isset($_POST["idproducto"])) {
            $usuario->id = $_POST["idusuario"];
            $idproducto = $_POST["idproducto"];

        } else {
            $usuario->id = $_REQUEST["idusuario"];
            $idproducto = $_REQUEST["idproducto"];
        }

        $result = $usuario->EliminaAlertas($idproducto);

        echo $result;

        break;


    case "EliminaNotificacionesRespuesta":

        $usuario = new Usuario();

        if (isset($_POST["idusuario"]) && isset($_POST["id_comentario"])) {
            $usuario->id = $_POST["idusuario"];
            $id_comentario = $_POST["id_comentario"];

        } else {
            $usuario->id = $_REQUEST["idusuario"];
            $id_comentario = $_REQUEST["id_comentario"];
        }

        $result = $usuario->EliminaAlertasRespuestas($id_comentario);

        echo $result;

        break;


    case "EliminaNotificacionesRelacionRespuesta":

        $usuario = new Usuario();

        if (isset($_POST["idusuario"]) && isset($_POST["id_producto"])) {
            $usuario->id = $_POST["idusuario"];
            $id_producto = $_POST["id_producto"];

        } else {
            $usuario->id = $_REQUEST["idusuario"];
            $id_producto = $_REQUEST["id_producto"];
        }

        $result = $usuario->EliminaAlertasRelacionRespuestas($id_producto);

        echo $result;

        break;

    case "CantidadAlertas":

        $usuario = new Usuario();

        if (isset($_POST["idusuario"])) {
            $usuario->id = $_POST["idusuario"];

        } else {
            $usuario->id = $_REQUEST["idusuario"];
        }

        $result = $usuario->ObtieneCantidadAlertas();

        echo $result;

        break;

    case "MyNotificaciones":

        $usuario = new Usuario();

        if (isset($_POST["idusuario"])) {
            $usuario->id = $_POST["idusuario"];

        } else {
            $usuario->id = $_REQUEST["idusuario"];
        }

        $result = $usuario->ObtieneNotificaciones();

        echo $result;

        break;

    default :
        break;
}