// Initialize your app
let myApp = new Framework7({
    animateNavBackIcon: true,
    // Enable templates auto precompilation
    precompileTemplates: true,
    // Enabled pages rendering using Template7
    swipeBackPage: false,
    swipeBackPageThreshold: 1,
    swipePanel: "left",
    swipePanelCloseOpposite: true,
    pushState: true,
    pushStateRoot: undefined,
    pushStateNoAnimation: false,
    pushStateSeparator: '#!/',
    // init: false,
    template7Pages: true
});

let miIP = ""; //

let miCountry = ""; //
let milatitude = ""; //
let milongitude = ""; //
let categoria_global = ""; //


// Export selectors engine
let $$ = Dom7;

// Add main View
let mainView = myApp.addView('.view-main', {
    // Enable dynamic Navbar
    dynamicNavbar: false
});

//Calendario
let calendarDefault = myApp.calendar({
    input: '#fecha',
    closeOnSelect: true,
    touchmove: true,
    yearPicker: true,
});

$$(document).on('pageInit', function (e) {
    $(".swipebox").swipebox();
    // usuario.iso = usuario.iso;
    let page = e.detail.page;

    if (page.name == 'productos') { // Pagina Productos
        id_categoria = page.query.id_cat;
        //alert(usuario.iso);
        if (id_categoria == undefined) {
            id_categoria = 1;
        }
        ObtieneEvento(id_categoria, usuario.iso);

        page.view.router.back({
            url: "categorias.html",
            force: true,
            ignoreCache: true
        });
        mainView.router.loadContent(".nav_right_like")
        categoria_global = id_categoria;
        document.getElementById("link_agregar").href = "evento_agregar.html?id_cat=" + id_categoria;
    }

    if (page.name == 'productos_sin_voto') { // Pagina Productos Sin Voto
        //alert(usuario.iso);
        ObtieneEventoSinVoto(usuario.iso);
        //let back1 = document.getElementById("link_back_voto");
        //back1.href = "evento_sin_voto.html";
    }

    if (page.name == 'eventos_nuevos') { // Pagina Productos
        ObtieneEventoNuevos(usuario.iso);
    }

    /*
    if (page.name == 'encuesta') { // Pagina Encuesta
        ObtieneEncuesta();
    }
    */

    if (page.name == 'mapa') { // Pagina Productos

        const markersGet = `logica/logica-evento.php?action=mapMarkers`;
        const markersRequest = request(markersGet);

        markersRequest
            .then(function (rs) {
                var markers = JSON.parse(rs);

                // return markers;
                var map = L.map('map', {
                    // minZoom: 5,
                    zoom: 16
                    // center: [e.latitude,, e.longitude],
                    // zoom: 10,
                })

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    subdomains: ['a', 'b', 'c']
                }).addTo(map)

                // var markerss = L.markerClusterGroup(); //new

                // var myURL = jQuery( 'script[src$="leaf-demo.js"]' ).attr( 'src' ).replace( 'leaf-demo.js', '' )
                var myURL = 'maps/';

                var myIconBlue = L.icon({
                    iconUrl: myURL + 'images/pin24-blue.png',
                    iconRetinaUrl: myURL + 'images/pin48-blue.png',
                    iconSize: [39, 34],
                    iconAnchor: [9, 21],
                    popupAnchor: [0, -14]
                })

                var myIconRed = L.icon({
                    iconUrl: myURL + 'images/pin24-red.png',
                    iconRetinaUrl: myURL + 'images/pin48-red.png',
                    iconSize: [39, 34],
                    iconAnchor: [9, 21],
                    popupAnchor: [0, -14]
                });

                // marker for current location
                map.locate({
                    setView: true,
                    // zoom: 16,
                    maxZoom: 10
                }).on("locationfound", e => {

                    L.marker([e.latitude, e.longitude], {icon: myIconRed})
                        .bindPopup('Estás aquí!')
                        .addTo(map);

                }).on("locationerror", error => {
                    // error
                    console.log("error");
                });

                for (var i = 0; i < markers.length; ++i) {
                    var myIconBlue = L.icon({
                        iconUrl: myURL + 'images_new/' + markers[i].cat,
                        iconRetinaUrl: myURL + 'images_new/' + markers[i].cat,
                        iconSize: [39, 34],
                        iconAnchor: [9, 21],
                        popupAnchor: [0, -14]
                    })

                    var marker = L.marker([markers[i].lat, markers[i].lng], {icon: myIconBlue})
                        .bindPopup('<a href="' + markers[i].url + '" target="_blank">' + markers[i].name + '</a>')
                        .addTo(map);
                    // markerss.addLayer(marker); //new
                    // console.log("name: "+markers[i].name);
                }

                // map.addLayer(markerss); //new

                // center map for current location
                map.locate({setView: true, maxZoom: 10});

            })
            // .then(repoRequest)
            // .then(handleReposList)
            .catch(handleErrors)

    }

    if (page.name == 'categorias') { // Pagina Categorias
        ObtieneCategorias();
    }

    if (page.name == 'nuevo_producto') {

        //window.location = "index.html";
        // console.log("Usuario: "+usuario.logged);
        if (usuario.logged != true) {
            // return false;
            // myApp.alert('Para agregar un evento debe haber ingresado.', 'Error');

            myApp.popup('.popup-login');
            window.location = "index.html";


        }

        id_categoria = page.query.id_cat;
        if (id_categoria == undefined) {
            id_categoria = 0;
        }
        ObtienePaisActualPopup();

        $$('.popup-paises').on('open', function () {
            ObtieneListadoPaisesPopup();
        });
        ComboCategorias(id_categoria);
        // appCamera.initialize();

        const markersGet = `logica/logica-evento.php?action=mapMarkers`;
        const markersRequest = request(markersGet);
        markersRequest
            .then(function (rs) {

                var map = L.map('map', {
                    zoom: 16
                })

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    subdomains: ['a', 'b', 'c']
                }).addTo(map)

                var myURL = 'maps/';

                var myIconBlue = L.icon({
                    iconUrl: myURL + 'images/pin24-blue.png',
                    iconRetinaUrl: myURL + 'images/pin48-blue.png',
                    iconSize: [39, 34],
                    iconAnchor: [9, 21],
                    popupAnchor: [0, -14]
                })


                // marker for current location
                map.locate({
                    setView: true,
                    // zoom: 16,
                    maxZoom: 16
                }).on("locationfound", e => {
                    milatitude = e.latitude;
                    milongitude = e.longitude;
                    // console.log(milatitude+" || "+milongitude);

                    L.marker([e.latitude, e.longitude], {draggable: true})
                        .bindPopup('Estás aquí!')
                        .addTo(map)
                        .on('dragend', function (e) {
                            milatitude = e.target.getLatLng().lat;
                            milongitude = e.target.getLatLng().lng;

                        });
                }).on("locationerror", error => {
                    // error
                    console.log("error location");
                });



                // center map for current location
                // map.locate({setView: true, maxZoom: 16});
            })
            // .then(repoRequest)
            // .then(handleReposList)
            .catch(handleErrors)

    }

    if (page.name == 'detalle_producto') { // Pagina Detalles Producto
        tipo = page.query.tipo;
        id_comentario = page.query.id_comentario;
        //alert("id_comentario"+id_comentario);
        id_producto = page.query.id_prod;
        id_producto_global = page.query.id_prod; //
        id_categoria = page.query.id_categ;
        page.view.router.back({
            url: "eventos.html",
            force: true,
            ignoreCache: true
        });
        if (id_categoria == undefined) {
            id_categoria = 1;
        }
        SumaViews(id_producto);
        //alert(tipo);
        if (tipo == "notificacion") {
            //ObtenerComentariosPadretodos();

            ObtieneDetallesProductosNotificacion(id_producto, id_comentario);

        } else if (tipo == "notificacionrespuesta") {
            ObtieneDetallesProductosNotificacionRespuesta(id_producto, id_comentario);
        } else if (tipo == "notificacionrespuestarelacion") {
            ObtieneDetallesProductosNotificacionRelacionRespuesta(id_producto, id_comentario);
        } else {
            ObtieneDetallesProductos(id_producto);
        }
        // let back = document.getElementById("link_back");
        // back.href = "eventos.html?id_cat=" + id_categoria;

        //ComboCategorias(id_producto);

        // test
        const markersGet = `logica/logica-evento.php?action=mapMarkers`;
        const markersRequest = request(markersGet);
        markersRequest
            .then(function (rs) {
                var markers = JSON.parse(rs);

                // return markers;
                var map = L.map('map', {
                    // minZoom: 5,
                    zoom: 16
                    // center: [e.latitude,, e.longitude],
                    // zoom: 10,
                })
console.log("voy...");
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    subdomains: ['a', 'b', 'c']
                }).addTo(map)

                // var markerss = L.markerClusterGroup(); //new

                // var myURL = jQuery( 'script[src$="leaf-demo.js"]' ).attr( 'src' ).replace( 'leaf-demo.js', '' )
                var myURL = 'maps/';


                // marker for current location
                map.locate({
                    setView: true,
                    // zoom: 16,
                    maxZoom: 10
                }).on("locationfound", e => {

                    L.marker([e.latitude, e.longitude], {icon: myIconRed})
                        .bindPopup('Estás aquí!')
                        .addTo(map);

                }).on("locationerror", error => {
                    // error
                    console.log("error");
                });

                for (var i = 0; i < markers.length; ++i) {
                    var myIconBlue = L.icon({
                        iconUrl: myURL + 'images_new/' + markers[i].cat,
                        iconRetinaUrl: myURL + 'images_new/' + markers[i].cat,
                        iconSize: [39, 34],
                        iconAnchor: [9, 21],
                        popupAnchor: [0, -14]
                    })

                    var marker = L.marker([markers[i].lat, markers[i].lng], {icon: myIconBlue})
                        .bindPopup('<a href="' + markers[i].url + '" target="_blank">' + markers[i].name + '</a>')
                        .addTo(map);
                    // markerss.addLayer(marker); //new
                    // console.log("name: "+markers[i].name);
                }

                // map.addLayer(markerss); //new

                // center map for current location
                map.locate({setView: true, maxZoom: 10});

            })
            // .then(repoRequest)
            // .then(handleReposList)
            .catch(handleErrors)

    }

    if (page.name == 'myproducts') { // Pagina MyProductos
        id_usuario = page.query.id_usuario;
        //alert(id_usuario);
        obtieneMyProduct(id_usuario);
    }

    if (page.name == 'eventos_map') { // Pagina Evetnos Mapa
        id_usuario = page.query.id_usuario;
        //alert(id_usuario);
        obtieneNotificaciones(id_usuario);
    }

    if (page.name == 'notificaciones') { // Pagina Notificaciones
        id_usuario = page.query.id_usuario;
        //alert(id_usuario);
        obtieneNotificaciones(id_usuario);
    }

    $("#ContactForm").validate({
        submitHandler: function (form) {
            ajaxContact(form);
            return false;
        }
    });

    $("#RegisterForm").validate();
    $("#LoginForm").validate();
    $("#ForgotForm").validate();



    $("a.switcher").bind("click", function (e) {
        e.preventDefault();

        let theid = $(this).attr("id");
        let theproducts = $("ul#photoslist");
        let classNames = $(this).attr('class').split(' ');

        if ($(this).hasClass("active")) {
            // if currently clicked button has the active class
            // then we do nothing!
            return false;
        } else {
            // otherwise we are clicking on the inactive button
            // and in the process of switching views!

            if (theid == "view13") {
                $(this).addClass("active");
                $("#view11").removeClass("active");
                $("#view11").children("img").attr("src", "images/switch_11.png");

                $("#view12").removeClass("active");
                $("#view12").children("img").attr("src", "images/switch_12.png");

                let theimg = $(this).children("img");
                theimg.attr("src", "images/switch_13_active.png");

                // remove the list class and change to grid
                theproducts.removeClass("photo_gallery_11");
                theproducts.removeClass("photo_gallery_12");
                theproducts.addClass("photo_gallery_13");

            } else if (theid == "view12") {
                $(this).addClass("active");
                $("#view11").removeClass("active");
                $("#view11").children("img").attr("src", "images/switch_11.png");

                $("#view13").removeClass("active");
                $("#view13").children("img").attr("src", "images/switch_13.png");

                let theimg = $(this).children("img");
                theimg.attr("src", "images/switch_12_active.png");

                // remove the list class and change to grid
                theproducts.removeClass("photo_gallery_11");
                theproducts.removeClass("photo_gallery_13");
                theproducts.addClass("photo_gallery_12");

            } else if (theid == "view11") {
                $("#view12").removeClass("active");
                $("#view12").children("img").attr("src", "images/switch_12.png");

                $("#view13").removeClass("active");
                $("#view13").children("img").attr("src", "images/switch_13.png");

                let theimg = $(this).children("img");
                theimg.attr("src", "images/switch_11_active.png");

                // remove the list class and change to grid
                theproducts.removeClass("photo_gallery_12");
                theproducts.removeClass("photo_gallery_13");
                theproducts.addClass("photo_gallery_11");

            }

        }

    });

    document.addEventListener('touchmove', function (event) {
        if (event.target.parentNode.className.indexOf('navbarpages') != -1 || event.target.className.indexOf('navbarpages') != -1) {
            event.preventDefault();
        }
    }, false);

    // Add ScrollFix
    let scrollingContent = document.getElementById("pages_maincontent");
    // new ScrollFix(scrollingContent);


})

