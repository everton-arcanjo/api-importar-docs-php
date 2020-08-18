<?php
namespace App\Controller;
use App\Entity\Documento;
use App\Model\DocumentoModel;

class DocumentoController{

  private $documentoModel;

  public function __construct(){
    $this->documentoModel = new DocumentoModel();
  }
  //POST - Criar um documento manual
  function create($data = null){
    $documento = $this->convertType($data);
    $result = $this->validate($documento);

    if($result != ""){
      return json_encode(["result" => $result]);
    }

    return json_encode(["result" =>$this->documentoModel->create($documento)]);
  }

  //POST - ler um documento
  function readFolder($data = null){

    $documentoUrl = $this->documentoModel->readAllDoc();
    
    $path = 'C:\Users\Naiara\Desktop\doc_api';
    if(!empty($path)){
      $path = 'C:\Users\Naiara\Desktop\doc_api';
    }
    
    foreach(glob($path."\*.txt") as $file){

      $handle = fopen($file, "r");
      
      while (($buffer = fgets($handle, 4096)) !== false) {

        $valor = explode(';', $buffer);
        $valor['empresa'] = $valor[0];
        $valor['chave'] = $valor[1];

      $documento = $this->readById($valor['empresa']);

        if(!empty($documento)){
          $documento = $this->convertType($data);
          $documento->setChave($valor['chave']);
          $documento->setEmpresa($valor['empresa']);
          $documento->setStatus('pendente');

          $this->documentoModel->create($documento);
          
          echo "Dados: Chaves salvas ".$valor['chave']."\n";

        } else{
          //Salvo as informações
          $doc_array = array('empresa'=> $valor['empresa'], 'chave'=>$valor['chave']);

          $this->documentoModel->create($documento);

          $this->create($doc_array);
        }         
      }
      if (!feof($handle)) {
          echo "Erro: Falha inexperada\n";
      }

      fclose($handle);
    }    
  }  

  //PUT - Alterar Doc
  function update($chave = '', $data = null){

    $chaves = $_GET['chave'];
    $status = $_GET['status'];

    if(isset($chaves)){

      $chaves_array = explode(',', $chaves);
      $total_chaves = count($chaves_array);

      if($total_chaves > 20){
        return  json_encode(["error" => 'Limite de chaves na requisição excedido '.$status.' limite de 20 chaves por requisição']);
      } else{

        foreach($chaves_array as $chave){

          $documento = $this->convertType($data);

          $documento->setChave($chave);

          $documento->setStatus('validado');        

          $result = $this->validate($documento, true);

          if($result != ""){

            //return json_encode(["result" => $result]);

            $documento->setStatus('Incorreto');
            $chave_todas[] = array('chave' => $documento->getChave(), 'status' => 'Incorreto');
          } else {

            $this->documentoModel->update($documento);
            $chave_todas[] = array('chave' => $documento->getChave(), 'status' => 'validado');
          }        
        }

        return  json_encode(["result" => $chave_todas]);
      }

    }
  }

  //GET - Retorna um documento pela chave
  function readById($empresa = ''){

    $empresa = filter_var($empresa, FILTER_SANITIZE_NUMBER_INT);

    if(strlen($empresa) < 4 || strlen($empresa) > 4){
      return json_encode(["result" => "invalid empresa"]);
    }

      return $this->documentoModel->readById($empresa);
  }

  //GET - Retorna todos os documento
  function readAll(){
    return $this->documentoModel->readAll();
  }

  private function convertType($data){
    return new Documento(
      null,
      (isset($data['empresa']) ? filter_var($data['empresa'], FILTER_SANITIZE_STRING) : null),
      (isset($data['chave']) ? filter_var($data['chave'], FILTER_SANITIZE_STRING) : null),
      (isset($data['status']) ? filter_var($data['status'], FILTER_SANITIZE_STRING) : null)
    );
  }

  private function validate(Documento $documento, $update = false){


    //$chaves = explode(',', $documento->getChave());

      if(strlen($documento->getChave()) != 44){
        return  "chave inválida";
      }

    return "";
  }

  //Remove letras e caracteres
  public function remove_caracter($str){

    return preg_replace("/[^0-9]/", "", $str); 

  }
  
}
