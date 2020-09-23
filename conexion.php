<?php
error_reporting(0);


class Conexion {
    /* variables de conexión */

    var $BaseDatos = "metales_mzapcr";
    var $Servidor = "mysql.s501.sureserver.com";
    var $Usuario = "mzapcr";
    var $Clave = "Holamzapcr22";


    /* identificador de conexión y consulta */
    var $Conexion_ID = 0;
    var $Consulta_ID = 0;
    /* número de error y texto error */
    var $Errno = 0;
    var $Error = "";
    
    /* Método Constructor: Cada vez que creemos una variable
      de esta clase, se ejecutará esta función */
    function __construct() {
        $this->Conexion_ID = mysqli_connect($this->Servidor, $this->Usuario, $this->Clave);
        if (!$this->Conexion_ID) {
            $this->Error = "Ha fallado la conexión.";
            return 0;
        }
        //seleccionamos la base de datos
        if (!@mysqli_select_db($this->Conexion_ID, $this->BaseDatos)) {
            $this->Error = "Imposible abrir " . $this->BaseDatos;
            return 0;
        }
        /* Si hemos tenido éxito conectando devuelve 
          el identificador de la conexión, sino devuelve 0 */
        return $this->Conexion_ID;
    }

    /* Ejecuta un consulta */
    function consulta($sql) {
        //ejecutamos la consulta
        $this->Consulta_ID = @mysqli_query($this->Conexion_ID, $sql);
        $res = $this->Consulta_ID;
        $this->Errno = mysqli_errno();
        $this->Error = mysqli_error();
        echo $this->Error;
        /* Si hemos tenido éxito en la consulta devuelve 
          el identificador de la conexión, sino devuelve 0 */
        return ($res != 0) ? $this->Consulta_ID : 0;
    }

    /* Devuelve el número de campos de una consulta */
    function numcampos() {
        return mysqli_num_fields($this->Consulta_ID);
    }

    /* Devuelve el número de registros de una consulta */
    function numregistros() {
        return mysqli_num_rows($this->Consulta_ID);
    }

    /* Devuelve el nombre de un campo de una consulta */
    function nombrecampo($numcampo) {
        return mysqli_field_name($this->Consulta_ID, $numcampo);
    }

}

?>
