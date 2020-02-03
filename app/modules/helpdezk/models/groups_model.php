<?php
if(class_exists('Model')) {
    class DynamicGroups_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicGroups_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicGroups_model extends apiModel {}
}

class groups_model extends DynamicGroups_model {

    public function selectCorporations() {
        return $this->db->Execute("SELECT idperson,name from tbperson where idtypeperson = 4");
    }

    public function insertGroup($idperson, $level, $costumer, $repass) {
        $query = "INSERT INTO hdk_tbgroup (idperson,`level`,idcustomer,repass_only) 
                   VALUES ($idperson,$level,$costumer,'$repass')";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectGroup($where = NULL, $order = NULL, $limit = NULL) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqli') {
            $query = "SELECT
                             tbg.idgroup,
                             tbp.name ,
                             tbg.idperson,
                             tbg.level as lvl,
                             tbg.status,
                             tbp2.name   as company
                        FROM hdk_tbgroup tbg,
                             tbperson tbp,
                             tbperson tbp2
                       WHERE tbg.idperson = tbp.idperson
                         AND tbp2.idperson = tbg.idcustomer
                      $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "SELECT
                             tbg.idgroup,
                             tbp.name ,
                             tbg.idperson,
                             tbg.level_ AS lvl,
                             tbg.status,
                             tbp2.name   AS company
                        FROM hdk_tbgroup tbg,
                             tbperson tbp,
                             tbperson tbp2
                       WHERE tbg.idperson = tbp.idperson
                         AND tbp2.idperson = tbg.idcustomer
                      $where $order";
            if($limit){
                $limit = str_replace('LIMIT', "", $limit);
                $p     = explode(",", $limit);
                $start = $p[0] + 1;
                $end   = $p[0] +  $p[1];
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
            }else{
                $query = $core;
            }
        }
        $ret = $this->db->Execute($query);
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "<br>Query: " . $query    ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function countGroups($where = NULL, $order = NULL, $limit = NULL) {
        $query = "SELECT count(idgroup) AS total 
                    FROM hdk_tbgroup tbg,
                             tbperson tbp,
                             tbperson tbp2
                   WHERE tbg.idperson = tbp.idperson
                     AND tbp2.idperson = tbg.idcustomer 
                  $where $order $limit";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function groupsDeactivate($id) {
        $query = "UPDATE hdk_tbgroup SET status = 'N' WHERE idgroup in ($id)";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function groupsActivate($id) {
        $query = "UPDATE hdk_tbgroup SET status = 'A' WHERE idgroup in ($id)";
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectGroupData($id) {
        $query = "SELECT `name`, level, a.idperson, idcustomer, repass_only 
                    FROM hdk_tbgroup a, tbperson b
                   WHERE a.idperson = b.idperson
                     AND idgroup = '$id'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }
    
    public function selectRepGroupData($id) {
        return $this->select("SELECT tbp.name, tbg.level, tbp.idperson, tbg.repass_only from hdk_tbgroup tbg, tbperson as tbp where tbg.idperson = tbp.idperson and tbp.idperson='$id';");
    }

    public function updateGroup($id, $company, $repass, $level) {
        $query = "UPDATE hdk_tbgroup 
                     SET idcustomer = '$company', 
                         repass_only = '$repass', 
                         level = '$level' 
                   WHERE idgroup ='$id'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectAllGroups() {
        return $this->db->Execute("SELECT idgroup, name from hdk_tbgroup");
    }

    public function selectAttendants() {
        $query = "SELECT idperson, `name` FROM tbperson WHERE idtypeperson = 3 AND `status` = 'A' ORDER BY `name`";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function selectGroupAttendants($id) {
        return $this->db->Execute("SELECT 
                                              usuario.name
                                            , usuario.idperson
                                            , grupo.name as nomegroup
                                            , usu_grupo.idgroup
                                            , usu_grupo.idperson as COD_USU
                                    FROM
                                             tbperson usuario LEFT JOIN
                                             hdk_tbgroup_has_person usu_grupo ON (
                                                 usuario.idperson = usu_grupo.idperson
                                                 AND usu_grupo.idgroup = '$id'
                                             )
                                             LEFT JOIN hdk_tbgroup grupo ON (
                                             usu_grupo.idgroup = grupo.idgroup
                                             )
                                    WHERE
                                        usuario.idtypeperson = 3
                                        AND usuario.status = 'A'
                                    ORDER BY
                                        usuario.name ASC;
                                   ");
    }

    public function getGroupFirstLevel(){
        return $this->select("SELECT idgroup, `name`, `level`, idperson, repass_only, `status` FROM hdk_tbgroup WHERE `level` = 1");
    }
	
	public function checkGroupOnlyRepass($idperson){
    	return $this->select("SELECT repass_only FROM hdk_tbgroup WHERE idperson = '$idperson'");
    }
    
    public function getNewGroupOnlyRepass($idperson, $idcustomer){
    	return $this->select("SELECT a.idperson FROM hdk_tbgroup a, hdk_tbgroup_alias b WHERE b.idalias = '$idperson' AND a.idperson = b.idgroup AND a.idcustomer = '$idcustomer'");
    }

    public function checkAttendantGroup($idperson, $id) {
        $query = "SELECT ghp.idperson, a.`name` 
                    FROM hdk_tbgroup_has_person ghp, tbperson a, tbperson grp, hdk_tbgroup g
                   WHERE a.idperson = ghp.idperson
                     AND g.idgroup = ghp.idgroup
                     AND grp.idperson = g.idperson
                     AND ghp.idperson = $idperson
                     AND g.idgroup = $id";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function getGroupsRepass() {
        $query = "SELECT a.idgroup, a.idperson, b.name AS grp, c.name AS company 
                    FROM hdk_tbgroup a, tbperson b, tbperson c 
                   WHERE a.idperson = b.idperson 
                     AND a.idcustomer = c.idperson 
                     AND a.repass_only = 'Y' 
                ORDER BY company, grp ASC";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function getGroupsAlias($idalias) {
        $query = "SELECT idgroup, idalias FROM hdk_tbgroup_alias WHERE idalias = '$idalias'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function deleteGroupsRepass($idalias) {
        $query = "DELETE FROM hdk_tbgroup_alias WHERE idalias = '$idalias'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function insertGroupsRepass($idGroups,$idalias){
        $query = "INSERT into hdk_tbgroup_alias (idgroup,idalias) values ($idGroups,$idalias)";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

}

?>
