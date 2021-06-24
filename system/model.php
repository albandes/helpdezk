<?php

class Model extends System{
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
        	$this->setConfig("db_connect","mysqli");
        }
        if ($db_connect == 'oci8po') define('ADODB_ASSOC_CASE', 0); # use  lowercase field names for ADODB_FETCH_ASSOC

		//include 'includes/adodb/adodb.inc.php';

        $adodbVersion = $this->getAdoDbVersion();
        include 'includes/adodb/'.$adodbVersion.'/adodb.inc.php';

		$this->db = NewADOConnection($db_connect);

		if($db_connect == 'mysqli'){
			if (!$this->db->Connect($db_hostname, $db_username, $db_password, $db_name)) {
				die("<br>Error connecting to database: " . $this->db->ErrorNo() . " - " . $this->db->ErrorMsg() . " File: " . __FILE__ . " Line: " . __LINE__ );
			}
		}
		elseif ($db_connect == 'oci8po'){
			$ora_db = "
						(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP) (HOST=".$db_hostname.")(PORT=".$db_port.")))
						(CONNECT_DATA=(SERVICE_NAME=".$db_sn.")))
					"; 
			if (!$this->db->Connect($ora_db, $db_username, $db_password)){
				die("<br>Error connecting to database: " . $this->db->ErrorNo() . " - " . $this->db->ErrorMsg() );
			}		
		}

        //PDO connection
        $dsn = "mysql:host={$db_hostname};dbname={$db_name}";
        $charsetPDO = ($this->getConfig('db_charset') && $this->getConfig('db_charset') != '') ? $this->getConfig('db_charset') : 'utf8';
        
        try{
            $this->dbPDO = new PDO($dsn,$db_username,$db_password);
            $this->dbPDO->exec("set names",$charsetPDO);
            $this->dbPDO->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $ex){
            die("<br>Error connecting to database: " . $ex->getMessage() . " File: " . __FILE__ . " Line: " . __LINE__ );
        }


    }

    public function setLimit($sql, $limit, $offset=0) { 
        $result = $sql." LIMIT $limit OFFSET $offset"; 
        return $result;          
    } 


    public function setLimitOracle($sql, $limit, $offset=0) { 
        $max = $offset + $limit; 
        $sql = "SELECT
                    *
                    FROM
                        (SELECT ROWNUM as num_line, T.* FROM ($sql) T WHERE ROWNUM <= $max)
                    WHERE num_line > $offset";
        return $sql;
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


    /**
     * Return if table exists in database
     *
     * The pipeTableExists function was previously used, however in some versions of mysql case sensitive problems
     * were reported in the function names. Then, we replace it with a query.
     *
     * @param string $tableName Table name
     * @return bool  true|false
     *
     * @since 1.1.10
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function tableExists($tableName)
    {


        $database = $this->getConfig('db_name');

        $query = "
                SELECT 
                  COUNT(*) as exist
                 FROM
                  information_schema.tables 
                WHERE table_schema = '$database' 
                  AND table_name = '$tableName' ;
                 ";

        $rs = $this->select($query);
        return $rs->fields['exist'];
    }

    public function dbError($file,$line,$method,$error,$query='')
    {
        if(empty($query))
            $sql = '';
        else
            $sql = ' , query : ' . $query;

        $file = str_replace($this->helpdezkPath,'',$file);

        echo "[DB Error] File: " . $file . " , method: " . $method . ", line: " . $line . ", Db message: " . $error . $sql;

    }

    public function TableMaxID($table, $key) 
    {
        $ret = $this->select("select max($key) as total from $table");
        return $ret->fields['total'];
    }

    public function saveEmailCron($idmodule,$code,$tag)
    {
        $sql =    "INSERT INTO tbemailcron(idmodule,code,date_in,tag,send)
                    VALUES ($idmodule,'$code',NOW(),'$tag',0);
                    ";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function getEmailCron($where)
    {
        $sql = "SELECT idemailcron, idmodule, code, date_in, date_out, send, tag
                    FROM tbemailcron
                    $where";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateEmailCron($set)
    {
        $sql = "UPDATE tbemailcron $set";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    /**
     * Returns the inserted, updated and deleted data to be saved into tblog table
     * 
     * @param  string $table    Table name from where to get data
     * @param  mixed $fields    Fields list (can be an array or string)
     * @param  string $where    Data query filters
     * @return array            
     */
    public function getDataLog($table,$fields,$where){
        if(is_array($fields) || is_string($fields)){
            $fields = is_array($fields) ? implode(',',$fields) : $fields;
        }else{
            $fields = "idlog";
        }
        
        $sql = "SELECT {$fields} FROM {$table} {$where}";
        
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }
    
    /**
     * Insert data into tblog table
     * 
     * @param  int $idprogram       Program ID where data was inserted, changed or deleted
     * @param  int $iduser          User ID who entered, changed or deleted the data
     * @param  string $tag          Operation performed           
     * @param  mixed $dataLog       Data
     * @return array
     */
    public function insertLog($idprogram,$iduser,$tag,$dataLog){
        
        $sql = "INSERT INTO tblog (idprogram,idperson,tag,dtlog,datalog)
                VALUES ($idprogram,$iduser,'$tag',NOW(),'$dataLog')";        
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->insert_Id());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
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
