<?php

/**
 ** Use: include_once("ProtectSql.php");
 **      $ProtectSql=new sqlinj;
 **
 ** Add custom words to list.
 ** $ProtectSql->add("into");
 **	
 ** Only $_GET["variable"] protect    
 ** $ProtectSql->start("get","variable"); 
 **
 ** aio -> $_REQUEST,$_GET,$_POST protect all types --- all -> all variables
 ** $ProtectSql->start("aio","all");
 **
**/

class sqlinj{
	private $gerideger;
	private $islet;
	public $liste=array('declare ','char ','set ','cast ','convert ','drop ','exec ','meta ','script ','select ','truncate ','insert ','delete ','union ','update ','create ','where ','join ','information_schema ','table_schema ','into ');
	private $specialfind=array('\'','"','-','*','=');
	private $specialreplace=array('&#39;','&#34;','&#45;','&#42;','&#61;');
	public function clean($find){
		return str_replace($this->specialfind,$this->specialreplace,$find);
	}
	public function start($veri,$tur='normal'){
		if($tur=='normal'){
			return self::normal(self::clean($veri));
		}elseif($tur=='all'){
			return self::tumsorgular($veri);
		}else{
			return self::req($tur,$veri);
		}
	}
	private function normal($deger){
		foreach($this->liste as $bul){
			$deger=str_replace($bul,'SQL_INJECT['.$bul.']',$deger);
			
		}
		return $deger;
	}
	private function tumsorgular($yapilacak){
			switch ($yapilacak){
			case 'post':
				$this->islet=array('POST');
			break;
			case 'get':
				$this->islet=array('GET');
			break;
			case 'request':
				$this->islet=array('REQUEST');
			break;
			case 'aio':
				$this->islet=array('POST','GET','REQUEST');
			break;
		}	
		foreach($this->islet as $islem){
			eval('foreach($_'.$islem.' as $ad=>$deger)
			{
				$_'.$islem.'[$ad]=self::clean($deger);
				foreach($this->liste as $bul){
				$_'.$islem.'[$ad]=str_ireplace($bul,"SQL_INJECT[".$bul."]",$_'.$islem.'[$ad]);
				}
			}
		return $_'.$islem.';');
		}
	}
	private function req($deger,$method){
		switch ($method){
			case 'post':
			$this->islet=self::clean($_POST[$deger]);
			break;
			case 'get':
			$this->islet=self::clean($_GET[$deger]);
			break;
			case 'request':
			$this->islet=self::clean($_REQUEST[$deger]);
			break;
		}	
		foreach($this->liste as $bul){
			$this->islet=str_ireplace($bul,'SQL_INJECT['.$bul.']',$this->islet);
			
		}
		return $this->islet;	
	}
	public function add($eklenecek){
		$this->liste[]=$eklenecek;
	}
}

?>
