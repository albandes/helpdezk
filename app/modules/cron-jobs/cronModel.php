<?php

class cronModel extends cronSystem{
    protected $db;
    public function __construct() 
    {
        parent::__construct();

        $db_connect 	= $this->getConfig('db_connect');
        $db_hostname	= $this->getConfig('db_hostname');
        $db_port 		= $this->getConfig('db_port');
        $db_name 		= $this->getConfig('db_name');
        $db_username 	= $this->getConfig('db_username');
        $db_password 	= $this->getConfig('db_password');
        $db_sn 			= $this->getConfig('db_sn');

        if(!$db_connect){
            $this->setConfig("db_connect","mysqlt");
        }
        if ($db_connect == 'oci8po') define('ADODB_ASSOC_CASE', 0); # use  lowercase field names for ADODB_FETCH_ASSOC

        $adodbVersion = $this->getAdoDbVersion();

        if (file_exists( HELPDEZK_PATH . 'includes/adodb/'.$adodbVersion.'/adodb.inc.php' )){
            include HELPDEZK_PATH . 'includes/adodb/'.$adodbVersion.'/adodb.inc.php';
        } else {
            die('Not found: ' . HELPDEZK_PATH . 'includes/adodb/'.$adodbVersion.'/adodb.inc.php');
        }


        $this->db = NewADOConnection($db_connect);

         if (!$this->db->Connect($db_hostname, $db_username, $db_password, $db_name))
             die("<br>Error connecting to database: " . $this->db->ErrorNo() . " - " . $this->db->ErrorMsg());

        //PDO connection
        $dsn = "mysql:host={$db_hostname};dbname={$db_name}";
        $charsetPDO = ($this->getConfig('db_charset') && $this->getConfig('db_charset') != '') ? $this->getConfig('db_charset') : 'utf8';
        
        try{
            $this->dbPDO = new PDO($dsn,$db_username,$db_password);
            //$this->dbPDO->exec("SET NAMES {$charsetPDO}");
            $this->dbPDO->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $ex){
            die("<br>Error connecting to database: " . $ex->getMessage() . " File: " . __FILE__ . " Line: " . __LINE__ );
        }

    }

    public function setLimit($sql, $limit, $offset=0) { 
        $result = $sql." LIMIT $limit OFFSET $offset"; 
        return $result;          
    } 


    public function insert($tabela, Array $dados)
    {
        foreach ($dados as $inds => $vals)  {
            $campos[] = $inds;
            $valores[] = $vals;
        }
        $campos = implode(", ", $campos);
        //$valores = "'".implode("' ,'", $valores)."'";
        $valores = implode(" , ", $valores);
        return $this->db->Execute("INSERT into {$tabela} ({$campos}) values ({$valores})");
    }
	
    public function read(Array $dados, $tabela, $where = null, $limit = null, $offset = null, $orderby = null )
    {
            $where = ($where != null ? "WHERE {$where}" : "");
            $limit = ($limit != null ? "LIMIT {$limit}" : "");
            $offset = ($offset != null ? "OFFSET {$offset}" : "");
            $orderby = ($orderby != null ? "ORDER BY {$orderby}" : "");
            foreach ($dados as $inds => $vals)  {
                $campos[] = $vals;
            }
            $campos = implode(", ", $campos);
            $q = $this->db->Execute("SELECT {$campos} FROM {$tabela} {$where} {$orderby} {$limit} {$offset} ");
            return $q;
    }
	
    public function update($tabela, Array $dados,$where)
    {
        foreach($dados as $inds => $vals){
            $campos[] = "{$inds} = '{$vals}'";   
        }
        $campos = implode(", ", $campos);
        return $this->db->Execute("UPDATE {$tabela} SET {$campos} WHERE {$where}");
    }
    
    public function delete($tabela,$where)
    {
        return $this->db->Execute("DELETE FROM {$tabela} WHERE {$where}");
    }
	
    public function select($sql)
    {
        $this->db->SetFetchMode(ADODB_FETCH_ASSOC); 
        $exec = $this->db->Execute($sql); 
        return $exec;
    }

    public function BeginTrans()
	{
		return $this->db->BeginTrans();         
    }

    public function RollbackTrans()
	{
        return $this->db->RollbackTrans();         
    }

    public function CommitTrans()
	{
        return $this->db->CommitTrans(); 
         
    }


    public function error($sError)
    {
    	$this->db->RollbackTrans(); 
        die($sError);
        return;
    }

    public function TableMaxID($table, $key) 
    {
        $ret = $this->select("select max($key) as total from $table");
        return $ret->fields['total'];
    }

    public function selectPDO($sql){
        try{
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute();
        }catch(PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        $aRet = array();
        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            $aRet[] = $row;
        }

        return array("success"=>true,"message"=>"","data"=>$aRet);
    }
    
    public function insertPDO($obj,$table){
        try{
            $sql = "INSERT INTO {$table} (".implode(',',array_keys((array) $obj)).") VALUES (".implode(',',array_values((array) $obj)).")";
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute();
        }catch(PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO($table));
    }

    public function updatePDO($obj,$cond,$table){
        try{
            foreach($obj as $i=>$v){
                $dados[] = "`$ind` = ". (is_null($v) ? " NULL ": "'{$v}'");
            }

            foreach($cond as $i=>$v){
                $where[] = "`$ind`". (is_null($v) ? " IS NULL ": " = '{$v}'");
            }

            $sql = "UPDATE {$table} SET ".implode(',',$dados)." WHERE ".implode(' AND ',$where);
            $sth = $this->dbPDO->prepare($sql);
            $sth->execute();
        }catch(PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"");
    }

    public function lastPDO($table){
        try{
            $sth = $this->dbPDO->prepare("SELECT DISTINCT LAST_INSERT_ID () `last` FROM {$table}");
            $sth->execute();
            $row = $sth->fetchObject();
        }catch(PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return $row->last;
    }

    public function firstPDO($obj){
        if(isset($obj[0])){
            return $obj[0];
        }else{
            return null;
        }
    }

    public function setObject($obj,$values,$exists=true){
        if(is_object($obj)){
            if(count($values) > 0){
                foreach($values as $i=>$v){
                    if(property_exists($obj,$i) || $exists){
                        $obj->$i = $values->$i;
                    }
                }
            }
        }
    }

    public function BeginTransPDO(){
        return $this->dbPDO->beginTransaction();
    }

    public function RollbackTransPDO(){
        return $this->dbPDO->rollBack();
    }

    public function CommitTransPDO(){
        return $this->dbPDO->commit();
    }


}
