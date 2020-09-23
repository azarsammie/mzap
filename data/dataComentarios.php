<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));


require_once ('conexion.php');

class Comentario 
{
        public  $id;
        public  $id_producto;
        public  $id_usuario;
        public  $texto;
        public  $conexion;
        public  $limit;
        public  $fecha;
        public  $id_padre;
        
        function __construct()
        {
            $this->conexion = new Conexion();
        }

        function insertaComentarioPadre()
        {
            $sql = "INSERT INTO mzapcr_comentarios (id_usuario,id_producto,fecha,comentario) 
            values ($this->id_usuario,$this->id_producto,NOW(),'$this->texto')";
            //echo $sql;
            $result = $this->conexion->consulta($sql);
            $this->id = mysqli_insert_id();
            $this->insertaNotificacionComentarioPadre($this->id);
            if ($result==0) 
            {
                return 0;
            }
            return $this->id;
        }

        function insertaNotificacionComentarioPadre($id_comentario)
        {

            $id_usuario_producto =0;
            $id_producto = 0;
            $id_usuario_comentario = 0;

            $sql = "SELECT
            mzapcr_eventos.id,
            mzapcr_eventos.id_usuario
            FROM
            mzapcr_eventos
            WHERE
            mzapcr_eventos.id = $this->id_producto";

            $result = $this->conexion->consulta($sql);
            $numero_filas = mysqli_num_rows($result);
            if ($numero_filas==0) 
            {
                //return 0;
            }
            else
            {
                while ($row = mysqli_fetch_array($result))
                {
                    $id_usuario_producto = $row["id_usuario"];
                    $id_producto = $row["id"];
                    $id_usuario_comentario = $this->id_usuario;
                }

                $sql = "insert into mzapcr_notificacion_comentario(id_usuario_producto,id_producto,id_comentario,id_usuario_comentario)
                values ($id_usuario_producto , $id_producto , $id_comentario , $id_usuario_comentario )";

                $result = $this->conexion->consulta($sql);
                $this->id = mysqli_insert_id();
                if ($result==0) 
                {
                    //return 0;
                }
            }

        }


        function insertaNotificacionComentarioHijo()
        {

            $sql = "SELECT
            mzapcr_comentarios.id as id_padre,
            mzapcr_comentarios.id_producto,
            mzapcr_comentarios.id_usuario as id_usuario_padre
            FROM
            mzapcr_comentarios
            WHERE
            mzapcr_comentarios.id = $this->id_padre";

            $result = $this->conexion->consulta($sql);
            $numero_filas = mysqli_num_rows($result);
            if ($numero_filas==0) 
            {
                //return 0;
            }
            else
            {
                while ($row = mysqli_fetch_array($result))
                {
                    $id_padre = $row["id_padre"];
                    $id_producto = $row["id_producto"];
                    $id_usuario_padre = $row["id_usuario_padre"];
                }

                $sql = "INSERT INTO mzapcr_notificacion_respuestas(id_usuario_padre,id_padre,id_hijo,id_usuario_hijo,id_producto)
                values($id_usuario_padre,$id_padre,$this->id,$this->id_usuario,$this->id_producto)";
                //echo  $sql; 
                $result = $this->conexion->consulta($sql);
                $id_notificacion = mysqli_insert_id();
                if ($result==0) 
                {
                    //return 0;
                }
                else
                {
                    $this->insertaNotificacionRelacionComentarioHijo($id_notificacion,$this->id_usuario,$id_producto);
                }
            }

        }                   

        function seleccionaComentariosPadre()
        {
            $sql = "SELECT
            mzapcr_comentarios.id,
            mzapcr_comentarios.id_usuario,
            mzapcr_comentarios.id_producto,
            mzapcr_comentarios.fecha,
            mzapcr_comentarios.comentario,
            mzapcr_usuario.nombre,
            mzapcr_usuario.foto
            FROM
            mzapcr_comentarios
            INNER JOIN mzapcr_usuario ON mzapcr_comentarios.id_usuario = mzapcr_usuario.id
            WHERE
            mzapcr_comentarios.id_producto = $this->id_producto
            and mzapcr_comentarios.id not in (SELECT
            mzapcr_relacion_comentarios.id_hijo
            FROM
            mzapcr_relacion_comentarios)
            ORDER BY fecha DESC
            LIMIT $this->limit , 4";
            //echo $sql;
            $result = $this->conexion->consulta($sql);
            $numero_filas = mysqli_num_rows($result);
            if ($numero_filas==0) 
            {
                return 0;
            }
            else
            {
                return $result;
            }
        }  

        function seleccionaComentariosPadreNotificacion()
        {

            $sql = "SELECT
                mzapcr_comentarios.id,
                mzapcr_comentarios.id_usuario,
                mzapcr_comentarios.id_producto,
                mzapcr_comentarios.fecha,
                mzapcr_comentarios.comentario,
                mzapcr_usuario.nombre,
                mzapcr_usuario.foto
                FROM
                mzapcr_comentarios
                INNER JOIN mzapcr_usuario ON mzapcr_comentarios.id_usuario = mzapcr_usuario.id
                WHERE
                mzapcr_comentarios.id_producto = $this->id_producto
                and mzapcr_comentarios.id  in (SELECT
            mzapcr_notificacion_comentario.id_comentario
            FROM
            mzapcr_notificacion_comentario
            WHERE
            mzapcr_notificacion_comentario.id_producto = $this->id_producto AND
            mzapcr_notificacion_comentario.estado = 0)
                ORDER BY fecha DESC";
            //echo $sql;
            $result = $this->conexion->consulta($sql);
            $numero_filas = mysqli_num_rows($result);
            if ($numero_filas==0) 
            {
                return 0;
            }
            else
            {
                return $result;
            }
        } 


