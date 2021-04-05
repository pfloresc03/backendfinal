<?php

class PartiturasController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

  public function obtenerPartituras($id_obra) {
    if (IDUSER){

      $eval = "SELECT * FROM partituras WHERE id_obra=? ";

      $peticion = $this->db->prepare($eval);
      $peticion->execute([$id_obra]);
      $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
      exit(json_encode($resultado));
    } else{
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));  
    }
    
  }

  public function subirArchivo($id_obra, $id_inst, $voz) {
    if(empty($id_obra)) {
      http_response_code(500);
      exit(json_encode(["error" => "id_obra vacio"]));
    }
    if(is_null(IDUSER)){
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));
    }
    if(isset($_FILES['partitura'])) {
      $partitura = $_FILES['partitura'];
      $mime = $partitura['type'];
      $size = $partitura['size'];
      $rutaTemp = $partitura['tmp_name'];
      $nombre = $partitura['name'];
  
      //Comprobamos que la partitura sea JPEG o PNG y que el tamaño sea menor que 4000KB.
      if( !(strpos($mime, "pdf")) || ($size > 4000000) ) {
        http_response_code(400);
        exit(json_encode(["error" => "La partitura tiene que ser PDF y no puede ocupar mas de 4MB"]));
      } else {
  
        //Ruta donde se guardarán los archivos.
        
        $ruta = ROOT."partituras/".$nombre;
        
        //Si se guarda la partitura correctamente actualiza la ruta en la tabla usuarios
        if(move_uploaded_file($rutaTemp,$ruta)) {
  
          //Prepara el contenido del campo imgSrc
          $archivo = "http://localhost/backendfinal/partituras/".$nombre;
  
          $eval = "INSERT INTO partituras (archivo, nombre, id_obra, id_instrumento, voz) VALUES (?,?,?,?,?)";
          $peticion = $this->db->prepare($eval);
          $peticion->execute([$archivo,$nombre,$id_obra,$id_inst,$voz]);
  
          http_response_code(201);
          exit(json_encode("Partitura subida correctamente"));
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

  public function editarPartitura() {
    $partitura = json_decode(file_get_contents("php://input"));
    if(IDUSER) {
      if(!isset($partitura->id_partitura) || !isset($partitura->id_instrumento) || !isset($partitura->id_voz)) {
        http_response_code(400);
        exit(json_encode(["error" => "No se han enviado todos los parametros"]));
      }
      $eval = "UPDATE partituras SET id_instrumento=?, voz=? WHERE id=? ";
      $peticion = $this->db->prepare($eval);
      $resultado = $peticion->execute([$partitura->id_instrumento,$partitura->id_voz,$partitura->id_partitura]);
      http_response_code(201);
      exit(json_encode("Partitura actualizada correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));        
    }
  }

  public function eliminarPartitura($id) {
    if(empty($id)) {
      http_response_code(400);
      exit(json_encode(["error" => "Peticion mal formada"]));    
    }
    if(IDUSER) {
      $borra = "SELECT nombre FROM partituras WHERE id=?";
      $peticion = $this->db->prepare($borra);
      $peticion->execute([$id]);
      $resultado = $peticion->fetch();
      
      $ruta = ROOT."partituras/".$resultado[0];
      unlink($ruta);

      $eval = "DELETE FROM partituras WHERE id=?";
      $peticion = $this->db->prepare($eval);
      $resultado = $peticion->execute([$id]);

      http_response_code(200);
      exit(json_encode("Partitura eliminada correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));            
    }
  }
}