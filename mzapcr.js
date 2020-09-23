// myApp.showPreloader("Cargando selecciona cuenta....");
// myApp.hidePreloader();

/*
 * Verifica si el usuario ya ha ingresado
 */
function logged_user_ask() {
    /*if (usuario.id === null) {
        myApp.popup('.popup-login');
    } else {
        document.getElementById('user_photo').src = rsUsuario['foto'];
        document.getElementById("user_name").innerHTML = '<p>Saludos,<br /><span>' + rsUsuario['nombre'] + "!</span></p>";
    }
    */
    // lee el último dato de la tabla de usuarios
    dbn.usuario.reverse().last(function (rsUsuario) {
        if (typeof rsUsuario === 'undefined') {
            // el usuario no ha ingresado
            myApp.popup('.popup-login');
            return false;
        } else {
            // el usuario ya ha ingresado
            myApp.alert('Hola ' + rsUsuario.nombre + '!<br />Su último ingreso fue el ' + rsUsuario.ingreso + ' (GMT -06:00).', 'Bienvenido');
            dbn.usuario.update(rsUsuario.id, {ingreso: calcTime(-6.0)});
            usuario.logged = true;
            usuario.id = rsUsuario.id;
            usuario.nombre = rsUsuario.nombre;
            usuario.iso = rsUsuario.iso;
            usuario.foto = rsUsuario.foto;
            document.getElementById('user_photo').src = rsUsuario['foto'];
            document.getElementById("user_name").innerHTML = '<p>Saludos,<br /><span>' + rsUsuario['nombre'] + "!</span></p>";
            myApp.confirm("Desea colaborar contestando una encuesta?", "Encuesta", function () {
                myApp.popup('.popup-encuesta');
            }, function () {
                // alert("Cancelar")
            });
        }
    });
}

/*
 * Crea/abre la base de datos local
 */
function open_db() {
    // define la base de datos
    dbn.version(1).stores({
        // usuario: 'id,prefijo,telefono,contrasena,correo,nombre,foto,sexo,fecha_nacimiento,iso,estado'
        usuario: 'id',
        preregistro: 'telefono',
    });
    dbn.open();
}

/*
 * Calcula la fecha y hora según el UTC offset
 * url: https://www.techrepublic.com/article/convert-the-local-time-to-another-time-zone-with-this-javascript/
 */
function calcTime(offset) {

    // create Date object for current location
    let d = new Date();

    // convert to msec
    // add local time zone offset
    // get UTC time in msec
    let utc = d.getTime() + (d.getTimezoneOffset() * 60000);

    // create new Date object for different city
    // using supplied offset
    let nd = new Date(utc + (3600000 * offset));

    // return time as a string
    return nd.toLocaleDateString('fr-CA') + " " + nd.toLocaleTimeString();
}



/*
 * Verifica login usuario
 */
function valida_login() {
    let telefono = $("#telefonoLogin").val();
    let codigo = $("#codigoLogin").val();

    if (telefono == "") {
        myApp.alert('Debe especificar su nombre de usuario.', 'Error');
        return
    } else if (codigo == "") {
        myApp.alert('Debe especificar el código recibido.', 'Error');
        return
    }

    myApp.showPreloader('Validando...');
    const usuarioGet = "logica/logicaUsuario.php?action=ValidaUP&telefono=" + telefono + "&codigo=" + codigo;
    const usuarioRequest = request(usuarioGet);
    usuarioRequest
        .then(function (rs) {
            myApp.hidePreloader();
            let rsUsuario = JSON.parse(rs);
            // console.log(rsUsuario);
            // console.log(rsUsuario[0]['id']);
            if (rsUsuario == 0) {
                myApp.alert('Datos incorrecto!<br />Verifique e intente de nuevo.', 'Error');
                return
            } else {
                dbn.usuario.put({
                    id: rsUsuario[0]['id'],
                    nombre: rsUsuario[0]['nombre'],
                    foto: rsUsuario[0]['foto'],
                    iso: rsUsuario[0]['iso'],
                    ingreso: calcTime(-6.0)
                });
                myApp.closeModal('.popup-login');
                document.getElementById('user_photo').src = rsUsuario[0]['foto'];
                document.getElementById("user_name").innerHTML = '<p>Saludos,<br /><span>' + rsUsuario[0]['nombre'] + "!</span></p>";
                //myApp.alert('Hola ' + rsUsuario[0]['nombre'] + '!<br />Su último ingreso fue el ' + calcTime(-6.0) + ' (GMT -06:00).', 'Bienvenido');
                myApp.alert('Hola ' + rsUsuario[0]['nombre'] + '!', 'Bienvenido');
                usuario.logged = true;
                myApp.confirm("Desea colaborar contestando una encuesta?", "Encuesta", function () {
                    //alert("Cancelar")
                    myApp.popup('.popup-encuesta');
                }, function () {
                    //alert("Confirmar")
                    //window.location.href = "index.html";
                });
            }
        })
        .catch(handleErrors)

}

/*
 * Valida si el usuario no/puede agregar eventos
 */
function valida_evento_agregar2() {
    if (usuario.logged == null) {
        myApp.alert('Para agregar eventos debe ingresar al aplicativo.', 'Alerta');
        myApp.popup('.popup-login');
    } else {
        window.location.href = "evento_agregar.html";
    }
}

/*
 * Valida si el usuario no/puede agregar eventos
 */
function valida_evento_agregar() {
    if (usuario.logged == null) {
        myApp.alert('Para agregar eventos debe ingresar al aplicativo.', 'Alerta');
        myApp.popup('.popup-login');
    } else {
        window.location.href = "evento_agregar.html";
    }
}

// function obtiene_eventos_mapa(pais) {
function obtiene_eventos_mapa() {

    $.ajax({
        async: false,
        type: "POST",
        url: "logica/logica-evento.php",
        data: "action=mapMarkers",
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando...');
        },
        success: function (data) {
            myApp.hidePreloader();
            // console.log("cuanto: " + data);
            //console.log(markers);
            return data;
        }
    });
}

/*** test ***/

function request(url) {
    return new Promise(function (resolve, reject) {
        const xhr = new XMLHttpRequest();
        xhr.timeout = 10000;
        xhr.onreadystatechange = function (e) {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    resolve(xhr.response)
                } else {
                    reject(xhr.status)
                }
            }
        }
        xhr.ontimeout = function () {
            reject('timeout')
        }
        xhr.open('post', url, true)
        xhr.send();
    })
}

function handleRS(rs) {
    // console.log(users);
    // return JSON.parse(users).name

    var markers = JSON.parse(rs);


    //console.log(markers);

    return markers;

}

function repoRequest(rs) {
    console.log(rs);
    return Promise.all(rs.map(function (user) {
        return request(user.repos_url)
    }))
}

function handleReposList(repos) {
    console.log('All users repos in an array', repos)
}

function handleErrors(error) {
    console.error('Something went wrong ', error)
    myApp.alert('Ha surgido un error inesperado. Por favor, esperar un momento e intentar de nuevo.', 'Alerta');
}





