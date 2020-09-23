let dbn = new Dexie("mzapcr_database");

let prefijonew = "";

let msg;

let usuario = {
    id: null,
    nombre: null,
    correo: null,
    foto: null,
    sexo: null,
    fecha_nacimiento: null,
    telefono: null,
    prefijo: null,
    productos: null,
    iso: null,
    estado: null,
    alertas: null,
    logged: null
};

// let imagenUP = '';

let limit = 0;
let id_producto_global = 0;
let limit_hijo = 0;
let id_padre_limit = 0;

// Variables para controlar el inicio y termino de la cuenta
let inicio = false;
let termino = false;

// Variables editables, para personalizar la barra de porcentaje
let tam_barra = 340;	// Tamaño de la barra en pixeles
let seg_barra = 50;		// Segundos de trabajo hasta 100%

// Se toma la diferencia con respecto el tiempo actual
let date = null;
let milisec_barra = seg_barra * 1000;
let milisec_final = null;

$(document).ready(ini);

// obtiene IP
$.getJSON('https://api.ipify.org?format=jsonp&callback=?', function(data) {
    miIP = data.ip;
    // console.log(data.ip);
});



function ini() {
    open_db();
    logged_user_ask();
}


function ObtienePaises() {
    myApp.showPreloader("Cargando países....");
    $.ajax(
        {
            async: true,
            type: "POST",
            url: "logica/logicaPaises.php",
            data: "action=ObtienePaises",
            beforSend: function (data) {
                myApp.showPreloader("Cargando....");
            },
            success: function (data) {

                $("#listapais").html(data);
                myApp.popup('.popup-forgot');
                //$("#paises").html(data);
                //setPrefijo();
                $("#Pais").prop('disabled', false);
                $("#Telefono").prop('disabled', false);
                myApp.hidePreloader();

            }
        });
}

function openselectpais() {
    ObtienePaises();
    myApp.popup('.popup-forgot');
}

function ValidaUsuario_old() {
    let telefono = $("#telefonoLogin").val();
    let codigo = $("#codigoLogin").val();

    if (telefono == "") {
        myApp.alert('Debe especificar su nombre de usuario.', 'Error');
        return
    } else if (codigo == "") {
        myApp.alert('Debe especificar el código recibido.', 'Error');
        return
    }

    let rsUsuario = null;
    $.ajax(
        {
            async: false,
            type: "POST",
            url: "logica/logicaUsuario.php",
            data: "action=ValidaUP&telefono=" + telefono + "&codigo=" + codigo,
            dataType: 'json',
            beforSend: function (data) {
                myApp.showPreloader("Validando...");
            },
            success: function (data) {
                myApp.hidePreloader();
                rsUsuario = data;
            }
        });
    if (rsUsuario == 0) {
        myApp.alert('Datos incorrectos!<br />Verifique e intente de nuevo.', 'Error');
        return
    } else {
        dbn.usuario.put({id: rsUsuario['id'], nombre: rsUsuario['nombre'], iso: rsUsuario['iso']});
        myApp.closeModal('.popup-login');
    }
}

function selectpais(prefijo, pais, bandera) {
    setPrefijo(prefijo, pais, bandera);
    myApp.closeModal('.popup-forgot');
    //myApp.popup('.popup-signup');
}

function setPrefijo(prefijo, pais, bandera) {
    //prefijo = $("#Pais").val();
    prefijonew = prefijo;
    $("#prefijo").html("+" + prefijo);
    $("#nombrepais").html(pais);
    document.getElementById('imagenpais').src = bandera;
}

function ObtieneISO(prefijo) {
    let iso = null;
    $.ajax(
        {
            async: false,
            type: "POST",
            url: "logica/logicaUsuario.php",
            data: "action=ObtieneISO&prefijo=" + prefijo,
            beforSend: function (data) {
                myApp.showPreloader("Cargando....");
            },
            success: function (data) {
                myApp.hidePreloader();
                //alert(data);
                iso = data;

            }
        });

    return iso;
}

