<?php
session_start();
//error_reporting(E_ALL);
require_once("../data/dataComentarios.php");



if (isset($_POST["action"])) {
    $action = $_POST["action"];
} else {
    $action = $_REQUEST["action"];
}


switch ($action) {

    case "InsertaComentario":

            $Comentario = new Comentario();

            if (isset($_POST["id_usuario"]) && isset($_POST["id_producto"]) && isset($_POST["texto"]) ) 
            {
                $Comentario->id_usuario = $_POST["id_usuario"];
                $Comentario->id_producto = $_POST["id_producto"];
                $Comentario->texto = $_POST["texto"];
            } 
            else 
            {
                $Comentario->id_usuario = $_REQUEST["id_usuario"];
                $Comentario->id_producto = $_REQUEST["id_producto"];
                $Comentario->texto = $_REQUEST["texto"];
            }

            $result = $Comentario->insertaComentarioPadre();

            echo $result;
        
        break; 

    case "ObtieneComentariosPadre":

            $Comentario = new Comentario();

            $html = "";

            if (isset($_POST["id_producto"]) && isset($_POST["limit"]) && isset($_POST["id_usuario"])   ) 
            {
                $Comentario->limit = $_POST["limit"];
                $Comentario->id_producto = $_POST["id_producto"];
                $id_usuario = $_POST["id_usuario"];
            } 
            else 
            {
                $Comentario->limit = $_REQUEST["limit"];
                $Comentario->id_producto = $_REQUEST["id_producto"];
                $id_usuario = $_REQUEST["id_usuario"];
            }

            $result = $Comentario->seleccionaComentariosPadre();

            if($result == 0)
            {
                return 0;
            }
            else
            {
                while ($row = mysqli_fetch_array($result))
                {
                    $contador_hijos = 0;
                    $Padre = new Comentario();
                    $Padre->id = $row['id']; 
                    $Padre->id_usuario = $row['id_usuario'];
                    $Padre->id_producto = $row['id_producto'];
                    $Padre->fecha = $row['fecha'];
                    $Padre->texto = $row['comentario']; 
                    $nombre = $row['nombre']; 
                    $foto = $row['foto']; 
                    $Padre->limit = 0;
                    $idcomentario = "#c".$Padre->id;    


                    $html .= '
                    <li class="comment_row" id="c'. $Padre->id .'"  >
                    <div class="comm_avatar"><img src="'. $foto .'" alt="" title="" border="0" onClick="mostrarimagen(\''. $foto .'\')"/></div>
                    <div class="comm_content"><p>'. $Padre->fecha .'<br><a href="#">'. $nombre .':  </a>'. $Padre->texto .' </p></div>
                    <textarea id="limitpadre'. $Padre->id .'" name="limitpadre'. $Padre->id .'" style="display:none" value="0"></textarea>
                    <blockquote class="dividir" id="dividir"></blockquote> ';

                    $html .='

                    <ul class="comments-hijo" id="comments-hijo'. $Padre->id .'">';
                    $resulthijos = $Padre->seleccionaComentariosHijos();
                    
                        while ($row = mysqli_fetch_array($resulthijos))
                        {

                            $Hijo = new Comentario();
                            $Hijo->id = $row['id']; 
                            $Hijo->id_usuario = $row['id_usuario'];
                            $Hijo->id_producto = $row['id_producto'];
                            $Hijo->fecha = $row['fecha'];
                            $Hijo->texto = $row['comentario']; 
                            $Hijonombre = $row['nombre']; 
                            $Hijofoto = $row['foto']; 

                            $html .='
                            <li>                                                    
                                    <div class="comm_avatar"><img src="'. $Hijofoto .'" alt="" title="" border="0" onClick="mostrarimagen(\''. $Hijofoto .'\')"/></div>
                                    <div class="comm_content"><p>'. $Hijo->fecha .'<br><a href="#">'. $Hijonombre .':  </a>'. $Hijo->texto .' </p>
                            </li>';
                            $contador_hijos++;
                        }

                    $html .='</ul>';
                    if($contador_hijos==4)
                    {
                        $html .='<a href="#" onclick="ObtenerComentariosHijosMas(\''.$Padre->id_producto.'\' , \''.$Padre->id.'\');" id="vermashijo'. $Padre->id .'" class="button_full">Ver mas</a>';
                    }

                    $html .= '<div class="comm_reply"><img src="images/icons/white/message_black.png" onClick="ViewReplyPadre('.$Padre->id.');" alt="reply" title="reply" border="0" /></div>
                    
                    <div class="contactform" id="replypadre'. $Padre->id .'" style="display:none">
                    <form id="agrega-comentario" name="agrega-comentario" method="POST" class="ajax-submit">
                    <textarea id="comentarioreply'. $Padre->id .'" name="comentarioreply'. $Padre->id .'" placeholder="Nuevo Comentario" class="form_textarea" rows="" cols="" required></textarea>
                    <input type="button" onClick="ReplyPadre('.$id_usuario.','.$Padre->id_producto.','. $Padre->id  .');" name="submitComentario" class="form_submit" id="submitComentario" value="Comentar" />
                    </form>
                    </div>                   
                    </li>
                    ';
                }

                               
            }
            $html .= '<div class="clear"></div>';
            echo $html;
        
        break; 


   case "ObtieneComentariosPadreTodos":

            $Comentario = new Comentario();

            $html = "";

            if (isset($_POST["id_producto"]) && isset($_POST["limit"]) && isset($_POST["id_usuario"])   ) 
            {
                $Comentario->limit = $_POST["limit"];
                $Comentario->id_producto = $_POST["id_producto"];
                $id_usuario = $_POST["id_usuario"];
            } 
            else 
            {
                $Comentario->limit = $_REQUEST["limit"];
                $Comentario->id_producto = $_REQUEST["id_producto"];
                $id_usuario = $_REQUEST["id_usuario"];
            }

            $result = $Comentario->seleccionaComentariosPadreTodos();

            if($result == 0)
            {
                return 0;
            }
            else
            {
                while ($row = mysqli_fetch_array($result))
                {
                    $contador_hijos = 0;
                    $Padre = new Comentario();
                    $Padre->id = $row['id']; 
                    $Padre->id_usuario = $row['id_usuario'];
                    $Padre->id_producto = $row['id_producto'];
                    $Padre->fecha = $row['fecha'];
                    $Padre->texto = $row['comentario']; 
                    $nombre = $row['nombre']; 
                    $foto = $row['foto']; 
                    $Padre->limit = 0;
                    $idcomentario = "#c".$Padre->id;    


                    $html .= '
                    <li class="comment_row" id="c'. $Padre->id .'"  >
                    <div class="comm_avatar"><img src="'. $foto .'" alt="" title="" border="0" onClick="mostrarimagen(\''. $foto .'\')"/></div>
                    <div class="comm_content"><p>'. $Padre->fecha .'<br><a href="#">'. $nombre .':  </a>'. $Padre->texto .' </p></div>
                    <textarea id="limitpadre'. $Padre->id .'" name="limitpadre'. $Padre->id .'" style="display:none" value="0"></textarea>
                    <blockquote class="dividir" id="dividir"></blockquote> ';

                    $html .='

                    <ul class="comments-hijo" id="comments-hijo'. $Padre->id .'">';
                    $resulthijos = $Padre->seleccionaComentariosHijos();
                    
                        while ($row = mysqli_fetch_array($resulthijos))
                        {

                            $Hijo = new Comentario();
                            $Hijo->id = $row['id']; 
                            $Hijo->id_usuario = $row['id_usuario'];
                            $Hijo->id_producto = $row['id_producto'];
                            $Hijo->fecha = $row['fecha'];
                            $Hijo->texto = $row['comentario']; 
                            $Hijonombre = $row['nombre']; 
                            $Hijofoto = $row['foto']; 
                        

                            $html .='
                            <li>                                                    
                                    <div class="comm_avatar"><img src="'. $Hijofoto .'" alt="" title="" border="0" onClick="mostrarimagen(\''. $Hijofoto .'\')"/></div>
                                    <div class="comm_content"><p>'. $Hijo->fecha .'<br><a href="#">'. $Hijonombre .':  </a>'. $Hijo->texto .' </p>
                            </li>';
                            $contador_hijos++;
                        }

                    $html .='</ul>';
                    if($contador_hijos==4)
                    {
                        $html .='<a href="#" onclick="ObtenerComentariosHijosMas(\''.$Padre->id_producto.'\' , \''.$Padre->id.'\');" id="vermashijo'. $Padre->id .'" class="button_full">Ver mas</a>';
                    }

                    $html .= '<div class="comm_reply"><img src="images/icons/white/message_black.png" onClick="ViewReplyPadre('.$Padre->id.');" alt="reply" title="reply" border="0" /></div>
                    <div class="contactform" id="replypadre'. $Padre->id .'" style="display:none">
                    <form id="agrega-comentario" name="agrega-comentario" method="POST" class="ajax-submit">
                    <textarea id="comentarioreply'. $Padre->id .'" name="comentarioreply'. $Padre->id .'" placeholder="Nuevo Comentario" class="form_textarea" rows="" cols="" required></textarea>
                    <input type="button" onClick="ReplyPadre('.$id_usuario.','.$Padre->id_producto.','. $Padre->id  .');" name="submitComentario" class="form_submit" id="submitComentario" value="Comentar" />
                    </form>
                    </div>                   
                    </li>
                    ';
                }                
            }
            $html .= '<div class="clear"></div>';
            echo $html;
        
        break; 



        case "InsertaComentarioReply":

                $Comentario = new Comentario();

                if (isset($_POST["id_usuario"]) && isset($_POST["id_producto"]) && isset($_POST["texto"]) && isset($_POST["id_padre"]) ) 
                {
                    $Comentario->id_usuario = $_POST["id_usuario"];
                    $Comentario->id_producto = $_POST["id_producto"];
                    $Comentario->texto = $_POST["texto"];
                    $Comentario->id_padre = $_POST["id_padre"];
                } 
                else 
                {
                    $Comentario->id_usuario = $_REQUEST["id_usuario"];
                    $Comentario->id_producto = $_REQUEST["id_producto"];
                    $Comentario->texto = $_REQUEST["texto"];
                    $Comentario->id_padre = $_POST["id_padre"];
                }

                $result = $Comentario->insertaComentariReplyPadre();

                echo $result;
            
            break; 

            case "ObtenerComentariosHijo":

                        $Padre = new Comentario();

                        $html = "";

                        if (isset($_POST["id_producto"]) && isset($_POST["limit"]) && isset($_POST["id_padre"])   ) 
                        {
                            $Padre->limit = $_POST["limit"];
                            $Padre->id_producto = $_POST["id_producto"];
                            $Padre->id = $_POST["id_padre"];
                        } 
                        else 
                        {
                            $Padre->limit = $_REQUEST["limit"];
                            $Padre->id_producto = $_REQUEST["id_producto"];
                            $Padre->id = $_REQUEST["id_padre"];
                        }

                        

                        $html .='<ul class="comments-hijo" id="comments-hijo'. $Padre->id .'">';
                        $resulthijos = $Padre->seleccionaComentariosHijos();
                        $contador_hijos = 0;
                            while ($row = mysqli_fetch_array($resulthijos))
                            {

                                $Hijo = new Comentario();
                                $Hijo->id = $row['id']; 
                                $Hijo->id_usuario = $row['id_usuario'];
                                $Hijo->id_producto = $row['id_producto'];
                                $Hijo->fecha = $row['fecha'];
                                $Hijo->texto = $row['comentario']; 
                                $Hijonombre = $row['nombre']; 
                                $Hijofoto = $row['foto']; 
                            

                                $html .='
                                <li>                                                    
                                        <div class="comm_avatar"><img src="'. $Hijofoto .'" alt="" title="" border="0" onClick="mostrarimagen(\''. $Hijofoto .'\')" /></div>
                                        <div class="comm_content"><p>'. $Hijo->fecha .'<br><a href="#">'. $Hijonombre .':  </a>'. $Hijo->texto .' </p>
                                </li>';
                                $contador_hijos++;
                            }

                        $html .='</ul>';
                        if($contador_hijos==4)
                        {
                            $html .='<a href="#" onclick="ObtenerComentariosHijosMas(\''.$Padre->id_producto.'\' , \''.$Padre->id.'\');" id="vermashijo'. $Padre->id .'" class="button_full">Ver mas</a>';
                        }     
                        echo $html;
                    
                    break; 

            case "ObtenerComentariosHijoMas":

                        $Padre = new Comentario();

                        $html = "";

                        if (isset($_POST["id_producto"]) && isset($_POST["limit"]) && isset($_POST["id_padre"])   ) 
                        {
                            $Padre->limit = $_POST["limit"];
                            $Padre->id_producto = $_POST["id_producto"];
                            $Padre->id = $_POST["id_padre"];
                        } 
                        else 
                        {
                            $Padre->limit = $_REQUEST["limit"];
                            $Padre->id_producto = $_REQUEST["id_producto"];
                            $Padre->id = $_REQUEST["id_padre"];
                        }

                        

                        $html .='<ul class="comments-hijo" id="comments-hijo'. $Padre->id .'">';
                        $resulthijos = $Padre->seleccionaComentariosHijos();
                        $contador_hijos = 0;
                            while ($row = mysqli_fetch_array($resulthijos))
                            {

                                $Hijo = new Comentario();
                                $Hijo->id = $row['id']; 
                                $Hijo->id_usuario = $row['id_usuario'];
                                $Hijo->id_producto = $row['id_producto'];
                                $Hijo->fecha = $row['fecha'];
                                $Hijo->texto = $row['comentario']; 
                                $Hijonombre = $row['nombre']; 
                                $Hijofoto = $row['foto']; 
                            

                                $html .='
                                <li>                                                    
                                        <div class="comm_avatar"><img src="'. $Hijofoto .'" alt="" title="" border="0" onClick="mostrarimagen(\''. $Hijofoto .'\')"/></div>
                                        <div class="comm_content"><p>'. $Hijo->fecha .'<br><a href="#">'. $Hijonombre .':  </a>'. $Hijo->texto .' </p>
                                </li>';
                                $contador_hijos++;
                            }

                        $html .='</ul>';
                        if($contador_hijos==4)
                        {
                            $html .='<a href="#" onclick="ObtenerComentariosHijosMas(\''.$Padre->id_producto.'\' , \''.$Padre->id.'\');" id="vermashijo'. $Padre->id .'" class="button_full">Ver mas</a>';
                        }     
                        echo $html;
                    
                    break;                    
        
        default :
        break;
}