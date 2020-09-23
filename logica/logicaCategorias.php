<?php
session_start();
error_reporting(E_ALL ^ E_DEPRECATED);
//error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
require_once("../data/dataCategorias.php");
// Turn off all error reporting
//header("Access-Control-Allow-Origin: *");



if (isset($_POST["action"])) {
    $action = $_POST["action"];
} else {
    $action = $_REQUEST["action"];
}


switch ($action) {

    case "ObtieneCategorias":
        $isoCountry = 'CR'; //$_POST["iso"];
        $dataCategorias = new Categorias();
        $dataCategorias->iso = $isoCountry;
        $listaCategorias = $dataCategorias->ObtenerListadoCategorias();
        $datos = "";
        foreach($listaCategorias as $lista)
			{
              /*  $datos .='<li onClick="ObtieneEvento('.$lista["id_categ"].');"><a href="#portfolio" id="portfolio-link" class="skel-layers-ignoreHref"><span class="icon fa-plus-square-o">'.utf8_encode($lista["categoria"]).'</span></a></li>';*/
                $dataCategorias->id_categoria = $lista["id_categ"];
                $dataCategorias->ObtenerCantidadxCategorias();
                $datos .='<li>
                    
                        <div class="post_date">
                            <!--<span class="day">'.$lista["cantidad"].'</span>-->
                            <span class="day">'.$dataCategorias->cantidad.'</span>
                            
                        </div>
                        <div class="post_title">
                        <h2><a href="eventos.html?id_cat='.$lista["id_categ"].'"><div class="post_entry">'.utf8_encode($lista["categoria"]).'</a></h2>
                        </div>
                    </div>
                </li>';
	            
            }
        echo $datos;

        //$dataCategorias->cerrar();	
			
        
        break;
        
    case "ComboCategorias":
        $id_categoria = $_POST["id_categoria"];
        $dataCategorias = new Categorias();
        $listaCategorias = $dataCategorias->ObtenerListadoCategorias();
        $contador = 0;
        $datos = '';
	    $datos .='<option value="0" selected>Seleccione una categoría</option>';
        foreach($listaCategorias as $lista)
			{
                /*if($id_categoria == 0 && $contador == 0){
                    $datos .='<option value="0" selected>Seleccione una categoría</option>';
                    $contador++;
                }else */ if($id_categoria == $lista["id_categ"]){
                    $datos .='<option value="'.$lista["id_categ"].'" selected>'.utf8_encode($lista["categoria"]).'</option>';
                }else{
                    $datos .='<option value="'.$lista["id_categ"].'">'.utf8_encode($lista["categoria"]).'</option>';
                }
	            
            }
        $datos .='';
        echo $datos;

        //$dataCategorias->cerrar();	
			
        
        break;
        
        default :
        break;
}