function CreaUsuario() {
    let prefijo = prefijonew; //$("#prefijo").val();
    let telefono = $("#Telefono").val();
    let correo = $("#Email").val();
    telefono = telefono.trim();
    correo = correo.trim();

    let expresionRegular1 = /^([a-zA-Z0-9]+)$/;//<--- con esto vamos a validar el numero
    let expresionRegular2 = /\s/;//<--- con esto vamos a validar que no tenga espacios en blanco
    let expresionRegular3 = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    if (telefono == "") {
        // myApp.alert('Debe especificar su número de teléfono.', 'Error');
        myApp.alert('Debe especificar su usuario.', 'Error');
        return
    } else if (correo == "") {
        myApp.alert('Debe especificar su cuenta e-mail.', 'Error');
        return
    } else if (expresionRegular2.test(telefono)) {
        // myApp.alert('Número de teléfono no válido. Verifique espacios o caracteres no numéricos.', 'Error');
        myApp.alert('Usuario no válido.', 'Error');
        return
    } else if (!expresionRegular1.test(telefono)) {
        // myApp.alert('Número de teléfono inválido.', 'Error');
        myApp.alert('NUsuario inválido.', 'Error');
        return
    } else if (prefijo == "") {
        myApp.alert('Debe seleccionar el páis.', 'Error');
        return
    } else {
        // myApp.confirm('Son correctos estos datos?? <br> +' + prefijo + telefono + '<br>' + correo + ' ', 'Se enviará un código para verificar su cuenta.', function () {
        myApp.confirm('Son correctos estos datos? <br> ' +  correo + ' ', 'Se enviará un código para verificar su cuenta.', function () {
            $.ajax(
                {
                    async: true,
                    type: "POST",
                    url: "logica/logicaUsuario.php",
                    data: "action=Registro&prefijo=" + prefijo + "&telefono=" + telefono + "&correo=" + correo,
                    beforSend: function (data) {
                        myApp.showPreloader("Cargando....");
                    },
                    success: function (data) {
                        if (data == 1) {
                            //alert(data);
                            pais = $("#nombrepais").html();
                            bandera = document.getElementById('imagenpais').src;

                            dbn.preregistro.put({prefijo: prefijo, telefono: telefono, pais: pais, bandera: bandera, correo: correo});

                            //sql = 'INSERT INTO PreRegistroDB (prefijo, telefono,pais,bandera) VALUES ("' + prefijo + '", "' + telefono + '", "' + pais + '", "' + bandera + '")';
                            //query(sql);

                            $("#Pais").prop('disabled', true);
                            $("#Telefono").prop('disabled', true);
                            //$("#Telefono").attr('disabled','disabled');
                            $("#codigo_verificacion").css("display", "block");
                            $("#enviar").css("display", "none");
                            myApp.hidePreloader();
                            iniciar_proceso();
                            //window.setTimeout("iniciar_proceso()", 300);
                            //myApp.popup('.popup-account');
                            myApp.confirm("Desea colaborar contestando una encuesta?", "Encuesta", function () {
                                myApp.popup('.popup-encuesta');
                            }, function () {
                                // alert("Cancelar")
                            });
                        } else {
                            // alert(data);
                            myApp.hidePreloader();
                        }
                    }
                });
        });
    }

}

function solicitar_codigo_nuevo() {

    prefijo = prefijonew; //$("#prefijo").val();
    telefono = $("#Telefono").val();
    telefono = telefono.trim();

    myApp.confirm('<br> Este es su numero ?  +' + prefijo + ' ' + telefono + ' ', 'Solicitar el Codigo de nuevo ? ', function () {
        $.ajax(
            {
                async: true,
                type: "POST",
                url: "logica/logicaUsuario.php",
                data: "action=ReCodigo&prefijo=" + prefijo + "&telefono=" + telefono,
                beforSend: function (data) {
                    myApp.showPreloader("Cargando....");
                },
                success: function (data) {
                    if (data == 1) {
                        $("#Pais").prop('disabled', true);
                        $("#Telefono").prop('disabled', true);
                        myApp.hidePreloader();
                        iniciar_proceso();
                        //window.setTimeout("iniciar_proceso()", 300);
                        //myApp.popup('.popup-account');
                    } else {
                        alert(data);
                        myApp.hidePreloader();
                    }
                }
            });
    });

}

