<?php
class db{
  private $host = "localhost";
  private $database = "back";
  private $user = "root";
  private $password = "";
  private $dbPDO;
  private $JWTkey = "ClaveSuperSecreta";

  public function getConnection() {
    $this->dbPDO = null;
    try{
      $this->dbPDO = new PDO('mysql:host='.$this->host.';dbname='.$this->database,$this->user,$this->password);
    } catch(PDOException $exception) {
      echo "Conexión Fallida: " . $exception->getMessage();
    }
    return $this->dbPDO;
  }

  public function getClave() {
    return $this->JWTkey;
  }
}
