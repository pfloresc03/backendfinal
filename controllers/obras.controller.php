<?php

class ObrasController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

  public function leerObras() {
    if(IDUSER){
      $eval = "SELECT * FROM obras";
      $peticion = $this->db->prepare($eval);
      $peticion->execute();
      $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
      exit(json_encode($resultado));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"])); 
    }
    
  }

  public function crearObra(){
    if (IDUSER){
      $obra = json_decode(file_get_contents("php://input"));
      if (!isset($obra->nombre)){
        http_response_code(400);
        exit(json_encode(["error" => "No se han enviado todos los parametros"]));
      } 
      $eval = "INSERT INTO obras(nombre) VALUES (?)";
      $peticion = $this->db->prepare($eval);
      $peticion->execute([$obra->nombre]);
      http_response_code(201);
      exit(json_encode("Obra creada correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"])); 
    }
  }
  
  

  public function eliminarObra($id) {
    if(empty($id)) {
      http_response_code(400);
      exit(json_encode(["error" => "Peticion mal formada"]));    
    }
    if(IDUSER) {
      $eval = "DELETE FROM obras WHERE id=? ";
      $peticion = $this->db->prepare($eval);
      $resultado = $peticion->execute([$id]);
      http_response_code(200);
      exit(json_encode("Obra eliminada correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));            
    }
  }
}