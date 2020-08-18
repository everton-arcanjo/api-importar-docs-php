<?php
namespace App\Entity;

class Documento{

	private $id;
	private $empresa;
	private $chave;
	private $status;

	//Constructor de Doc
	public function __construct($id = 0, $empresa = '', $chave = '', $status = ''){
		$this->id = $id;
		$this->empresa = $empresa;
		$this->chave = $chave;
		$this->status = $status;
	}

	//Setters
	public function setId($id){
		$this->id = $id;
	}

	public function setEmpresa($empresa){
		$this->empresa = $empresa;
	}

	public function setChave($chave){
		$this->chave = $chave;
	}

	public function setStatus($status){
		$this->status = $status;
	}

	//Getter
	public function getId(){
		return $this->id;
	}

	public function getEmpresa(){
		return $this->empresa;
	}

	public function getChave(){
		return $this->chave;
	}

	public function getStatus(){
		return $this->status;
	}
}