/* --------------------------------- Inicio Eventos  ----------------------------------- */

function ObtieneEvento(id_categoria, pais) {
    categoriaBuscar = id_categoria;
    // alert("Hola1");
    // muestraProductos();
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaEvento.php",
        data: "action=ObtieneEvento&id_categoria=" + id_categoria + "&pais=" + pais + "&id_usuario=" + usuario.id,
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando eventos...');
        },
        success: function (data) {
            myApp.hidePreloader();
            let res = data.split("|");

            $("#photoslist").html(res[0]);
            $("#TituloCategoria").html(res[1]);
            //ComboCategorias(id_categoria);
            //document.getElementById('photo').src = "images/camera.jpg";
        }
    });
}

function BuscarEvento() {
    alert("Buscar evento...");
    let nombreProducto = document.getElementById('searchProducto').value;
    if (nombreProducto == "") {
        myApp.alert("Debe escribir el nombre de un Producto.", "Alerta");
        return
    }
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaEvento.php",
        data: "action=BuscarEvento&id_categoria=" + categoria_global + "&producto=" + nombreProducto + "&iso=" + usuario.iso,
        beforeSend: function (objeto) {
            myApp.showPreloader('Buscando...');
        },
        success: function (data) {
            myApp.hidePreloader();
            $("#resultado_productos").html(data);
        }
    });
}

