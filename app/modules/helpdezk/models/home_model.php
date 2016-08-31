<?php
class home_model extends Model{
    public $database;

    public function __construct(){
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function selectUserLogin($cod_usu){
        $ret = $this->select("select login from tbperson where idperson = $cod_usu");
        $nom = $ret->fields['login'];
        return $nom;
    }
	
	public function getChangePass($cod_usu){
        $ret = $this->select("select change_pass from tbperson where idperson = $cod_usu");
        $change_pass = $ret->fields['change_pass'];
        return $change_pass;
    }
    
    public function selectMenu(){
        return $this->select("select tbm.idmodule as idmodule_pai, tbm.name as module, tbpc.idmodule as idmodule_origem, tbpc.name as category, 
            tbpc.idprogramcategory as category_pai, tbp.idprogramcategory as idcategory_origem, tbp.name as program,   tbp.controller as controller,
            tbp.idprogram as idprogram from tbmodule tbm, tbprogramcategory tbpc, tbprogram tbp where tbm.idmodule=tbpc.idmodule and 
            tbp.idprogramcategory=tbpc.idprogramcategory and tbp.status='A' order by idmodule_pai");        
    }
	
	public function foundRows(){
		return $this->select("SELECT FOUND_ROWS() AS `found_rows`");
	}
	
	public function showAdmBtn($idperson, $idtypeperson){	   
       if($this->database == 'oci8po'){
            return $this->select("SELECT 
                                (SELECT COUNT(*) FROM tbtypepersonpermission WHERE idtypeperson = $idtypeperson AND allow = 'Y') AS total_typeperson,
                                (SELECT COUNT(*) FROM tbpermission WHERE idperson = $idperson) as total_person FROM dual");
       }elseif($this->database == 'mysqlt'){
            return $this->select("SELECT 
                                (SELECT COUNT(*) FROM tbtypepersonpermission WHERE idtypeperson = $idtypeperson AND allow = 'Y') AS total_typeperson,
                                (SELECT COUNT(*) FROM tbpermission WHERE idperson = $idperson) as total_person");
       }

    }

    /**
     * returns the value of the config in hdk_tbconfig
     * use when session isn´t seted
     *
     * @param  string $name        Session´s name
     * @return string              Value of session
     * @since Version 1.2
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */

    public function getConfigValue($name){
        $ret = $this->select("select value from hdk_tbconfig where session_name = '$name' ");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['value'];
    }

    public function getNumberPermPersonModule($idperson, $idmodule)
    {
        $ret = $this->select("
                                SELECT
                                   COUNT(idpermissiongroup) AS total
                                FROM
                                   tbtypepersonpermission
                                WHERE idprogram IN
                                   (SELECT
                                      b.idprogram
                                   FROM
                                      tbprogramcategory a,
                                      tbprogram b
                                   WHERE idmodule = $idmodule
                                      AND b.idprogramcategory = a.idprogramcategory)
                                   AND idtypeperson =
                                   (SELECT
                                      idtypeperson
                                   FROM
                                      tbpersonmodule
                                   WHERE idperson = $idperson
                                      AND idmodule = $idmodule)
                                   AND allow = 'Y'
                                ");
            return $ret->fields['total'];
    }

}
