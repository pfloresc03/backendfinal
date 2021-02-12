<?php

class NotasController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

  public function obtenerNotas() {
    $busqueda = null;
    if(!empty($_GET["busqueda"])) $busqueda = $_GET["busqueda"];

    $eval = "SELECT * FROM notas WHERE (idUser IS NULL";
    $eval .= (IDUSER ? " OR idUser = ". IDUSER : null).")";
    $eval .= $busqueda ? " AND CONCAT_WS('', titulo,contenido) LIKE '%".$busqueda."%'" : null;

    $peticion = $this->db->prepare($eval);
    $peticion->execute();
    $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
    exit(json_encode($resultado));
  }
  
  public function publicarNota() {
    $nota = json_decode(file_get_contents("php://input"));

    if(!isset($nota->titulo) || !isset($nota->contenido)) {
      http_response_code(400);
      exit(json_encode(["error" => "No se han enviado todos los parametros"]));
    }

    $peticion = $this->db->prepare("INSERT INTO notas (titulo,contenido,idUser) VALUES (?,?,?)");
    $resultado = $peticion->execute([$nota->titulo,$nota->contenido,IDUSER]);
    http_response_code(201);
    exit(json_encode("Nota creada correctamente"));
  }

  public function editarNota() {
    $nota = json_decode(file_get_contents("php://input"));
    if(IDUSER) {
      if(!isset($nota->id) || !isset($nota->titulo) || !isset($nota->contenido)) {
        http_response_code(400);
        exit(json_encode(["error" => "No se han enviado todos los parametros"]));
      }
      $eval = "UPDATE notas SET titulo=?, contenido=? WHERE id=? AND idUser=?";
      $peticion = $this->db->prepare($eval);
      $resultado = $peticion->execute([$nota->titulo,$nota->contenido,$nota->id,IDUSER]);
      http_response_code(201);
      exit(json_encode("Nota actualizada correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));        
    }
  }

  public function eliminarNota($id) {
    if(empty($id)) {
      http_response_code(400);
      exit(json_encode(["error" => "Peticion mal formada"]));    
    }
    if(IDUSER) {
      $eval = "DELETE FROM notas WHERE id=? AND idUser=?";
      $peticion = $this->db->prepare($eval);
      $resultado = $peticion->execute([$id,IDUSER]);
      http_response_code(200);
      exit(json_encode("Nota eliminada correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));            
    }
  }
}