function limpiaBusqueda() {

    $("#resultado_productos").html("");
}

function subirProducto() {
    
    if(navigator.onLine==true){
    let imagen = document.getElementById('photoButton').style.backgroundImage;
    let id_categoria = document.getElementById('val_categoria').value;
    let nombreProducto = document.getElementById('producto').value;
    let descripcion = document.getElementById('descripcion').value;
    // console.log("photoButton.url: "+document.getElementById('photoButton').url);
    let stringSRC = imagen.includes("data:image/jpeg;base64,");
    // console.log("??? "+document.getElementById("imageUpload").target.files[0].name);
    console.log("stringSRC: " + stringSRC);
    //alert("usuario "+usuario.id+" iso "+usuario.iso);
    if (stringSRC == false) {
        myApp.alert("Debe subir una fotografía.", "Alerta");
        return
    }
    //alert(id_categoria);
    if (imagen == "" || id_categoria == "" || id_categoria == "0" || nombreProducto == "" || descripcion == "") {
        myApp.alert("Debe completar todos los campos", "Alerta");
        return
    } else {
        //alert(id_categoria+"-"+imagen+"-"+nombreProducto+"-"+descripcion);
        console.log(usuario);
        $.ajax({
            async: true,
            type: "POST",
            url: "logica/logicaEvento.php",
            data: "action=SubirEvento&id_categoria=" + id_categoria + "&imagen=" + imagen + "&nombreProducto=" + nombreProducto + "&descripcion=" + descripcion + "&miIP=" + miIP + "&latitude=" + milatitude + "&longitude=" + milongitude + "&iso=" + usuario.iso + "&id_usuario=" + usuario.id,
            beforeSend: function (objeto) {
                myApp.showPreloader('Guardando...');
            },
            success: function (data) {
                myApp.hidePreloader();
                if (data == 1) {
                    alert("Evento Insertado Correctamente");
                    obtieneCantidadProductos(usuario.id);
                    mainView.router.reloadPage('categorias.html');
                    //ObtieneEvento(id_categoria);
                } else {
                    alert("Surgió un problema. Intenta nuevamente.")
                }
                //document.getElementById('photo').src = "images/camera.jpg";
            }
        });
    }
    }else{
        alert("offline from: my-app.js");
    }
}

