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
if(!empty($_SERVER['HTTP_AUTHORIZATION'])) {
  $jwt = $_SERVER['HTTP_AUTHORIZATION'];
  try {
    $JWTraw = JWT::decode($jwt, $bd->getClave(), array('HS256'));
    $idUser = $JWTraw->id;
  } catch (Exception $e) { }
}
//Comprueba primero que no sea una peticion de tipo OPTIONS.
//Si no es así, opera con cada uno de los metodos
if($metodo != "OPTIONS") {
  switch($metodo) {

//Metodo GET.
    case "GET":
      $busqueda = null;
      if(!empty($_GET["busqueda"])) $busqueda = $_GET["busqueda"];
      
      $eval = "SELECT * FROM notas WHERE (idUser IS NULL";
      $eval .= ($idUser ? " OR idUser = ". $idUser : null).")";
      $eval .= $busqueda ? " AND CONCAT_WS('', titulo,contenido) LIKE '%".$busqueda."%'" : null;

      $peticion = $conexion->prepare($eval);
      $peticion->execute();
      $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
      exit(json_encode($resultado));
      break;

//Metodo POST.
    case "POST":
      $nota = json_decode(file_get_contents("php://input"));

      if(!isset($nota->titulo) || !isset($nota->contenido)) {
        http_response_code(400);
        exit(json_encode(["error" => "No se han enviado todos los parametros"]));
      }

      $peticion = $conexion->prepare("INSERT INTO notas (titulo,contenido,idUser) VALUES (?,?,?)");
      $resultado = $peticion->execute([$nota->titulo,$nota->contenido,$idUser]);
      http_response_code(201);
      exit(json_encode("Nota creada correctamente"));
      break;

//Metodo PUT.
    case "PUT":
      $nota = json_decode(file_get_contents("php://input"));
      if($idUser) {
        if(!isset($nota->id) || !isset($nota->titulo) || !isset($nota->contenido)) {
          http_response_code(400);
          exit(json_encode(["error" => "No se han enviado todos los parametros"]));
        }
        $eval = "UPDATE notas SET titulo=?, contenido=? WHERE id=? AND idUser=?";
        $peticion = $conexion->prepare($eval);
        $resultado = $peticion->execute([$nota->titulo,$nota->contenido,$nota->id,$idUser]);
        http_response_code(201);
        exit(json_encode("Nota actualizada correctamente"));
      } else {
        http_response_code(401);
        exit(json_encode(["error" => "Fallo de autorizacion"]));        
      }
      break;

//Metodo DELETE.
    case "DELETE":
      $id = null;
      if(!empty($_GET["id"])) $id = $_GET["id"];
      else{
        http_response_code(400);
        exit(json_encode(["error" => "Peticion mal formada"]));    
      }
      if($idUser) {
        $eval = "DELETE FROM notas WHERE id=? AND idUser=?";
        $peticion = $conexion->prepare($eval);
        $resultado = $peticion->execute([$id,$idUser]);
        http_response_code(200);
        exit(json_encode("Nota eliminada correctamente"));
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