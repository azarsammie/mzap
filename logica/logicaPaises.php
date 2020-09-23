<?php
session_start();
//error_reporting(E_ALL);
require_once("../data/dataPaises.php");



if (isset($_POST["action"])) {
    $action = $_POST["action"];
} else {
    $action = $_REQUEST["action"];
}


switch ($action) {

    case "ObtienePaises":
        $dataPaises = new Pais();
        $listaPaises = $dataPaises->ObtienePaises();
        if(empty($listaPaises))
        {
            $datos = "Error , intente más tarde ";
        }
        else
        {
            foreach($listaPaises as $pais)
            {
                $datos.= '         
                  <li onClick="selectpais(\''.$pais["prefijo"].'\',\''.$pais["nombre"].'\',\'data:image/jpeg;base64,'.$pais["bandera"].'\')">
                  <div class="feat_small_icon"><img src="data:image/jpeg;base64,'.$pais["bandera"].'" alt="" title="'.$pais["nombre"].'" /></div>
                  <div class="feat_small_details">
                  <font color="black">"'.utf8_encode($pais["nombre"]).'"</font>
                  </li> ';
            }

        }
        echo $datos;
        
        break;
        
    case "ObtienePaisActual":
        $iso_pais = $_POST["iso"];
        $dataPaises = new Pais();
        $dataPaises->iso = $iso_pais;
        $dataPaises->ObtenerPaisActual();
        
        $datos = '<li>
              <div class="post_entry">
                  <div class="post_date">
                      <img src="data:image/jpeg;base64,'.$dataPaises->bandera.'" alt="" title="" />
                  </div>
                  <div class="post_title">
                      <h2><a href="#" id="pais_selected"><font color="white">'.utf8_encode($dataPaises->nombre).'</font></a></h2>
                  </div>
              </div>
          </li>';
        
        echo $datos;
        
        break;
        
    case "ObtienePaisActualPopup":
        $iso_pais = $_POST["iso"];
        $dataPaises = new Pais();
        $dataPaises->iso = $iso_pais;
        $dataPaises->ObtenerPaisActual();
        
        $datos = '<li>
              <div class="post_entry">
                  <div class="post_date">
                      <img src="data:image/jpeg;base64,'.$dataPaises->bandera.'" alt="" title="" />
                  </div>
                  <div class="post_title">
                      <h2><a href="#" data-popup=".popup-paises" class="open-popup" id="pais_selected">'.utf8_encode($dataPaises->nombre).'</a></h2>
                  </div>
              </div>
          </li>';
        
        echo $datos;
        
        break;
        
    case "ObtieneListadoPaises":
        $dataPaises = new Pais();
        $dataPaises->iso = $iso_pais;
        $listaPaises = $dataPaises->ObtenerListadoPaises();
        $datos ='';
        foreach($listaPaises as $pais){
        $datos .= '<li>
              <div class="post_entry">
                  <div class="post_date">';
                    if(empty($pais["bandera"])){
                        
                    }else{
                        $datos .= '<img src="data:image/jpeg;base64,'.$pais["bandera"].'" alt="" title="" />';
                    }
                  $datos .= '</div>
                  <div class="post_title">
                      <h2><a href="#" onClick="CambiaPais(\''.$pais["iso"].'\');" id="pais_selected">'.$pais["nombre"].'</a></h2>
                  </div>
              </div>
          </li>';
        }
        echo $datos;
        
        break;
        
    case "ObtieneListadoPaisesPopup":
        $dataPaises = new Pais();
        $dataPaises->iso = $iso_pais;
        $listaPaises = $dataPaises->ObtenerListadoPaises();
        $datos ='';
        foreach($listaPaises as $pais){
        $datos .= '<li style="background-color:#ECECEC !important;">
              <div class="post_entry">
                  <div class="post_date">';
                    if(empty($pais["bandera"])){
                        
                    }else{
                        $datos .= '<img src="data:image/jpeg;base64,'.$pais["bandera"].'" alt="" title="" />';
                    }
                  $datos .= '</div>
                  <div class="post_title">
                      <h2><a href="#" onClick="CambiaPaisPopup(\''.$pais["iso"].'\');" id="pais_selected">'.$pais["nombre"].'</a></h2>
                  </div>
              </div>
          </li>';
        }
        echo $datos;
        
        break;
        
        default :
        break;
}