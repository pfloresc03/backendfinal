<?php
//Librerias necesarias.
require_once("../../config/db.php");
require "../../vendor/autoload.php";
use \Firebase\JWT\JWT;

//Proteccion CORS.
header("Access-Control-Allow-Methods: POST");

//Guardamos el metodo utilizado para acceder a esta URL.
$metodo = $_SERVER["REQUEST_METHOD"];

//Comprobamos que sea un metodo POST
if ($metodo == "POST") {

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
    } catch (Exception $e) {}
  }

  if(is_null($idUser)){
    http_response_code(401);
    exit(json_encode(["error" => "Fallo de autorizacion"]));
  }
  if(isset($_FILES['imagen'])) {
    $imagen = $_FILES['imagen'];
    $mime = $imagen['type'];
    $size = $imagen['size'];
    $rutaTemp = $imagen['tmp_name'];

    //Comprobamos que la imagen sea JPEG o PNG y que el tamaño sea menor que 400KB.
    if( !(strpos($mime, "jpeg") || strpos($mime, "png")) || ($size > 400000) ) {
      http_response_code(400);
      exit(json_encode(["error" => "La imagen tiene que ser JPG o PNG y no puede ocupar mas de 400KB"]));
    } else {

      //Comprueba cual es la extensión del archivo.
      $ext = strpos($mime, "jpeg") ? ".jpg":".png";
      $nombreFoto = "p-".$idUser."-".time().$ext;
      $ruta = "../../images/".$nombreFoto;

      //Comprobamos que el usuario no tenga mas fotos de perfil subidas al servidor.
      //En caso de que exista una imagen anterior la elimina.
      $imgFind = "../../images/p-".$idUser."-*";
      $imgFile = glob($imgFind);
      foreach($imgFile as $fichero) unlink($fichero);
      
      //Si se guarda la imagen correctamente actualiza la ruta en la tabla usuarios
      if(move_uploaded_file($rutaTemp,$ruta)) {

        //Prepara el contenido del campo imgSrc
        $imgSRC = "http://localhost/backendPHP/images/".$nombreFoto;

        $eval = "UPDATE users SET imgSrc=? WHERE id=?";
        $peticion = $conexion->prepare($eval);
        $peticion->execute([$imgSRC,$idUser]);

        http_response_code(201);
        exit(json_encode("Imagen actualizada correctamente"));
      } else {
        http_response_code(500);
        exit(json_encode(["error" => "Ha habido un error con la subida"]));      
      }
    }
  }  else {
    http_response_code(400);
    exit(json_encode(["error" => "No se han enviado todos los parametros"]));
  }
}