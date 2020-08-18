<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once("../../vendor/autoload.php");
use App\Controller\DocumentoController;

$controller = null;
$chave      = null;
$id         = null;
$chave      = null;
$empresa      = null;
$listar       = null;

$method     = $_SERVER["REQUEST_METHOD"]; //POST, PUT, DELETE and GET
$uri = $_SERVER["REQUEST_URI"];
$data       = null;
parse_str(file_get_contents('php://input'), $data);

$unsetCount = 3;
//TRATA A URI
$ex = explode("/", $uri);
for($i = 0; $i < $unsetCount; $i++){
  unset($ex[$i]);
}

$ex = array_filter(array_values($ex));
if(isset($ex[0])){
  $controller = $ex[0];
}

if(isset($ex[1])){
  $id = $ex[1];
}

if(isset($ex[1])){
  $chave = $ex[1];
}

if(isset($ex[1])){
  $empresa = $ex[1];
}

if(isset($ex[1])){
  $listar = $ex[1];
}

//FIM TRATA A URI
$documentoController = new DocumentoController();

switch($method) {
  case 'GET':
  if($controller != null && $empresa == null){
    echo $documentoController->readFolder();
        
  }elseif($controller != null && $listar != null && $listar == 'documento'){
    echo $documentoController->readAll();
  }elseif($controller != null && $empresa != null){
    echo $documentoController->readById($empresa);
  }else{
    echo json_encode(["result" => "inválido"]);
  }
  break;

  case 'POST':
  if($controller != null && $chave == null){
    echo $documentoController->create($data);
    echo $documentoController->readById($empresa);
  }else{
    echo json_encode(["result" => "inválido"]);
  }
  break;

  case 'PUT':
  if($controller != null && $chave != null){
    echo $documentoController->update($chave, $data);
  }else{
    echo json_encode(["result" => "Chave inválida"]);
  }
  break;

  case 'DELETE':
    if($controller != null && $chave != null){
      echo $documentoController->delete($chave);
    }else{
      echo json_encode(["result" => "inválido"]);
    }
  break;

  default:
    echo json_encode(["result" => "request inválida"]);
  break;
}

?>
