<?php
session_start();
//error_reporting(E_ALL);
require_once("../data/dataOrac.php");


if (isset($_POST["action"])) {
    $action = $_POST["action"];
} else {
    $action = $_REQUEST["action"];
}


switch ($action) {

    case "ObtieneTipoAlimento":
        $dataOraciones = new Orac();
        $listaTipoAlimento = $dataOraciones->ObtenerListadoTipoAlimento();
        $datos = '';
        foreach ($listaTipoAlimento as $lista) {
            if ($lista["id"] == 1) {
                $datos .= '<option value="' . $lista["id"] . '" selected>' . utf8_encode($lista["tipo_alimento"]) . '</option>';
            } else {
                $datos .= '<option value="' . $lista["id"] . '">' . utf8_encode($lista["tipo_alimento"]) . '</option>';
            }

        }
        $datos .= '';
        echo $datos;


        break;

    case "ObtieneOracion":
        if (isset($_POST["id"])) {
            $id = $_POST["id"];
        } else {
            $id = $_REQUEST["id"];
        }

        if ($id != 2) {
            $dataOraciones = new Orac();
            $dataOraciones->id = $id;
            $dataOraciones->ObtenerOracion();

            $datos = '' . utf8_encode($dataOraciones->bendicion_previa) . '<br><br>';

            $datos .= '' . utf8_encode($dataOraciones->bendicion_posterior) . '<br><br>';

            $datos .= '' . utf8_encode($dataOraciones->alimentos) . '<br><br>';


            echo $datos;
        } else {
            $dataOraciones1 = new Orac();
            $dataOraciones1->id = 2;
            $dataOraciones1->ObtenerOracion();

            $dataOraciones2 = new Orac();
            $dataOraciones2->id = 3;
            $dataOraciones2->ObtenerOracion();

            $datos = '' . utf8_encode($dataOraciones1->bendicion_previa) . '<br><br>';

            $datos .= '' . utf8_encode($dataOraciones1->bendicion_posterior) . '<br><br>';

            $datos .= '' . utf8_encode($dataOraciones1->alimentos) . '<br><br>';



            echo $datos;
        }


        break;

}

?>