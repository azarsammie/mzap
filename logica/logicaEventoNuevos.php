<?php
session_start();
//error_reporting(E_ALL);
require_once("../data/dataEventoNuevo.php");


if (isset($_POST["action"])) {
    $action = $_POST["action"];
} else {
    $action = $_REQUEST["action"];
}


switch ($action) {

    case "ObtieneEventoNuevos":
        $pais = "CR"; //$_POST["pais"];
        $dataProductos = new EventoNuevo();
        $dataProductos->pais = $pais;
        $listaProductos = $dataProductos->ObtenerListadoEventoNuevos();
        if (empty($listaProductos)) {
            $datos = '<strong><span style="color:#fff;">No hay Productos Nuevos.</span></strong>';
        } else {
            $contador = 0;
            $datos = '';
            foreach ($listaProductos as $lista) {


                $datos .= '<li ><div class="content-block inset" style="background-color:#fff !important;">
                <div class="chip">
                  <img src="' . utf8_encode($lista["foto"]) . '" alt="Person" width="96" height="96" id="imagenperfil" onClick="mostrarimagen(\'' . utf8_encode($lista["foto"]) . '\')">
                  <p class="texto-chip"><strong>' . $lista["nombre"] . '</strong><span>' . $lista["fechaP"] . '</span></p>
                </div>';


                $datos .= '
                <a href="evento_detalle.html?id_prod=' . $lista["id"] . '&id_categ=' . $lista["id_categ"] . '" title="Photo title"><img src="' . $lista["imagen64"] . '" alt="image"/><center><p><h1>' . utf8_encode($lista["nombre_producto"]) . '</h1></p></center>
                </a>';

                $dataProductos->id = $lista["id"];
                $dataProductos->ObtenerEstrellasEvento();
                $cantidadEstrellas = $dataProductos->totalEstrellas;
                $valorDecimal = floatval($cantidadEstrellas);
                $result = is_float($valorDecimal);
                $cantidadEstrellas = intval($cantidadEstrellas);
                $letrasEstrellas = "";

                for ($i = 0; $i < 5; $i++) {
                    if ($i < $cantidadEstrellas) {
                        //$letrasEstrellas .= "★";
                        $letrasEstrellas .= '<img src="images/icons/white/mZapCR_S.png" style="float:left;width:32px; height:32px;align:center;" alt="" title="" />';
                    } else {
                        if (($result == 1) && ($cantidadEstrellas == $i) && ($cantidadEstrellas > 0)) {
                            $letrasEstrellas .= '<img src="images/icons/white/mZapCR_M.png" style="float:left;width:32px; height:32px;align:center;" alt="" title="" />';
                        } else {
                            //$letrasEstrellas .= "☆";
                            $letrasEstrellas .= '<img src="images/icons/white/mZapCR_N.png" style="float:left;width:32px; height:32px;align:center;" alt="" title="" />';
                        }
                    }
                }

                $datos .= '<center><h3 style="color:#007AFF; text-align:center; display: inline-block;" name="puntuacion' . $dataProductos->id . '" id="puntuacion' . $dataProductos->id . '">' . $letrasEstrellas . '</h3><center>';

                $datos .= '<!-- Inicio Porcentaje  -->
              <div class="chip_porcentaje">
                   <span id="cant_porcentaje' . $dataProductos->id . '" >' . number_format($dataProductos->ProcentajeEstrellas, 0, '.', ' ') . ' %</span> Evento comprobado
              </div>
              <!-- Fin Porcentaje  -->';

                $datos .= '<!-- Inicio Votos  -->
              <div class="chip_voto">
                   <span id="cant_votos' . $dataProductos->id . '" >' . number_format($dataProductos->cantidadVotos, 0, '.', ' ') . '</span> Total Votos
              </div>
              <!-- Fin Votos  -->';


                $datos .= '</p></center>
                </a>
                </div>
                
                </li>';
            }
            $datos .= '<div class="clearleft"></div>';

            //$dataProductos->cerrarP();
        }
        echo $datos;

        break;

    case "BuscarEvento":
        $categ = $_POST["id_categoria"];
        $producto = $_POST["producto"];
        $dataProductos = new Evento();
        $dataProductos->id_categoria = $categ;
        $dataProductos->nombre = $producto;
        $listaProductos = $dataProductos->ObtenerListadoBusquedaEvento();
        if (empty($listaProductos)) {
            $categoria = $dataProductos->ObtenerCategoriaxEvento();
            $datos = "No hay eventos con ese nombre en esta categoría. |" . utf8_encode($categoria) . "";
        } else {
            $contador = 0;
            $datos = '<div class="4u 12u$(mobile)">';
            foreach ($listaProductos as $lista) {
                $datos .= '<article class="item">
										<img class="image fit" content="img-src *" src="' . $lista["imagen64"] . '" alt="" />
										<header>
											<h3>' . utf8_encode($lista["nombre_producto"]) . '</h3>
                                            <h6>' . utf8_encode($lista["descripcion"]) . '</h6>
										</header>
									</article>';
                if ($contador == 2) {
                    $datos .= '</div>
                    <div class="4u 12u$(mobile)">';
                    $contador = 0;
                }
                $contador++;
            }
            $datos .= '</div>|' . utf8_encode($lista["categoria"]) . '';

            //$dataProductos->cerrarP();
        }
        echo $datos;

        break;

    case "SubirEvento":
        $imagen = $_POST["imagen"];
        $imagen = eregi_replace("[\n|\r|\n\r ]", '+', $imagen);
        $id_categoria = $_POST["id_categoria"];
        $nombreProducto = $_POST["nombreProducto"];
        $descripcion = $_POST["descripcion"];
        $miIP = $_POST["miIP"];
        $latitude = $_POST["latitude"];
        $longitude = $_POST["longitude"];
        $pais = $_POST["iso"];

        $dataProductos = new Evento();
        $dataProductos->imagen = $imagen;
        $dataProductos->id_categoria = $id_categoria;
        $dataProductos->nombre = $nombreProducto;
        $dataProductos->descripcion = $descripcion;
        $dataProductos->miIP = $miIP;
        $dataProductos->latitude = $latitude;
        $dataProductos->longitude = $longitude;
        $dataProductos->pais = $pais;

        $respuesta = $dataProductos->InsertaNuevoEvento();

        echo $respuesta;

        break;

    case "DetalleEvento":
        $id_producto = $_POST["id_producto"];
        $id_usuario = $_POST["id_usuario"];
        $dataProductos = new Evento();
        $dataProductos->id = $id_producto;
        $dataProductos->ObtenerDetalleEvento();

        $contador = 0;
        $datos = '';

        $datos .= '<blockquote id="nombre_producto">
              ' . utf8_encode($dataProductos->nombre) . '
              </blockquote>
              
              <img id="imagen_producto" src="' . $dataProductos->imagen . '" alt="" title="" />
              
            <h3>Descripción del evento</h3>
			<p id="descripcion">' . utf8_encode($dataProductos->descripcion) . '</p>
            
              
              <h3>Comentarios</h3>   
              <ul class="comments" id="comentarios">';

        $Comentario = new Comentario();

        $Comentario->limit = 0;
        $Comentario->id_producto = $dataProductos->id;

        $result = $Comentario->seleccionaComentariosPadre();

        if ($result == 0) {
            $datos .= "No existen comentarios";
        } else {

            while ($row = mysqli_fetch_array($result)) {
                $contador_hijos = 0;
                $Padre = new Comentario();
                $Padre->id = $row['id'];
                $Padre->id_usuario = $row['id_usuario'];
                $Padre->id_producto = $row['id_producto'];
                $Padre->fecha = $row['fecha'];
                $Padre->texto = $row['comentario'];
                $nombre = utf8_encode($row['nombre']);
                $foto = $row['foto'];
                $Padre->limit = 0;

                $datos .= '
            <li class="comment_row" id="c' . $Padre->id . '">
            <div class="comm_avatar"><img src="' . $foto . '" alt="" title="" border="0" /></div>
            <div class="comm_content"><p>' . $Padre->fecha . '<br><a href="#">' . $nombre . ':  </a>' . $Padre->texto . ' </p></div>
            <textarea id="limitpadre' . $Padre->id . '" name="limitpadre' . $Padre->id . '" style="display:none" value="0"></textarea>
            <blockquote class="dividir" id="dividir"></blockquote> 
            ';

                $datos .= '<ul class="comments-hijo" id="comments-hijo' . $Padre->id . '">';
                $resulthijos = $Padre->seleccionaComentariosHijos();

                while ($row = mysqli_fetch_array($resulthijos)) {

                    $Hijo = new Comentario();
                    $Hijo->id = $row['id'];
                    $Hijo->id_usuario = $row['id_usuario'];
                    $Hijo->id_producto = $row['id_producto'];
                    $Hijo->fecha = $row['fecha'];
                    $Hijo->texto = $row['comentario'];
                    $Hijonombre = utf8_encode($row['nombre']);
                    $Hijofoto = $row['foto'];


                    $datos .= '
                    <li>                                                    
                            <div class="comm_avatar"><img src="' . $Hijofoto . '" alt="" title="" border="0" /></div>
                            <div class="comm_content"><p>' . $Hijo->fecha . '<br><a href="#">' . $Hijonombre . ':  </a>' . $Hijo->texto . ' </p>
                    </li>';
                    $contador_hijos++;
                }

                $datos .= '</ul>';
                if ($contador_hijos == 4) {
                    $datos .= '<a href="#" onclick="ObtenerComentariosHijosMas(\'' . $Padre->id_producto . '\' , \'' . $Padre->id . '\');"  id="vermashijo' . $Padre->id . '"  class="button_full">Ver mas</a>';
                }
                $datos .= '
            <div class="comm_reply"><img src="images/icons/white/message_black.png" onClick="ViewReplyPadre(\'' . $Padre->id . '\');" alt="reply" title="reply" border="0" /></div>
            <div class="contactform" id="replypadre' . $Padre->id . '" style="display:none">
                <form id="agrega-comentario" name="agrega-comentario" method="POST" class="ajax-submit">
                <textarea id="comentarioreply' . $Padre->id . '" name="comentarioreply' . $Padre->id . '" placeholder="Nuevo Comentario" class="form_textarea" rows="" cols="" required></textarea>
                <input type="button" onClick="ReplyPadre(\'' . $id_usuario . '\',\'' . $dataProductos->id . '\',\'' . $Padre->id . '\');" name="submitComentario" class="form_submit" id="submitComentario" value="Comentar" />
                </form>
                </div>
            </li>';
            }
        }
        $datos .= '<div class="clear"></div>';

        $datos .= '</ul>
              
                <a href="#" onclick="ObtenerComentariosPadreMas();" id="vermaspadre" class="button_full">Ver mas</a>


                <div class="contactform">
                <form id="agrega-comentario" name="agrega-comentario" method="POST" class="ajax-submit">
                <textarea id="comentario" name="comentario" placeholder="Nuevo Comentario" class="form_textarea" rows="" cols="" required></textarea>
                <input type="button" onClick="AgregarComentario(\'' . $id_usuario . '\',\'' . $dataProductos->id . '\');" name="submitComentario" class="form_submit" id="submitComentario" value="Comentar" />
                </form>
                </div>


              <h3>Opciones:</h3>
              <ul class="simple_list">
              <li><a href="#">Reportar este producto</a></li>
              <li><a href="#">Reportar el usuario</a></li>
              </ul>';


        //$dataProductos->cerrarP();
        echo $datos;

        break;

    default :
        break;
}