function VerificaUsuario() {
    let codigo = $("#codigo").val();
    let prefijo = prefijonew;
    let telefono = $("#Telefono").val();
    let correo = $("#Email").val();
    let iso = ObtieneISO(prefijo);
    //alert(iso);

    if (codigo != "") {

        $.ajax(
            {
                async: true,
                type: "POST",
                url: "logica/logicaUsuario.php",
                data: "action=VerificaUsuario&codigo=" + codigo + "&telefono=" + telefono + "&prefijo=" + prefijo + "&iso=" + iso,
                beforSend: function (data) {
                    myApp.showPreloader("Cargando....");
                },
                success: function (data) {
                    if (data != 0) {
                        let id = data;

                        dbn.usuario.put({id: id, prefijo: prefijo, telefono: telefono, iso: iso});

                        seleciona_usuario(telefono);
                        myApp.popup('.popup-register');
                        myApp.hidePreloader();

                    } else {
                        alert("Codigo incorrecto !");
                        myApp.hidePreloader();
                    }
                }
            });

    } else {
        $("#alerta_codigo").html("<span SIZE=5 COLOR=red>* Campo Obligatorio</span>");
        setTimeout("$('#alerta_codigo').html('');  ", 3000);
    }
}

function GuardaInfoUsuario() {


    let nombre = $("#nombre").val();
    let correo = $("#correo").val();
    let fecha_nacimiento = $("#fecha").val();
    let sexo = $("#sexo").val();
    let telefono = usuario.telefono;
    let prefijo = usuario.prefijo;
    let foto = "";

    correo = ''; //correo.trim();
    nombre = ''; //nombre.trim();

    let exprcorreo = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        //myApp.showPreloader("Cargando....");
        $.ajax(
            {

                async: true,
                type: "POST",
                url: "logica/logicaUsuario.php",
                data: "action=InfoUsuario&foto=" + foto + "&nombre=" + nombre.trim() + "&correo=" + correo.trim() + "&fecha_nacimiento=" + fecha_nacimiento + "&sexo=" + sexo + "&telefono=" + telefono + "&prefijo=" + prefijo,
                beforSend: function (data) {
                    myApp.showPreloader("Cargando....");
                },
                success: function (data) {
                    if (data == 1) {
                        myApp.closeModal();
                        myApp.popup('.popup-cuestionario');
                        usuario.nombre = nombre;
                        usuario.foto = foto;
                        usuario.fecha_nacimiento = fecha_nacimiento;
                        usuario.sexo = sexo;
                        usuario.correo = correo;
                        $("#user_name").html("<p>Hola, <span>" + usuario.nombre + "</span></p>");

                    } else {
                        //alert("No se...."+data);
                        myApp.hidePreloader();
                    }
                }
            });

}

function OpenDB_old() {
    //
    // Define your database
    //
    dbn.version(1).stores({
        // usuario: 'id,prefijo,telefono,contrasena,correo,nombre,foto,sexo,fecha_nacimiento,iso,estado'
        usuario: 'id',
        preregistro: 'telefono',
    });
    dbn.open();

}

function CreateTable(tabla, succesCBK) {
    if (this.DB === null) {
        this.msg = 'Para crear una tabla es necesario abrir primero una Base de datos';
        alert(this.msg);
    } else {
        //en query se guardara la consulta
        let query = 'CREATE TABLE IF NOT EXISTS ' + tabla.name + '(';

        //con este for construyo la query

        let long = tabla.colum.length;
        for (let i = 0; i < long; i++) {
            if (i !== 0)
                query += ',';
            query += tabla.colum[i] + ' ' + tabla.dataType[i] + ' ';
            if (tabla.isNotNull[i])
                query += ' NOT NULL ';
            if (tabla.primaryKey[i])
                query += ' PRIMARY KEY ';
            if (tabla.unike[i])
                query += ' UNIQUE ';
            if (tabla.autoincrement[i])
                query += ' AUTOINCREMENT ';
            if (tabla.defaultValues[i].length !== 0) {
                if (tabla.dataType[i] === 'integer' | tabla.dataType[i] === 'INTEGER' | tabla.dataType[i] === 'BOOLEAN' | tabla.dataType[i] === 'boolean') {
                    query += ' DEFAULT ' + tabla.defaultValues[i];
                } else
                    query += ' DEFAULT "' + tabla.defaultValues[i] + '"';
            }
        }
        query += ');';
    }
    querytabla(query, [], succesCBK);
}