function ObtieneDetallesProductos(id_producto) {
    //alert(usuario.id);
    //muestraProductos();
    id_padre_limit = 0; //
    limit = 0;//
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaEvento.php",
        data: "action=DetalleEvento&id_producto=" + id_producto + "&id_usuario=" + usuario.id,
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando detalles...');
        },
        success: function (data) {
            myApp.hidePreloader();

            $("#detalles_producto").html(data);
        }
    });
}

function ObtieneDetallesProductosNotificacion(id_producto, id_comentario) {
    //alert(usuario.id);
    //muestraProductos();
    id_padre_limit = 0; //
    limit = 0;//

    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaEvento.php",
        data: "action=DetalleEventoNotificacion&id_producto=" + id_producto + "&id_usuario=" + usuario.id,
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando notificaciones...');
        },
        success: function (data) {

            $("#detalles_producto").html(data);
            //$("#vermaspadre").css("display", "none");
            myApp.hidePreloader();
            EliminaNotificaciones(id_producto);

        }
    });
}

function ObtieneDetallesProductosNotificacionRespuesta(id_producto, id_comentario) {
    alert("id_comentario" + id_comentario);
    //muestraProductos();
    id_padre_limit = 0; //
    limit = 0;//

    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaEvento.php",
        data: "action=DetalleEventoNotificacionRespuesta&id_producto=" + id_producto + "&id_usuario=" + usuario.id + "&id_comentario=" + id_comentario,
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando notificaciones...');
        },
        success: function (data) {

            $("#detalles_producto").html(data);
            //$("#vermaspadre").css("display", "none");
            myApp.hidePreloader();
            EliminaNotificacionesRespuesta(id_comentario);

        }
    });
}

function ObtieneDetallesProductosNotificacionRelacionRespuesta(id_producto, id_comentario) {
    alert("relacion id_comentario" + id_comentario);
    //muestraProductos();
    id_padre_limit = 0; //
    limit = 0;//

    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaEvento.php",
        data: "action=DetalleEventoNotificacionRespuesta&id_producto=" + id_producto + "&id_usuario=" + usuario.id + "&id_comentario=" + id_comentario,
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando notificaciones...');
        },
        success: function (data) {

            $("#detalles_producto").html(data);
            //$("#vermaspadre").css("display", "none");
            myApp.hidePreloader();
            EliminaNotificacionesRelacionRespuesta(id_producto);

        }
    });
}

function ObtieneEventoSinVoto(pais) {
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaEvento.php",
        data: "action=ObtieneEventoSinVoto&pais=" + pais + "&id_usuario=" + usuario.id,
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando eventos sin votos...');
        },
        success: function (data) {
            myApp.hidePreloader();
            $("#photoslist").html(data);
        }
    });
}

/* --------------------------------- Fin Eventos  ----------------------------------- */

/* --------------------------------- Inicio Categorias  ----------------------------------- */

function ObtieneCategorias() {
    let iso = usuario.iso;
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaCategorias.php",
        data: "action=ObtieneCategorias&iso=" + iso,
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando categorías...');
        },
        success: function (data) {
            myApp.hidePreloader();
            $("#categorias").html(data);
        }
    });
}

function ComboCategorias(id_categoria) {
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaCategorias.php",
        data: "action=ComboCategorias&id_categoria=" + id_categoria,
        beforeSend: function (objeto) {
            if(navigator.onLine == true){
            myApp.showPreloader('Cargando categorías...');
            }else{
                myApp.alert("¡Estás desconectado! su evento se guardará y se cargará cuando vuelva a estar en línea.");
            }
        },
        success: function (data) {
            myApp.hidePreloader();
            $("#val_categoria").html(data);
        }
    });
}

/* --------------------------------- Fin Categorias  ----------------------------------- */

/* --------------------------------- Geolocalizacion  ----------------------------------- */

