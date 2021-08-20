<?php

if(class_exists('Model')) {
    class DynamicProgram_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicProgram_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicProgram_model extends apiModel {}
}

class programs_model extends DynamicProgram_model {
    public function insertProgram($name,$controller,$smarty,$idpc){
        $query = "INSERT INTO tbprogram (name,controller,smarty,idprogramcategory,status) VALUES('$name','$controller','$smarty','$idpc','A')";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function selectProgram($where = NULL, $order = NULL, $limit = NULL){
        $database = $this->getConfig('db_connect');

        if ($database == 'mysqli') {
            $query = "SELECT tbp.idprogram, tbp.name, tbp.controller, tbm.name as module, tbp.status, tbtp.name as category, tbp.smarty from tbprogram as tbp, tbmodule as tbm, tbprogramcategory as tbtp where tbtp.idmodule = tbm.idmodule and tbtp.idprogramcategory = tbp.idprogramcategory $where $order $limit";
        } elseif ($database == 'oci8po') {
            $limit = str_replace('LIMIT', "", $limit);
            $p     = explode(",", $limit);
            $start = $p[0]+1; 
            $end   = $p[0]+$p[1]; 
            $core  = "
                        SELECT   tbp.idprogram,
                                 tbp.name,
                                 tbp.controller,
                                 tbm.name AS module,
                                 tbp.status,
                                 tbtp.name AS category,
                                 tbp.smarty
                        FROM   tbprogram tbp, tbmodule tbm, tbprogramcategory tbtp
                        WHERE   tbtp.idmodule = tbm.idmodule
                                 AND tbtp.idprogramcategory = tbp.idprogramcategory
                        $where $order         
                        ";
            $query =    "
                        SELECT   *
                          FROM   (SELECT                                          
                                        a  .*, ROWNUM rnum
                                    FROM   (  
                                              
                                            $core 

                                            ) a
                                   WHERE   ROWNUM <= $end)
                         WHERE   rnum >= $start         
                        ";
        }

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
            return $ret;

    }

    public function countProgram($where = NULL, $order = NULL, $limit = NULL){
        $query = "SELECT count(idprogram) as total from tbprogram tbp, tbmodule tbm, tbprogramcategory tbtp where tbtp.idmodule = tbm.idmodule and tbtp.idprogramcategory = tbp.idprogramcategory $where";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function selectModules($where=null,$order=null){
        $query = "SELECT idmodule, name FROM tbmodule $where $order";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function selectCategory($id){
        $query = "SELECT idprogramcategory, name FROM tbprogramcategory WHERE idmodule = $id";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function countCategory($id){
        return $this->select("SELECT count(idprogramcategory) from tbprogramcategory where idprogramcategory = $id");
    }

    public function categoryInsert($name,$module,$smarty){
       $query = "INSERT INTO tbprogramcategory (`name`,idmodule,smarty) VALUES ('$name',$module,'$smarty')";
        
       $ret = $this->db->Execute($query);

       if (!$ret) {
           $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
           $this->error($sError);
           return false;
       }
       
       return $ret;
    }

    public function lastIdCategory(){
        $query = "SELECT MAX(idprogramcategory) AS last FROM tbprogramcategory";
            
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        
        return $ret->fields['last'];
    }

    public function selectProgramData($id){
        $query = "SELECT name, controller, smarty, idprogramcategory FROM tbprogram WHERE idprogram='$id'";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function selectProgramModule($cat){
        $query = "SELECT idmodule, name FROM tbprogramcategory WHERE idprogramcategory='$cat'";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function updateProgram($id,$name,$controller,$smarty,$category){
        
        $query = "UPDATE tbprogram SET name = '$name', controller = '$controller', smarty = '$smarty', idprogramcategory='$category' WHERE idprogram='$id'";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function selectProgramID($name, $idc, $controller){
        
        $query = "SELECT idprogram FROM tbprogram WHERE name = '$name' AND controller = '$controller' AND idprogramcategory='$idc'";
        $ret = $this->db->Execute($query);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        
        return $ret->fields['idprogram'];        
    }

    public function insertDefaultPermission($idprogram, $idaccess, $allow){
        $query = "INSERT INTO tbdefaultpermission (idprogram, idaccesstype, allow) VALUES ('$idprogram','$idaccess','$allow')";
        $ret = $this->db->Execute($query);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function getDefaultPermission($idprogram){
        $query = "SELECT iddefaultpermission,idaccesstype FROM tbdefaultpermission WHERE idprogram = $idprogram";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function clearDefaultPerm($idprogram){
        $query = "DELETE FROM tbdefaultpermission WHERE idprogram = $idprogram";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }    
    
    public function countTypePerson(){
        $query = "SELECT count(idtypeperson) AS count FROM tbtypeperson";
        $ret = $this->db->Execute($query);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        
        return $ret->fields['count'];
    }

    public function insertGroupPermission($idprogram,$idtype,$idaccess){
        $query = "INSERT INTO tbtypepersonpermission (idprogram,idtypeperson,idaccesstype,allow) VALUES ('$idprogram','$idtype','$idaccess','N')";
        $ret = $this->db->Execute($query);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }
    
	public function clearGroupPerm($idprogram){
        $query = "DELETE FROM tbtypepersonpermission WHERE idprogram = $idprogram";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function getTypePerson(){
        $query = "SELECT idtypeperson, name AS count FROM tbtypeperson";
        $ret = $this->db->Execute($query);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function changeProgramStatus($id,$newStatus){
        $query = "UPDATE tbprogram SET status = '$newStatus' WHERE idprogram = $id";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
     }

    /**
     * Returns active modules
     * @return object   	Returns a recordset of active modules
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     *
     */
    public function getModulesCategoryAtive($idperson,$idmodule,$cond=null)
    {
        

        $sql = "(SELECT
                        DISTINCT cat.name              AS category,
                        cat.idprogramcategory 	AS category_id,
                        cat.smarty 		AS cat_smarty
                   FROM tbperson  p,
                        tbtypepersonpermission  g,
                        tbaccesstype  a,
                        tbprogram  pr,
                        tbmodule  m,
                        tbprogramcategory  cat,
                        tbtypeperson  tp
                  WHERE g.idaccesstype = a.idaccesstype
                    AND g.idprogram = pr.idprogram
                    AND m.idmodule = cat.idmodule
                    AND cat.idprogramcategory = pr.idprogramcategory
                    AND tp.idtypeperson = g.idtypeperson
                    AND m.status = 'A'
                    AND pr.status = 'A'
                    AND p.idperson = '$idperson'
                   $cond
                    AND g.idaccesstype = '1'
                    AND g.allow = 'Y'
                    AND m.idmodule = $idmodule
                    )
                  UNION
                    (
                 SELECT
                        DISTINCT cat.name              	AS category,
                        cat.idprogramcategory 	AS category_id,
                        cat.smarty 		AS cat_smarty
                   FROM tbperson  per,
                        tbpermission  p,
                        tbprogram  pr,
                        tbmodule  m,
                        tbprogramcategory  cat,
                        tbaccesstype  acc
                  WHERE m.idmodule = cat.idmodule
                    AND pr.idprogramcategory = cat.idprogramcategory
                    AND per.idperson = p.idperson
                    AND pr.idprogram = p.idprogram
                    AND m.status = 'A'
                    AND pr.status = 'A'
                    AND p.idperson = '$idperson'
                    AND p.idaccesstype = acc.idaccesstype
                    AND p.idaccesstype = '1'
                    AND p.allow = 'Y'
                    AND m.idmodule = $idmodule
                    )";
        $rs = $this->select($sql);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $rs;
    }

    /**
     * Returns a recordset with the permission access per group
     * @param  int      $idperson   User id
     * @param  int      $type       Id of user type
     * @return object   Returns a recordset with the permission access per group
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     *
     */
    /*
    public function selectGroupPermissionMenu($idperson, $idmodule)
    {
        $sql =						"
                                    select
                                      m.idmodule           as idmodule_pai,
                                      m.name               as module,
                                      cat.idmodule          as idmodule_origem,
                                      cat.name              as category,
                                      cat.idprogramcategory as category_pai,
                                      cat.smarty as cat_smarty,
                                      pr.idprogramcategory  as idcategory_origem,
                                      pr.name               as program,
                                      pr.controller         as controller,
                                      pr.smarty  as pr_smarty,
                                      pr.idprogram          as idprogram,
                                      a.idaccesstype    as permission,
                                      g.allow
                                    from tbperson  p,
                                      tbtypepersonpermission  g,
                                      tbaccesstype  a,
                                      tbprogram  pr,
                                      tbmodule  m,
                                      tbprogramcategory  cat,
                                      tbtypeperson  tp
                                    WHERE g.idaccesstype = a.idaccesstype
                                        and g.idprogram = pr.idprogram
                                        and m.idmodule = cat.idmodule
                                        and cat.idprogramcategory = pr.idprogramcategory
                                        and tp.idtypeperson = g.idtypeperson
                                        AND m.status = 'A'
                                        AND pr.status = 'A'
                                        AND p.idperson = '$idperson'
                                        AND pr.idprogramcategory IN
                                          (SELECT
                                            idprogramcategory
                                          FROM
                                            tbprogramcategory
                                          WHERE idmodule = $idmodule)
                                        AND tp.idtypeperson IN (
                                                                SELECT
                                                                   idtypeperson
                                                                FROM
                                                                   tbperson
                                                                WHERE idperson = $idperson
                                                                UNION
                                                                SELECT
                                                                   idtypeperson
                                                                FROM
                                                                   tbpersonmodule
                                                                WHERE idperson = $idperson
                                        )
                                        AND g.idaccesstype = '1'
                                        AND g.allow = 'Y'
                                        ";

        // Old version :  AND tp.idtypeperson = '$type'
        $rsGroupperm = $this->select($sql);
        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
        } else {
            if ($rsGroupperm->fields['idmodule_pai'])
                return $rsGroupperm;
        }


    }

    */
    public function getPermissionMenu($idperson,  $andModule, $cond=null)
    {
        
        $sql = "
                (
                SELECT
                    m.idmodule           	as idmodule_pai,
                    m.name               	as module,
                    m.path				as path,
                    cat.idmodule          as idmodule_origem,
                    cat.name              as category,
                    cat.idprogramcategory as category_pai,
                    cat.smarty 			as cat_smarty,
                    pr.idprogramcategory  as idcategory_origem,
                    pr.name               as program,
                    pr.controller         as controller,
                    pr.smarty  			as pr_smarty,
                    pr.idprogram          as idprogram,
                    g.allow
                FROM tbperson  p,
                    tbtypepersonpermission  g,
                    tbaccesstype  a,
                    tbprogram  pr,
                    tbmodule  m,
                    tbprogramcategory  cat,
                    tbtypeperson  tp
                WHERE g.idaccesstype = a.idaccesstype
                  AND g.idprogram = pr.idprogram
                  AND m.idmodule = cat.idmodule
                    and cat.idprogramcategory = pr.idprogramcategory
                    and tp.idtypeperson = g.idtypeperson
                    AND m.status = 'A'
                    AND pr.status = 'A'
                    AND p.idperson = '$idperson'
                    $cond
                    AND g.idaccesstype = '1'
                    AND g.allow = 'Y'
                    AND $andModule
                )
                UNION
                (
                    select
                        m.idmodule           	as idmodule_pai,
                        m.name               	as module,
                        m.path				as path,
                        cat.idmodule          as idmodule_origem,
                        cat.name              as category,
                        cat.idprogramcategory as category_pai,
                        cat.smarty 			as cat_smarty,
                        pr.idprogramcategory  as idcategory_origem,
                        pr.name               as program,
                        pr.controller         as controller,
                        pr.smarty  			as pr_smarty,
                        pr.idprogram          as idprogram,
                        p.allow
                    from tbperson  per,
                        tbpermission  p,
                        tbprogram  pr,
                        tbmodule  m,
                        tbprogramcategory  cat,
                        tbaccesstype  acc
                    where m.idmodule = cat.idmodule
                        and pr.idprogramcategory = cat.idprogramcategory
                        and per.idperson = p.idperson
                        AND pr.idprogram = p.idprogram
                        and m.status = 'A'
                        and pr.status = 'A'
                        AND p.idperson = '$idperson'
                        AND p.idaccesstype = acc.idaccesstype
                        AND p.idaccesstype = '1'
                        AND $andModule
                )
                ";
        //die($sql);
        $rsGroupperm = $this->select($sql);
        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
        } else {
            if ($rsGroupperm->fields['idmodule_pai'])
                return $rsGroupperm;
        }


    }

    public function getProgramCategory($where=NULL,$order=NULL,$limit=NULL){
        $query = "SELECT idprogramcategory, name FROM tbprogramcategory $where $order $limit";
        
        $ret = $this->db->Execute($query);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');
    }

}

?>
