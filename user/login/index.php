<?php
//Librerias necesarias.
require_once("../../config/db.php");
require "../../vendor/autoload.php";
use \Firebase\JWT\JWT;

//Proteccion CORS.
header("Access-Control-Allow-Methods: POST");
header('Content-Type: application/json');

//Guardamos el metodo utilizado para acceder a esta URL.
$metodo = $_SERVER["REQUEST_METHOD"];

//Comprobamos que sea un metodo POST
if ($metodo == "POST") {
  //Obtenemos la conexion con la base de datos.
  $bd = new db();
  $conexion = $bd->getConnection();

  $user = json_decode(file_get_contents("php://input"));

  if(!isset($user->email) || !isset($user->password)) {
    http_response_code(400);
    exit(json_encode(["error" => "No se han enviado todos los parametros"]));
  }

  //Primero busca si existe el usuario, si existe que obtener el id y la password.
  $peticion = $conexion->prepare("SELECT id,password FROM users WHERE email = ?");
  $peticion->execute([$user->email]);
  $resultado = $peticion->fetchObject();

  if($resultado) {

    //Si existe un usuario con ese email comprobamos que la contraseña sea correcta.
    if(password_verify($user->password, $resultado->password)) {

      //Preparamos el token.
      $iat = time();
      $exp = $iat + 3600*24*2;
      $token = array(
        "id" => $resultado->id,
        "iat" => $iat,
        "exp" => $exp
      );

      //Calculamos el token JWT y lo devolvemos.
      $jwt = JWT::encode($token, $bd->getClave());
      http_response_code(200);
      exit(json_encode($jwt));

    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Password incorrecta"]));
    }

  } else {
    http_response_code(404);
    exit(json_encode(["error" => "No existe el usuario"]));  
  }
}