function onSuccess(position) {
    milatitude = position.coords.latitude;
    milongitude = position.coords.longitude;

    $.post("https://nominatim.openstreetmap.org/reverse?format=json&lat=" + milatitude + "&lon=" + milongitude + "&sensor=false", function (result) {
        // console.log(result);
        // myApp.alert(result['address']['country'], 'Su País es:');
        usuario.iso = result['address']['country_code'];
    });
}

// onError Callback receives a PositionError object
//
function onError(error) {
    myApp.alert('code: ' + error.code + '\n' + 'message: ' + error.message + '\n', 'Error con la Geolocalización!');
    myApp.alert('Activa tu GPS para poder obtener tu ubicación.', 'Error con la Geolocalización!');
}

/* --------------------------------- Fin Geolocalizacion ----------------------------------- */

/* --------------------------------- Obtiene IP  ----------------------------------- */

function obtenerIP() {
    $.post("http://ipecho.net/plain", function (result) {
        // console.log(result);
        // myApp.alert(result, 'Su IP es:');
        miIP = location;
    });


}

/* --------------------------------- Fin Obtiene IP ----------------------------------- */


/* --------------------------------- Inicio Paises  ----------------------------------- */

function ObtienePaisActual() {
    //alert(usuario.iso);
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaPaises.php",
        data: "action=ObtienePaisActual&iso=" + usuario.iso,
        beforeSend: function (objeto) {
            //myApp.showPreloader('Cargando...');
        },
        success: function (data) {
            //alert("data");

            $("#pais_seleccionado").html(data);
            //myApp.hidePreloader();
            //ObtieneListadoPaises();
        }
    });
}

function ObtieneListadoPaises() {
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaPaises.php",
        data: "action=ObtieneListadoPaises",
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando listado países...');
        },
        success: function (data) {
            //alert("data");
            myApp.hidePreloader();
            $("#lista_paises").html(data);
        }
    });
}

function CambiaPais(iso) {

    usuario.iso = iso;
    ObtienePaisActual();
    mainView.router.reloadPage('paises.html');
}

function ObtienePaisActualPopup() {
    //alert(usuario.iso);
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaPaises.php",
        data: "action=ObtienePaisActualPopup&iso=" + usuario.iso,
        beforeSend: function (objeto) {
            //myApp.showPreloader('Cargando...');
        },
        success: function (data) {
            //alert("data");
            //myApp.hidePreloader();
            $("#pais_seleccionado_popup").html(data);
            //ObtieneListadoPaises();
        }
    });
}

function ObtieneListadoPaisesPopup() {
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaPaises.php",
        data: "action=ObtieneListadoPaisesPopup",
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando listado países...');
        },
        success: function (data) {
            //alert("data");
            myApp.hidePreloader();
            $("#lista_paises_popup").html(data);
        }
    });
}

function CambiaPaisPopup(iso) {

    //alert("nuevo iso: "+iso);
    //myApp.showPreloader('Cambiando...');
    usuario.iso = iso;
    //myApp.hidePreloader();
    //myApp.alert('País cambiado.','Éxito!');
    myApp.closeModal('.popup-paises');
    ObtienePaisActualPopup();
    //ObtieneListadoPaises();
}

/* --------------------------------- Fin Paises  ----------------------------------- */

/* --------------------------------- Inicio Eventos Nuevos  ----------------------------------- */

function ObtieneEventoNuevos(pais) {

    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaEventoNuevos.php",
        data: "action=ObtieneEventoNuevos&pais=" + pais,
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando...');
        },
        success: function (data) {
            myApp.hidePreloader();
            $("#photoslist").html(data);
        }
    });
}

/* --------------------------------- Fin Eventos Nuevos  ----------------------------------- */

/* --------------------------------- Inicio MyEventos  ----------------------------------- */

function obtieneMyProduct(idusuario) {

    if (idusuario != undefined) {
        //alert(idusuario);
        myApp.showPreloader("Cargando mypro....");
        $.ajax(
            {
                async: true,
                type: "POST",
                url: "logica/logicaUsuario.php",
                data: "action=MyProducts&idusuario=" + idusuario,
                success: function (data) {
                    //alert(data);
                    myApp.hidePreloader();
                    $("#lista_myproducts").html(data);

                }
            });

    } else {
        alert("error");
    }
}

/* --------------------------------- Fin MyEventos  ----------------------------------- */

/* --------------------------------- Inicio Notificaciones  ----------------------------------- */

function obtieneNotificaciones(idusuario) {

    if (idusuario != undefined) {
        //alert(idusuario);
        myApp.showPreloader("Cargando notificaciones....");
        $.ajax(
            {
                async: true,
                type: "POST",
                url: "logica/logicaUsuario.php",
                data: "action=MyNotificaciones&idusuario=" + idusuario,
                success: function (data) {
                    myApp.hidePreloader();
                    $("#lista_notificaciones").html(data);

                }
            });

    } else {
        alert("error");
    }
}

function EliminaNotificaciones(idproducto) {
    idusuario = usuario.id;
    if (idusuario != undefined) {
        //alert("error");
        $.ajax(
            {
                async: true,
                type: "POST",
                url: "logica/logicaUsuario.php",
                data: "action=EliminaNotificaciones&idusuario=" + idusuario + "&idproducto=" + idproducto,
                beforeSend: function (objeto) {
                    myApp.showPreloader('Cargando elimina notifcaciones...');
                },
                success: function (data) {
                    //alert(data);
                    obtieneCantidadAlertas(idusuario);
                    myApp.hidePreloader();
                    //$("#lista_notificaciones").html(data);

                }
            });

    } else {
        alert("error");
    }
}

