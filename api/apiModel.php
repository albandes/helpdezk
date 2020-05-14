<?php

class apiModel extends apiSystem{
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
        //$db_sn 			= $this->getConfig('db_sn');

        if(!$db_connect){
            $this->setConfig("db_connect","mysqli");
        }

        $adodbVersion = $this->getAdoDbVersion();
        include HELPDEZK_PATH . '/includes/adodb/'.$adodbVersion.'/adodb.inc.php';

        $this->db = NewADOConnection($db_connect);

        $this->db->Connect($db_hostname, $db_username, $db_password, $db_name);


    }

    // new
    public function isRequest($code)
    {
        $ret = $this->db->Execute( "SELECT   idrequest FROM   hdk_tbrequest WHERE code_request = '$code'" );
        if ($ret->RecordCount()== 0) {
            return false;
        } else {
            return true;
        }
    }
    // new
    public function fixTokenColumnTbPerson()
    {

        return $this->db->execute("ALTER TABLE tbperson MODIFY token VARCHAR (255)");

    }
    //new
    public function dbError($file,$line,$method,$error,$query='')
    {
        if(empty($query))
            $sql = '';
        else
            $sql = ' , query : ' . $query;

        $file = str_replace($this->_helpdezkPath,'',$file);

        echo "[DB Error] File: " . $file . " , method: " . $method . ", line: " . $line . ", Db message: " . $error . $sql;

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

    public function TableMaxID($table, $key) 
    {
        $ret = $this->select("select max($key) as total from $table");
        return $ret->fields['total'];
    }



}
