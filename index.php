<?php
//Importamos las librerias necesarias.
require_once 'config/db.php';
require_once 'config/cors.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

//Guardamos la url para buscar el controlador y ponemos mensaje de bienvenida.
if(!isset($_GET['url'])) {
  exit(json_encode(["Bienvenido al Backendfinal con routes"]));
}

$url = $_GET['url'];

//Preparamos la conexion con la base de datos
$bd = new db();
$conexion = $bd->getConnection();

//Comprueba si hay algún token valido en la cabecera y obtiene el ID del USER
$idUser = null;
//A su vez se obtiene el rol del usuario.
$rolUser = null;

if(!empty($_SERVER['HTTP_AUTHORIZATION'])) {
  $jwt = $_SERVER['HTTP_AUTHORIZATION'];
  try {
    //A diferencia del anterior backend, en este hemos modificado el contenido del token,
    //añadiendole al final ? idRol. Con la función explode se divide facilmente.
    $jwt = explode('?',$jwt)[0];

    $JWTraw = JWT::decode($jwt, $bd->getClave(), array('HS256'));
    $idUser = $JWTraw->id;

    //Aun pasando el proceso de verificación JWT se comprueba si en la base de datos existe el usuario.
    $peticion = $conexion->prepare("SELECT id,idRol FROM users WHERE id = ?");
    $peticion->execute([$idUser]);
    if($peticion->rowCount() == 0) {
      $idUser = null;
    } else {
      $resultado = $peticion->fetchObject();
      $rolUser = $resultado->idRol;
    }
  } catch (Exception $e) { }
}

//Guardamos las variables globales. IDUSER, Metodo, CJWT, DIRECTORIO ROOT.
define('IDUSER', $idUser);
define('ROLUSER', $rolUser);
define('METODO', $_SERVER["REQUEST_METHOD"]);
define('ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('CJWT', $bd->getClave());

//También definimos los diferentes IDROL que tendrá nuestra aplicación y que corresponden con la tabla SQL.
define('IDADMIN', 14);
define('IDUSER1', 1);

//Procesamos la ruta y los metodos.
$control = explode('/',$url);
switch($control[0]) {

  case "user":
    require_once("controllers/user.controller.php");
    $user = new UserController($conexion);
    switch(METODO) {
      case "GET":
        switch($control[1]) {
          case "list":
            $user->listarUser();
            break;
          case "":
            $user->leerPerfil();
            break;
        }
        break;

      case "POST":
        switch($control[1]) {
          case "login":
            $user->hacerLogin();
            break;
          case "":
            $user->registrarUser();
        }
        break;

      case "PUT":
        $user->editarUser();
        break;

      case "DELETE":
        $user->eliminarUser();
        break;

      default: exit(json_encode(["Bienvenido al Backend con routes"]));  
    }  
    break;

  case "archivos":
    require_once("controllers/partituras.controller.php");
    $partituras = new PartiturasController($conexion);
    switch(METODO) {
      case "GET":
        $partituras->obtenerPartituras($control[1]);
        break;

      case "POST":
        $partituras->subirArchivo($control[1],$control[2],$control[3]);
        break;

      case "PUT":
        $partituras->editarPartitura();
        break;

      case "DELETE":
        $partituras->eliminarPartitura($control[1]);
        break;

      default: exit(json_encode(["Bienvenido al Backend con routes"]));
    }
    break;

  case "instrumentos":
    require_once("controllers/instrumentos.controller.php");
    $instrumentos = new InstrumentosController($conexion);
    switch(METODO) {
      case "GET":
        if(isset($control[1]) && $control[1]== "ver")
          $instrumentos->verInstrumentos();
        else
          $instrumentos->obtenerPartituras($control[1]);
        break;

      default: exit(json_encode(["Bienvenido al Backend con routes"]));
    }
    break;

  case "obras":
    require_once("controllers/obras.controller.php");
    $mensajes = new ObrasController($conexion);
    switch(METODO) {
      case "GET":
        $mensajes->leerObras();
        break;

      case "POST":
        $mensajes->crearObra();
        break;

      case "PUT":
        $mensajes->editarObra();
        break;

      case "DELETE":
        $mensajes->eliminarObra($control[1]);
        break;

      default: exit(json_encode(["Bienvenido al Backend con routes"]));
    }
    break;

  case "conciertos":
    require_once("controllers/conciertos.controller.php");
    $conciertos = new ConciertosController($conexion);
    switch(METODO) {
      case "GET":
        $conciertos->verConciertos();
        break;

      case "POST":
        $conciertos->crearConcierto();
        break;

      case "PUT":
        $conciertos->editarConcierto();
        break;

      case "DELETE":
        $conciertos->eliminarConcierto($control[1]);
        break;

      default: exit(json_encode(["Bienvenido al Backend con routes"]));
    }
    break;
  case "videos":
    require_once("controllers/videos.controller.php");
    $mensajes = new VideosController($conexion);
    switch(METODO) {
      case "GET":
        $mensajes->obtenerVideos($control[1]);
        break;

      case "POST":
        $mensajes->publicarVideo();
        break;

      case "PUT":
        $mensajes->editarVideo();
        break;
        
      case "DELETE":
        $mensajes->eliminarVideo($control[1]);
        break;

      default: exit(json_encode(["Bienvenido al Backend con routes"]));
    }
    break;

  case "admin":
    require_once("controllers/admin.controller.php");
    $admin = new AdminController($conexion);
    switch(METODO) {
      case "GET":
        if(isset($control[1]) && $control[1] == "roles")
          $admin->obtenerRoles();
        else
          $admin->obtenerUsers();
        break;
      case "PUT":
        $admin->cambiarRol();
        break;
      default: exit(json_encode(["Bienvenido al Backend con routes"]));
    }
    break;

    default:
    exit(json_encode(["Bienvenido al Backend con routes"]));
}