function EliminaNotificacionesRespuesta(id_comentario) {
    idusuario = usuario.id;
    if (idusuario != undefined) {
        //alert("error");
        $.ajax(
            {
                async: true,
                type: "POST",
                url: "logica/logicaUsuario.php",
                data: "action=EliminaNotificacionesRespuesta&idusuario=" + idusuario + "&id_comentario=" + id_comentario,
                beforeSend: function (objeto) {
                    myApp.showPreloader('Cargando elimina notificaciones...');
                },
                success: function (data) {
                    alert(data);
                    obtieneCantidadAlertas(idusuario);
                    myApp.hidePreloader();
                    //$("#lista_notificaciones").html(data);

                }
            });

    } else {
        alert("error");
    }
}

function EliminaNotificacionesRelacionRespuesta(id_producto) {
    idusuario = usuario.id;
    if (idusuario != undefined) {
        //alert("error");
        $.ajax(
            {
                async: true,
                type: "POST",
                url: "logica/logicaUsuario.php",
                data: "action=EliminaNotificacionesRelacionRespuesta&idusuario=" + idusuario + "&id_producto=" + id_producto,
                beforeSend: function (objeto) {
                    myApp.showPreloader('Cargando elimina notificaiones...');
                },
                success: function (data) {
                    alert(data);
                    obtieneCantidadAlertas(idusuario);
                    myApp.hidePreloader();
                    //$("#lista_notificaciones").html(data);

                }
            });

    } else {
        alert("error");
    }
}

/* --------------------------------- Fin Notificaciones  ----------------------------------- */

/* --------------------------------- Inicio Comentarios  ----------------------------------- */

function AgregarComentario(id_usuario, id_producto) {
    if (usuario.logged == null) {
        myApp.alert('Para agregar comentarios a los eventos debe ingresar al aplicativo.', 'Alerta');
    } else {
        //alert("entro");
        texto = $("#comentario").val();
        $.ajax({
            async: true,
            type: "POST",
            url: "logica/logicaComentarios.php",
            data: "action=InsertaComentario&id_usuario=" + id_usuario + "&id_producto=" + id_producto + "&texto=" + texto,
            beforeSend: function (objeto) {
                myApp.showPreloader('Cargando agrega comentario...');
            },
            success: function (data) {
                ObtenerComentariosPadreInicio(id_producto, data);

                myApp.hidePreloader();
                //alert(data);
                //$("#val_categoria").html(data);
            }
        });
    }
}

function ObtenerComentariosPadreInicio(id_producto, id_comentario) {
    //alert("entro");
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaComentarios.php",
        data: "action=ObtieneComentariosPadre&id_producto=" + id_producto + "&limit=0&id_usuario=" + usuario.id,
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando obtiene come...');
        },
        success: function (data) {

            if (data == 0) {
                $("#comentarios").html("No existen comentarios en este producto");
            } else {
                $("#comentarios").html(data);
                $("#vertodospadre").css("display", "none");
                $("#vermaspadre").css("display", "block");
                $("#comentario").val("");
                limit = 0;
                limit_hijo = 0;

            }
            //alert(id_comentario);
            //location.hash = "#c" + id_comentario;
            myApp.hidePreloader();
            //alert(data);
            //$("#val_categoria").html(data);
        }
    });
}

function ObtenerComentariosPadreMas() {

    limit = limit + 4;
    //alert(limit);
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaComentarios.php",
        data: "action=ObtieneComentariosPadre&id_producto=" + id_producto_global + "&limit=" + limit + "&id_usuario=" + usuario.id,
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando obtiene comen...');
        },
        success: function (data) {
            if (data == 0) {
                $("#vermaspadre").css("display", "none");
            } else {
                $("#comentarios").append(data);
            }

            myApp.hidePreloader();
            //alert(data);
            //$("#val_categoria").html(data);
        }
    });
}

function ViewReplyPadre(id_padre) {
    $("#replypadre" + id_padre).css("display", "block");
}

function ReplyPadre(id_usuario, id_producto, id_padre) {
    if (usuario.logged == null) {
        myApp.alert('Para agregar comentarios a los eventos debe ingresar al aplicativo.', 'Alerta');
    } else {
        //alert("entro"+id_padre);
        texto = $("#comentarioreply" + id_padre).val();
        $.ajax({
            async: true,
            type: "POST",
            url: "logica/logicaComentarios.php",
            data: "action=InsertaComentarioReply&id_usuario=" + id_usuario + "&id_producto=" + id_producto + "&texto=" + texto + "&id_padre=" + id_padre,
            beforeSend: function (objeto) {
                myApp.showPreloader('Cargando viewreply...');
            },
            success: function (data) {

                ObtenerComentariosHijo(id_producto, id_padre);
                alert(data);
                //$("#val_categoria").html(data);
            }
        });
    }
}

