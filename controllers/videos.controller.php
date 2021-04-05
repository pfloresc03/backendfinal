<?php

class VideosController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

  public function obtenerVideos($id_concierto) {

    $eval = "SELECT * FROM videoteca WHERE id_concierto=?";

    $peticion = $this->db->prepare($eval);
    $peticion->execute([$id_concierto]);
    $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
    exit(json_encode($resultado));
  }
  
  public function publicarVideo() {
    if (IDUSER){
      $video = json_decode(file_get_contents("php://input"));

      if(!isset($video->titulo) || !isset($video->enlace)) {
        http_response_code(400);
        exit(json_encode(["error" => "No se han enviado todos los parametros"]));
      }
  
      $peticion = $this->db->prepare("INSERT INTO videoteca (titulo,autor,enlace,id_concierto) VALUES (?,?,?,?)");
      $resultado = $peticion->execute([$video->titulo,$video->autor,$video->enlace,$video->id_concierto]);
      http_response_code(201);
      exit(json_encode("Video añadido correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));            
    }
    
  }

  public function eliminarVideo($id) {
    if(empty($id)) {
      http_response_code(400);
      exit(json_encode(["error" => "Peticion mal formada"]));    
    }
    if(IDUSER) {
      $eval = "DELETE FROM videoteca WHERE id=? ";
      $peticion = $this->db->prepare($eval);
      $resultado = $peticion->execute([$id]);
      http_response_code(200);
      exit(json_encode("Video eliminado correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));            
    }
  }

  public function editarVideo() {
    $video = json_decode(file_get_contents("php://input"));
    if(IDUSER) {
      if(!isset($video->id) || !isset($video->titulo) || !isset($video->enlace)) {
        http_response_code(400);
        exit(json_encode(["error" => "No se han enviado todos los parametros"]));
      }
      $eval = "UPDATE videoteca SET titulo=?,autor=?, enlace=?, id_concierto=? WHERE id=?";
      $peticion = $this->db->prepare($eval);
      $resultado = $peticion->execute([$video->titulo,$video->autor,$video->enlace,$video->id_concierto,$video->id]);
      http_response_code(201);
      exit(json_encode("Video actualizado correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));        
    }
  }
}