function querytabla(consulta, array, succesCBK, errKB) {
    if (array === null)
        array = [];
    if (succesCBK === null)
        succesCBK = function (results) {
            console.log('succes');
            alert('succes');
        };
    if (errKB === null)
        errKB = function (results) {
            alert('Eror inesperado; codigo del error: ' + results.message);
        };
    db.transaction(function (transaction) {
        transaction.executeSql((consulta),
            array,
            function (transaction, results) {
                succesCBK(results);
            },
            function (transaction, results) {
                errKB(results);
            }
        );
    });
}

function query(sql) {
    query1(sql, [], function () {
        //alert(' Datos insertados correctamente');
    }, function (e) {
        alert(e.message);
    });
}

function query1(consulta, array, succesCBK, errCBK) {
    db.transaction(function (transaction) {
        transaction.executeSql((consulta),
            array,
            function (transaction, results) {
                succesCBK(results);
            },
            function (transaction, results) {
                errCBK(results);
            }
        );
    });
}

function elimina_tabla(tabla) {
    sql = "DROP TABLE " + tabla;
    query(sql);
}

function seleciona_usuario(telefono) {
    dbn.preregistro.get(telefono).then(function (rsuser) {
        usuario.id = rsuser.id;
        usuario.telefono = rsuser.telefono;
        usuario.prefijo = rsuser.prefijo;
        usuario.correo = rsuser.correo;
    });



}

function obtieneCantidadProductos(idusuario) {

    if (idusuario != undefined) {
        //alert(idusuario);
        myApp.showPreloader("Cargando....");
        $.ajax(
            {
                async: true,
                type: "POST",
                url: "logica/logicaUsuario.php",
                data: "action=CantidadMyProducts&idusuario=" + idusuario,
                success: function (data) {
                    //alert(data);
                    myApp.hidePreloader();
                    usuario.productos = data;
                    $("#cantidadmyproducts").html(data);

                }
            });

    } else {
        //alert("error");
    }
}

function obtieneCantidadAlertas(idusuario) {

    if (idusuario != undefined) {
        //alert(idusuario);
        //myApp.showPreloader("Cargando....");
        $.ajax(
            {
                async: true,
                type: "POST",
                url: "logica/logicaUsuario.php",
                data: "action=CantidadAlertas&idusuario=" + idusuario,
                success: function (data) {
                    //alert("Notificaciones "+data);
                    //myApp.hidePreloader();
                    usuario.alertas = data;
                    $("#cantidadalertas").html(data);

                }
            });

    } else {
        alert("error");
    }
}

function seleciona_cuenta_old() {
    //myApp.showPreloader("Cargando selecciona cuenta....");

    dbn.usuario.reverse().last(function (wtf) {
        if (typeof wtf === 'undefined') {
            // usuario no ha ingresado
            myApp.popup('.popup-login');
        } else {
            // usuario ya ha ingresado
            myApp.alert('Hola ' + wtf.nombre + '!', 'Bienvenido!');
        }
    });


}

function carga_cuenta() {
    myApp.showPreloader("Cargando ...");
    db.transaction(function (tx) {
        sql = 'SELECT * FROM UsuarioDB';
        //alert(sql);
        tx.executeSql(sql, [], function (tx, results) {
            let len = results.rows.length, i;
            msg = "<p>Found rows: " + len + "</p>";

            if (len > 0) {
                //alert(msg);

                for (i = 0; i < len; i++) {
                    usuario.id = results.rows.item(i).id;
                    usuario.telefono = results.rows.item(i).telefono;
                    usuario.prefijo = results.rows.item(i).prefijo;
                    usuario.nombre = results.rows.item(i).nombre;
                    usuario.foto = results.rows.item(i).foto;
                    usuario.fecha_nacimiento = results.rows.item(i).fecha_nacimiento;
                    usuario.sexo = results.rows.item(i).sexo;
                    usuario.correo = results.rows.item(i).correo;
                    usuario.iso = results.rows.item(i).iso;
                    //alert("Usuario id "+usuario.id+ " telefono"+usuario.telefono+" prefijo "+usuario.prefijo);
                    $("#nombre").val(usuario.nombre);
                    $("#correo").val(usuario.correo);
                    $("#fecha").val(usuario.fecha_nacimiento);
                    $("#sexo").val(usuario.sexo);
                    //document.getElementById('photo').src = usuario.foto;
                    myApp.popup('.popup-login');
                    myApp.hidePreloader();
                    //alert(msg);
                }
            } else {
                //ObtienePaises();
                myApp.popup('.popup-signup');
                myApp.hidePreloader();
            }

        }, null);

    });
}

