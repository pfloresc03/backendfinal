<?php

class InstrumentosController {

    private $db = null;
  
    function __construct($conexion) {
      $this->db = $conexion;
    }

    public function obtenerInstrumento($id) {
        if (IDUSER){
    
          $eval = "SELECT * FROM instrumentos WHERE id=? ";
    
          $peticion = $this->db->prepare($eval);
          $peticion->execute([$id]);
          $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
          exit(json_encode($resultado));
        } else{
          http_response_code(401);
          exit(json_encode(["error" => "Fallo de autorizacion"]));  
        }
        
    }

    public function verInstrumentos() {
        if (IDUSER){
    
          $eval = "SELECT * FROM instrumentos ";
    
          $peticion = $this->db->prepare($eval);
          $peticion->execute();
          $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
          exit(json_encode($resultado));
        } else{
          http_response_code(401);
          exit(json_encode(["error" => "Fallo de autorizacion"]));  
        }
        
    }


}