function ObtenerComentariosHijo(id_producto, id_padre) {
    //alert("entro");
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaComentarios.php",
        data: "action=ObtenerComentariosHijo&id_producto=" + id_producto + "&limit=0&id_padre=" + id_padre,
        beforeSend: function (objeto) {
            //myApp.showPreloader('Cargando comentarios hi...');
        },
        success: function (data) {

            if (data == 0) {
                $("#c" + id_padre).html("No existen comentarios en este producto");
            } else {
                //$("#vermaspadre").css("display", "block");
                $("#comentarioreply" + id_padre).val("");
                $("#replypadre" + id_padre).css("display", "none");
                $("#vermashijo" + id_padre).remove();
                $("#comments-hijo" + id_padre).html(data);
                $("#limitpadre" + id_padre).val("0");
                limit = 0;
                id_padre_limit = 0;

            }
            //alert(id_comentario);
            //location.hash = "#c" + id_comentario;
            myApp.hidePreloader();
            //alert(data);
            //$("#val_categoria").html(data);
        }
    });
}

function ObtenerComentariosHijosMas(id_producto, id_padre) {
    limit_hijo = Number($("#limitpadre" + id_padre).val()) + Number(4);

    //alert('limit'+limit_hijo+'producto'+id_producto+'padre'+id_padre);
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaComentarios.php",
        data: "action=ObtenerComentariosHijoMas&id_producto=" + id_producto + "&limit=" + limit_hijo + "&id_padre=" + id_padre,
        beforeSend: function (objeto) {
            myApp.showPreloader('Cargando comentarios hijo...');
        },
        success: function (data) {

            if (data == 0) {
                $("#vermashijo" + id_padre).css("display", "none");
            } else {
                //$("#vermaspadre").css("display", "block");
                $("#vermashijo" + id_padre).remove();
                $("#comments-hijo" + id_padre).append(data);
                $("#limitpadre" + id_padre).val(limit_hijo);

            }
            //alert(id_comentario);
            //location.hash = "#c" + id_comentario;
            myApp.hidePreloader();
            //alert(data);
            //$("#val_categoria").html(data);
        }
    });
}

function OpenComentsOption(comentario) {
    let clickedLink = $(comentario);
    let popoverHTML = '<div class="popover">' +
        '<div class="popover-inner">' +
        '<div class="list-block">' +
        '<ul>' +
        '<li><a href="#" class="item-link list-button">Link 1</li>' +
        '<li><a href="#" class="item-link list-button">Link 2</li>' +
        '<li><a href="#" class="item-link list-button">Link 3</li>' +
        '</ul>' +
        '</div>' +
        '</div>' +
        '</div>'
    myApp.popover(popoverHTML, clickedLink);

}

/* --------------------------------- Fin Comentarios  ----------------------------------- */

/* --------------------------------- Inicio Imagen Perfil  ----------------------------------- */

function mostrarimagen(imagen) {
    //imagen = document.getElementById('imagenperfil').src;
    /*=== Popup Dark ===  */
    let myPhotoBrowserPopupDark = myApp.photoBrowser({
        photos: [imagen,],
        theme: 'dark',
        type: 'popup'
    });
    myPhotoBrowserPopupDark.open();
}

/* --------------------------------- Fin Imagen Perfil  ----------------------------------- */

/* --------------------------------- Inicio VIEWS  ----------------------------------- */

function SumaViews(id_evento) {

    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaEvento.php",
        data: "action=SumaView&id_producto=" + id_evento,
        beforeSend: function (objeto) {
            //myApp.showPreloader('Cargando suma views...');
        },
        success: function (data) {
            if (data == 1) {
                ObtieneViews(id_evento);
            }
            //myApp.hidePreloader();
            //$("#cant_views").html(data);
        }
    });
}

function ObtieneViews(id_evento) {

    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaEvento.php",
        data: "action=MuestraView&id_producto=" + id_evento,
        beforeSend: function (objeto) {
            //myApp.showPreloader('Cargando obtiene views...');
        },
        success: function (data) {
            $("#cant_views").html(data);
        }
    });
}

/* --------------------------------- Fin VIEWS  ----------------------------------- */

/* --------------------------------- Inicio VIEWS  ----------------------------------- */

function VotoLike(id_usuario, id_evento) {
    if (usuario.logged == null) {
        myApp.alert('Para votar por eventos debe ingresar al aplicativo.', 'Alerta');
    } else {
        myApp.confirm('Usted va a votar VÁLIDO para este evento, está seguro?', 'Voto Válido', function () {
            $.ajax({
                async: true,
                type: "POST",
                url: "logica/logicaVotaciones.php",
                data: "action=VotoLike&id_usuario=" + id_usuario + "&id_producto=" + id_evento,
                beforeSend: function (objeto) {
                    //myApp.showPreloader('Cargando votolike...');
                },
                success: function (data) {
                    if (data == 1) {
                        document.getElementById('imageValidor' + id_evento).src = "images/icons/white/like_mZpaCR.png";
                        document.getElementById('imageNoValido' + id_evento).src = "images/icons/white/mZapCR_dislike.png";
                        ObtieneEstrellas(id_evento);
                        document.getElementById('prod-' + id_evento).style.display = "none";
                    }
                }
            });
        });
    }
}

