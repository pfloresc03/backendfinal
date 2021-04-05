<?php

class ConciertosController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

    public function verConciertos() {

      $eval = "SELECT * FROM conciertos ";
      $peticion = $this->db->prepare($eval);
      $peticion->execute();
      $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
      exit(json_encode($resultado));

    }

    public function crearConcierto(){
        if (IDUSER){
          $concierto = json_decode(file_get_contents("php://input"));
          if (!isset($concierto->nombre)){
            http_response_code(400);
            exit(json_encode(["error" => "No se han enviado todos los parametros"]));
          } 
          $eval = "INSERT INTO conciertos(nombre, fecha) VALUES (?,?)";
          $peticion = $this->db->prepare($eval);
          $peticion->execute([$concierto->nombre,$concierto->fecha]);
          http_response_code(201);
          exit(json_encode("concierto creado correctamente"));
        } else {
          http_response_code(401);
          exit(json_encode(["error" => "Fallo de autorizacion"])); 
        }
      }
      
      public function editarConcierto() {
        $concierto = json_decode(file_get_contents("php://input"));
        if(IDUSER) {
          if(!isset($concierto->id) || !isset($concierto->nombre) || !isset($concierto->fecha)) {
            http_response_code(400);
            exit(json_encode(["error" => "No se han enviado todos los parametros"]));
          }
          $eval = "UPDATE conciertos SET nombre=?, fecha=? WHERE id=?";
          $peticion = $this->db->prepare($eval);
          $resultado = $peticion->execute([$concierto->nombre,$concierto->fecha,$concierto->id]);
          http_response_code(201);
          exit(json_encode("concierto actualizado correctamente"));
        } else {
          http_response_code(401);
          exit(json_encode(["error" => "Fallo de autorizacion"]));        
        }
      }
    
      public function eliminarConcierto($id) {
        if(IDUSER) {
            if(empty($id)) {
            http_response_code(400);
            exit(json_encode(["error" => "Peticion mal formada"]));    
            }

            $eval = "DELETE FROM conciertos WHERE id=? ";
            $peticion = $this->db->prepare($eval);
            $resultado = $peticion->execute([$id]);
        
            http_response_code(200);
            exit(json_encode("concierto eliminado correctamente"));
        } else {
            http_response_code(401);
            exit(json_encode(["error" => "Fallo de autorizacion"]));            
        }
      }
}