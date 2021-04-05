<?php

class InstrumentosController {

    private $db = null;
  
    function __construct($conexion) {
      $this->db = $conexion;
    }

    public function obtenerPartituras($id) {
        if (IDUSER){
    
          $eval = "SELECT p.id,p.archivo,p.nombre,p.id_obra,p.id_instrumento,p.voz,i.nombreInst FROM partituras p,instrumentos i WHERE p.id_instrumento=i.id && p.id_obra=?";
    
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