function VotoDiskike(id_usuario, id_evento) {
    if (usuario.logged == null) {
        myApp.alert('Para votar por eventos debe ingresar al aplicativo.', 'Alerta');
    } else {
        myApp.confirm('Usted va a votar NO VÁLIDO para este evento, está seguro?', 'Voto No Válido', function () {
            $.ajax({
                async: true,
                type: "POST",
                url: "logica/logicaVotaciones.php",
                data: "action=VotoDiskike&id_usuario=" + id_usuario + "&id_producto=" + id_evento,
                beforeSend: function (objeto) {
                    //myApp.showPreloader('Cargando voto disklike...');
                },
                success: function (data) {
                    //alert(data);
                    if (data == 1) {
                        document.getElementById('imageValido' + id_evento).src = "images/icons/white/mZapCR_like.png";
                        document.getElementById('imageNoValido' + id_evento).src = "images/icons/white/mZapCR_dislike.png";
                        ObtieneEstrellas(id_evento);
                        document.getElementById('prod-' + id_evento).style.display = "none";
                    }
                }
            });
        });
    }
}

function ObtieneEstrellas(id_evento) {
    $.ajax({
        async: true,
        type: "POST",
        url: "logica/logicaEvento.php",
        data: "action=ObtieneEstrellasEvento&id_producto=" + id_evento,
        beforeSend: function (objeto) {
            //myApp.showPreloader('Cargando estrellas...');
        },
        success: function (data) {
            //alert(data);
            let res = data.split("|");
            $("#puntuacion" + id_evento).html(res[0]);
            $("#cant_porcentaje" + id_evento).html(res[1]);
            $("#cant_votos" + id_evento).html(res[2]);

        }
    });
}

/* --------------------------------- Fin VIEWS  ----------------------------------- */

/**
 * Get's the user's location and logs it to the console
 */
function getLocation() {
    return new Promise(function (resolve) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(resolve, resolve);
        } else {
            console.error("Geolocation is not supported by this browser.");
            resolve(null);
        }
        ;
    }).then(function (position) {
        if (position !== null && position !== undefined && position.code !== 1) {
            return position.coords.latitude + "," + position.coords.longitude;
        }
        return null;
    });
}

function generateLocationLink(location) {
    const googleMapsEmbedLink = "https://maps.google.com/maps?t=&ie=UTF8&iwloc=";

    if (location === null || location === undefined || location.length == 0) {
        return googleMapsEmbedLink;
    }

    return googleMapsEmbedLink + "&q=" + encodeURI(location);
}

function generateEmbedLocationLink(location) {
    return generateLocationLink(location) + "&output=embed";
}

function updateLocation() {
    let mapFrame = document.getElementById("gmap");
    let location = document.getElementById('location').value;
    let src = generateEmbedLocationLink(location);
    mapFrame.setAttribute("src", src);
}

function initLocationService() {

    let locationInputElement = document.getElementById('location');
    locationInputElement.addEventListener("input", updateLocation);
    locationInputElement.addEventListener("change", updateLocation);
    // let coordenadas = getLocation();
    // totest();
    getLocation().then(function (coordinates) {
        //TODO convert coordinates to address using google api.
        locationInputElement.value = encodeURI(coordinates);
        updateLocation();
        //     let coordenadas = coordinates.split(',');
        //     milatitude = coordenadas[0];
        //     milongitude = coordenadas[1];
        //     console.log("latitude: "+milatitude);
        //     console.log("longitude: "+milongitude);
    })
}

function myCurrentLocation() {

    // navigator.geolocation.getCurrentPosition(function () {}, function () {}, {});
    //The working next statement.
    navigator.geolocation.getCurrentPosition(function (position) {
        milatitude = position['coords']['latitude'];
        milongitude = position['coords']['longitude'];
    }, function (e) {
        //Your error handling here
    }, {
        enableHighAccuracy: true
    });

}

/**
 * Gets the image from the user and set it to the photo button
 */
function getImage__() {
    var image = null;
    var file = document.getElementById("imageUpload").files[0];
    var reader = new FileReader();

    reader.onload = function () {
        image = "url(" + reader.result + ")";
        document.getElementById('photoButton').style.backgroundImage = image;
    };

    if (file) {
        image = reader.readAsDataURL(file);
    }

    return file;
}


/**
 * Gets the image from the user and set it to the photo button
 */
function getImage() { // getImage_resize
    let image = null;
    let file = document.getElementById("imageUpload").files[0];
    let reader = new FileReader();

    reader.onload = function () {
        minifyImg(reader.result, 500, 'image/jpeg', (data) => {
            image = "url(" + data + ")";
            document.getElementById('photoButton').style.backgroundImage = image;
        });
    };

    if (file) {
        image = reader.readAsDataURL(file);
    }

    return file;
}

var minifyImg = function (dataUrl, newWidth, imageType = "image/jpeg", resolve, imageArguments = 0.7) {
    var image, oldWidth, oldHeight, newHeight, canvas, ctx, newDataUrl;
    (new Promise(function (resolve) {
        image = new Image();
        image.src = dataUrl;
        // console.log(image);
        resolve('Done : ');
    })).then((d) => {
        oldWidth = image.width;
        oldHeight = image.height;
        // console.log([oldWidth, oldHeight]);
        newHeight = Math.floor(oldHeight / oldWidth * newWidth);
        // console.log(d + ' ' + newHeight);

        canvas = document.createElement("canvas");
        canvas.width = newWidth;
        canvas.height = newHeight;
        // console.log(canvas);
        ctx = canvas.getContext("2d");
        ctx.drawImage(image, 0, 0, newWidth, newHeight);
        //log(ctx);
        newDataUrl = canvas.toDataURL(imageType, imageArguments);
        resolve(newDataUrl);
    });
};
