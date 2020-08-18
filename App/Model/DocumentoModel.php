<?php
namespace App\Model;
use App\Entity\Documento;
use App\Util\Serialize;

class DocumentoModel{
  private $fileName;
  private $listDocumento = [];
  private $fileLog;
  private $listLog = [];  

  public function __construct(){
    $this->fileName = "../database/documento.db";
    $this->fileLog = "../database/log_documento.db";
    $this->load();
  }

  public function readAll(){
    return (new Serialize())->serialize($this->listDocumento);
  }

  public function readAllDoc(){
    return (new Serialize())->serialize($this->listLog);
  }  

  public function readById($empresa){

    foreach($this->listDocumento as $d){

      if($d->getEmpresa() == $empresa){
        return (new Serialize())->serialize($d);
      }
    }

    return json_encode([]);
  }

  public function create(Documento $documento){
    $documento->setId($this->getLastId());

    $this->listDocumento[] = $documento;
    $this->save();

    return "ok";
  }

  public function update(Documento $documento){
    $result = "Não encontrado";

    for($i = 0; $i < count($this->listDocumento); $i++){
      
      if(trim($this->listDocumento[$i]->getChave()) == $documento->getChave()){

        $documento->setId($this->listDocumento[$i]->getId());
        $documento->setEmpresa($this->listDocumento[$i]->getEmpresa());

        $this->listDocumento[$i] = $documento;
        $result = "ok";
      }
    }

    $this->save();

    return $result;
  }

  public function delete($id){
    $result = "Não encontrado";
    for($i = 0; $i < count($this->listDocumento); $i++){
      if($this->listDocumento[$i]->getId() == $id){
        unset($this->listDocumento[$i]);
        $result = "ok";
      }
    }

    $this->listDocumento = array_filter(array_values($this->listDocumento));

    $this->save();
    return $result;
  }
  //Metodo Interno
  private function save(){
    $temp = [];

    foreach($this->listDocumento as $g){
      $temp[]       = [
        "id"        => $g->getId(),
        "empresa"    => $g->getEmpresa(),
        "chave" => $g->getChave(),
        "status"   => $g->getStatus()
      ];

      $fp = fopen($this->fileName, "w");
      fwrite($fp, json_encode($temp));
      fclose($fp);
    }
  }

  private function getLastId(){
    $lastId = 0;

    foreach($this->listDocumento as $d){
      if($d->getId() > $lastId)
      $lastId = $d->getId();
    }

    return ($lastId + 1);
  }

  private function load(){
    if(!file_exists($this->fileName) || filesize($this->fileName) <= 0)
    return [];

    $fp = fopen($this->fileName, "r");
    $str = fread($fp, filesize($this->fileName));
    fclose($fp);

    $arrayDocumento = json_decode($str);

    foreach($arrayDocumento as $d){
      $this->listDocumento[] = new Documento(
        $d->id,
        $d->empresa,
        $d->chave,
        $d->status
      );
    }
  }
}