        function seleccionaComentariosPadreNotificacionRespuesta($id_comentario)
        {


            $sql = "SELECT
                mzapcr_comentarios.id,
                mzapcr_comentarios.id_usuario,
                mzapcr_comentarios.id_producto,
                mzapcr_comentarios.fecha,
                mzapcr_comentarios.comentario,
                mzapcr_usuario.nombre,
                mzapcr_usuario.foto
                FROM
                mzapcr_comentarios
                INNER JOIN mzapcr_usuario ON mzapcr_comentarios.id_usuario = mzapcr_usuario.id
                WHERE
                mzapcr_comentarios.id_producto = $this->id_producto
                and mzapcr_comentarios.id  in (SELECT
            mzapcr_notificacion_comentario.id_comentario
            FROM
            mzapcr_notificacion_comentario
            WHERE
            mzapcr_notificacion_comentario.id_producto = $this->id_producto AND
                        mzapcr_comentarios.id = $id_comentario )
                ORDER BY fecha DESC";
            //echo $sql;
            $result = $this->conexion->consulta($sql);
            $numero_filas = mysqli_num_rows($result);
            if ($numero_filas==0) 
            {
                return 0;
            }
            else
            {
                return $result;
            }
        } 




        function seleccionaComentariosPadreTodos()
        {
            $sql = "SELECT
            mzapcr_comentarios.id,
            mzapcr_comentarios.id_usuario,
            mzapcr_comentarios.id_producto,
            mzapcr_comentarios.fecha,
            mzapcr_comentarios.comentario,
            mzapcr_usuario.nombre,
            mzapcr_usuario.foto
            FROM
            mzapcr_comentarios
            INNER JOIN mzapcr_usuario ON mzapcr_comentarios.id_usuario = mzapcr_usuario.id
            WHERE
            mzapcr_comentarios.id_producto = $this->id_producto
            and mzapcr_comentarios.id not in (SELECT
            mzapcr_relacion_comentarios.id_hijo
            FROM
            mzapcr_relacion_comentarios)
            ORDER BY fecha DESC";
            //LIMIT $this->limit , 4";
            //echo $sql;
            $result = $this->conexion->consulta($sql);
            $numero_filas = mysqli_num_rows($result);
            if ($numero_filas==0) 
            {
                return 0;
            }
            else
            {
                return $result;
            }
        } 
        function seleccionaComentariosHijos()
        {
            $sql = "SELECT
            mzapcr_comentarios.id,
            mzapcr_comentarios.id_usuario,
            mzapcr_comentarios.id_producto,
            mzapcr_comentarios.fecha,
            mzapcr_comentarios.comentario,
            mzapcr_usuario.nombre,
            mzapcr_usuario.foto
            FROM
            mzapcr_relacion_comentarios
            INNER JOIN mzapcr_comentarios ON mzapcr_relacion_comentarios.id_hijo = mzapcr_comentarios.id
            INNER JOIN mzapcr_usuario ON mzapcr_comentarios.id_usuario = mzapcr_usuario.id
            WHERE
            mzapcr_relacion_comentarios.id_padre = $this->id
            ORDER BY mzapcr_comentarios.fecha ASC
            LIMIT $this->limit , 4";
            //echo $sql;
            $result = $this->conexion->consulta($sql);
            $numero_filas = mysqli_num_rows($result);
            if ($numero_filas==0) 
            {
                return 0;
            }
            else
            {
                return $result;
            }
        }         

        function insertaComentariReplyPadre()
        {
            $sql = "INSERT INTO mzapcr_comentarios (id_usuario,id_producto,fecha,comentario) 
            values ($this->id_usuario,$this->id_producto,NOW(),'$this->texto')";
            //echo $sql;
            $result = $this->conexion->consulta($sql);
            $this->id = mysqli_insert_id();
            if ($result==0) 
            {
                return 0;
            }
            else
            {
                $sql = "INSERT INTO mzapcr_relacion_comentarios (id_padre,id_hijo) values($this->id_padre,$this->id)";
                //echo $sql;
                $result = $this->conexion->consulta($sql);
                if ($result==0) 
                {
                    return 0;
                }
            }
            $this->insertaNotificacionComentarioHijo();
            return $this->id;
        } 


        function insertaNotificacionRelacionComentarioHijo($id_notificacion,$id_usuario,$id_producto)
        {

            $sql = "SELECT DISTINCT
            mzapcr_notificacion_respuestas.id_usuario_hijo
            FROM
            mzapcr_notificacion_respuestas
            WHERE
            mzapcr_notificacion_respuestas.id_padre = $this->id_padre AND
            mzapcr_notificacion_respuestas.id_usuario_hijo <> $id_usuario";

            $result = $this->conexion->consulta($sql);
            $numero_filas = mysqli_num_rows($result);
            if ($numero_filas==0) 
            {
                //return 0;
            }
            else
            {
                while ($row = mysqli_fetch_array($result))
                {
                    $id_usuario = $row["id_usuario_hijo"];

                    $sql2 = "INSERT INTO mzapcr_relacion_notificacion(id_notificacion,id_usuario,id_producto)
                    values($id_notificacion,$id_usuario,$id_producto)";
                    //echo  $sql; 
                    $result2 = $this->conexion->consulta($sql2);

                }


            }

        }  


}