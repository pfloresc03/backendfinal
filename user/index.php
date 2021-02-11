<?php
//Librerias necesarias.
require_once("../config/db.php");
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;

//Proteccion CORS.
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE");
header('Content-Type: application/json');

//Guardamos el metodo utilizado para acceder a esta URL.
$metodo = $_SERVER["REQUEST_METHOD"];

//Obtenemos la conexion con la base de datos.
$bd = new db();
$conexion = $bd->getConnection();
$idUser = null;

//Comprueba si hay algún token valido en la cabecera y obtiene el ID del USER
if(!empty($_SERVER['HTTP_AUTHORIZATION']) && $metodo != "POST") {
  $jwt = $_SERVER['HTTP_AUTHORIZATION'];
  try {
    $JWTraw = JWT::decode($jwt, $bd->getClave(), array('HS256'));
    $idUser = $JWTraw->id;
  } catch (Exception $e) {}
}
//Comprueba primero que no sea una peticion de tipo OPTIONS.
//Si no es así, opera con cada uno de los metodos
if($metodo != "OPTIONS") {
  switch($metodo) {

//Metodo GET. Obtiene los datos del usuario si ha hecho LOGIN.
    case "GET":
      if($idUser) {
        $eval = "SELECT nombre,apellidos,email,telefono,dni,imgSrc FROM users WHERE id=?";
        $peticion = $conexion->prepare($eval);
        $peticion->execute([$idUser]);
        $resultado = $peticion->fetchObject();
        echo json_encode($resultado);
      } else {
        http_response_code(401);
        echo json_encode(["error" => "Fallo de autorizacion"]);       
      }
      break;

//Metodo POST.
    case "POST":
      //Guardamos los parametros de la petición.
      $user = json_decode(file_get_contents("php://input"));

      //Comprobamos que los datos sean consistentes.
      if(!isset($user->email) || !isset($user->password)|| !isset($user->dni)) {
        http_response_code(400);
        exit(json_encode(["error" => "No se han enviado todos los parametros"]));

      }
      if(!isset($user->nombre)) $user->nombre = null;
      if(!isset($user->apellidos)) $user->apellidos = null;
      if(!isset($user->telefono)) $user->telefono = null;
      
      //Comprueba que no exista otro usuario con el mismo email.
      $peticion = $conexion->prepare("SELECT id FROM users WHERE email=?");
      $peticion->execute([$user->email]);
      $resultado = $peticion->fetchObject();
      if(!$resultado) {
        $password = password_hash($user->password, PASSWORD_BCRYPT);
        $eval = "INSERT INTO users (nombre,apellidos,password,email,telefono,dni) VALUES (?,?,?,?,?,?)";
        $peticion = $conexion->prepare($eval);
        $peticion->execute([
          $user->nombre,$user->apellidos,$password,$user->email,$user->telefono,$user->dni
        ]);
        
        //Preparamos el token.
        $id = $conexion->lastInsertId();
        $iat = time();
        $exp = $iat + 3600*24*2;
        $token = array(
          "id" => $id,
          "iat" => $iat,
          "exp" => $exp
        );

        //Calculamos el token JWT y lo devolvemos.
        $jwt = JWT::encode($token, $bd->getClave());
        http_response_code(201);
        echo json_encode($jwt);
      } else {
        http_response_code(409);
        echo json_encode(["error" => "Ya existe este usuario"]);
      }
      break;

//Metodo PUT. Edita el usuario siempre que este haya hecho login
    case "PUT":
      if($idUser) {
        //Cogemos los valores de la peticion.
        $user = json_decode(file_get_contents("php://input"));
        
        //Comprobamos si existe otro usuario con ese correo electronico.
        if(isset($user->email)) {
          $peticion = $conexion->prepare("SELECT id FROM users WHERE email=?");
          $peticion->execute([$user->email]);
          $resultado = $peticion->fetchObject();
          
          //Comprobamos si hay algun resultado, sino continuamos editando.
          if($resultado) {
            //Si el id del usuario con este email es distinto del usuario que ha hecho LOGIN.
            if($resultado->id != $idUser) {
              http_response_code(409);
              exit(json_encode(["error" => "Ya existe un usuario con este email"]));              
            }
          } 
        }

        //Obtenemos los datos guardados en el servidor relacionados con el usuario
        $peticion = $conexion->prepare("SELECT nombre,apellidos,email,telefono FROM users WHERE id=?");
        $peticion->execute([$idUser]);
        $resultado = $peticion->fetchObject();

        //Combinamos los datos de la petición y de los que había en la base de datos.
        $nNombre = isset($user->nombre) ? $user->nombre : $resultado->nombre;
        $nApellidos = isset($user->apellidos) ? $user->apellidos : $resultado->apellidos;
        $nTelefono = isset($user->telefono) ? $user->telefono : $resultado->telefono;
        $nEmail = isset($user->email) ? $user->email : $resultado->email;

        //Si hemos recibido el dato de modificar la password.
        if(isset($user->password) && (strlen($user->password))){

          //Encriptamos la contraseña.
          $nPassword = password_hash($user->password, PASSWORD_BCRYPT);
          //Preparamos la petición.
          $eval = "UPDATE users SET nombre=?,apellidos=?,password=?,email=?,telefono=? WHERE id=?";
          $peticion = $conexion->prepare($eval);
          $peticion->execute([$nNombre,$nApellidos,$nPassword,$nEmail,$nTelefono,$idUser]);
        } else {
          $eval = "UPDATE users SET nombre=?,apellidos=?,email=?,telefono=? WHERE id=?";
          $peticion = $conexion->prepare($eval);
          $peticion->execute([$nNombre,$nApellidos,$nEmail,$nTelefono,$idUser]);        
        }
        http_response_code(201);
        exit(json_encode("Usuario actualizado correctamente"));
      } else {
        http_response_code(401);
        exit(json_encode(["error" => "Fallo de autorizacion"]));         
      }
      break;

//Metodo DELETE. Elimina el usuario si este ha hecho LOGIN.
    case "DELETE":
      if($idUser) {
        
        //Buscamos si el usuario tenía imagenes y la eliminamos.
        $imgSrc = "../images/p-".$idUser.".*";
        $imgFile = glob($imgSrc);
        foreach($imgFile as $fichero) unlink($fichero);

        $eval = "DELETE FROM users WHERE id=?";
        $peticion = $conexion->prepare($eval);
        $resultado = $peticion->execute([$idUser]);
        http_response_code(200);
        exit(json_encode("Usuario eliminado correctamente"));
      } else {
        http_response_code(401);
        exit(json_encode(["error" => "Fallo de autorizacion"]));            
      }
      break;

    default:
      http_response_code(404);
      exit(json_encode(["error" => "Solo se permiten metodos GET, POST, PUT, DELETE"]));
      break;
  }
}