function carga_preregistro() {
    myApp.showPreloader("Cargando ...");
    db.transaction(function (tx) {
        sql = 'SELECT * FROM PreRegistroDB';
        //alert(sql);
        tx.executeSql(sql, [], function (tx, results) {
            let len = results.rows.length, i;
            msg = "<p>Found rows: " + len + "</p>";

            if (len > 0) {
                //alert(msg);

                for (i = 0; i < len; i++) {
                    prefijonew = results.rows.item(i).prefijo;
                    telefono = results.rows.item(i).telefono;
                    $("#prefijo").html("+" + prefijonew);
                    $("#nombrepais").html(results.rows.item(i).pais);
                    document.getElementById('imagenpais').src = results.rows.item(i).bandera;
                    $("#Telefono").val(telefono);
                    $("#Pais").prop('disabled', true);
                    $("#Telefono").prop('disabled', true);
                    $("#codigo_verificacion").css("display", "block");
                    $("#enviar").css("display", "none");
                    myApp.hidePreloader();
                    myApp.popup('.popup-signup');
                    iniciar_proceso();
                    //alert(msg);
                }
            } else {
                //ObtienePaises();
                myApp.popup('.popup-signup');
                myApp.hidePreloader();
            }

        }, null);

    });
}

// Funcion que inicia el proceso
function iniciar_proceso() {
    $("#divreintentar").css("display", "none");
    $("#barra").css("display", "block");
    tam_barra = $("#barra").width();
    date = new Date();
    milisec_final = date.getTime() + milisec_barra;
    inicio = false;
    termino = false;
    // Solo si no a iniciado el proceso se inicia
    if (inicio == false) {
        inicio = true;
        aumenta_barra();
    }
}

// Funcion que aumenta el porcentaje de la barra
function aumenta_barra() {
    // Solo si no a terminado sigue aumentando
    if (termino == false) {
        // Se toma el tiempo actual
        let ahora = new Date();
        milisec_ahora = ahora.getTime();

        // Se toma el tiempo restante para llegar a 100%
        let milisec_restante = Math.ceil((milisec_final - milisec_ahora) / 100);
        if (milisec_restante < 0) {
            milisec_restante = 0;
        }

        // Se divide el tiempo restante en horas, minutos y segundos
        let horas = Math.floor(milisec_restante / 36000);
        let minutos = Math.floor(milisec_restante % 36000 / 600);
        let segundos = Math.floor(milisec_restante % 36000 % 600) / 10;
        if ((segundos % 1) == 0) {
            segundos = segundos + ".0";
        }
        let salida;
        if (horas > 0) {
            let salida = "Reintentar en " + horas + " horas, " + minutos + " minutos y " + segundos + " segundos.";
        } else {
            if (minutos > 0) {
                let salida = "Reintentar en " + minutos + " minuto(s) y " + segundos + " segundos.";
            } else {
                let salida = "Reintentar en " + segundos + " segundos.";
            }
        }

        // Se genera el porcentaje a partir del tiempo restante para el 100%
        milisec_restante = Math.floor(milisec_restante) / 10;
        let porcentaje = Math.floor(((milisec_barra - milisec_restante * 1000) / milisec_barra) * 100);
        if (porcentaje < 0) {
            porcentaje = 1;
        }

        // Se verifica si se llego al tiempo final
        if (milisec_final >= milisec_ahora) {
            // Si aun no termina solo se muestra el porcentaje
            document.getElementById("div_boton").innerHTML = salida;
            document.getElementById("div_barra").innerHTML = porcentaje + "%";
        } else {
            // Si termina se puede continuar mostrando o enviando alguna informacion
            termino = true;
            /*document.forma.submit();*/ // Mandar que termino el trabajo para permitir continuar
            document.getElementById("div_barra").innerHTML = "100% listo";
            $("#barra").css("display", "none");
            $("#divreintentar").css("display", "block");
            $("#Pais").prop('disabled', false);
            $("#Telefono").prop('disabled', false);
        }
        document.getElementById("div_completado").style.width = (porcentaje / 100) * tam_barra + "px";
        setTimeout("aumenta_barra();", 100);
    }
}

