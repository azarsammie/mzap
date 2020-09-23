<?php
session_start();
//error_reporting(E_ALL);
require_once("../data/dataContactenos.php");


if (isset($_POST["action"])) {
	$action = $_POST["action"];
} else {
	$action = $_REQUEST["action"];
}


switch ($action) {

	case "Contactenos":
		/* $nombre = $_POST["name"];
		 $email = $_POST["email"];
		 $mensaje = $_POST["message"];*/

		$name = stripslashes($_POST['ContactName']);
		$to = trim($_POST['to']);
		$email = strtolower(trim($_POST['ContactEmail']));
		$subject = stripslashes($_POST['subject']);
		$message = stripslashes($_POST['ContactComment']);
		$error = '';
		$Reply = $to;
		$from = $to;

		// Check Name Field
		if (!$name) {
			$error .= 'Por favor ingrese su nombre.<br />';
		}

		// Checks Email Field
		if (!$email) {
			$error .= 'Por favor ingrese un correo.<br />';
		}
		if ($email && !ValidateEmail($email)) {
			$error .= 'Por favor ingrese un correo válido.<br />';
		}

		// Checks Subject Field
		if (!$subject) {
			$error .= 'Por favor ingrese su asunto.<br />';
		}

		// Checks Message (length)
		if (!$message || strlen($message) < 3) {
			$error .= "Por favor ingrese un mensaje. Debe tener al menos 5 caracteres.<br />";
		}

		// Let's send the email.
		if (!$error) {
			$dataContactenos = new Contactenos();
			$dataContactenos->nombre = $name;
			$dataContactenos->email = $email;
			$dataContactenos->mensaje = $message;
			$result = $dataContactenos->InsertaNuevoMensaje();
			$messages = "De: $email <br>";
			$messages .= "Nombre: $name <br>";
			$messages .= "Correo: $email <br>";
			$messages .= "Mensaje: $message <br><br>";
			$emailto = $to;

			$mail = mail($emailto, $subject, $messages, "from: $from <$Reply>\nReply-To: $Reply \nContent-type: text/html");

			if ($mail) {
				echo 'success';
			}
		} else {
			echo '<div class="error">' . $error . '</div>';
		}

		/*  $dataContactenos = new Contactenos();
		  $dataContactenos->nombre = $nombre;
		  $dataContactenos->email = $email;
		  $dataContactenos->mensaje = $mensaje;
		  $result = $dataContactenos->InsertaNuevoMensaje();

		  echo $result;*/

		break;

	default :
		break;
}

function ValidateEmail($email) {

	$regex = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";
	$eregi = preg_replace($regex, '', trim($email));

	return empty($eregi) ? true : false;
}