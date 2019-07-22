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

    public function insertGroup($name, $level, $costumer, $repass) {
        return $this->db->Execute("INSERT into hdk_tbgroup (name,level,idperson,repass_only) values ('$name',$level,$costumer,'$repass')");
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
                        from hdk_tbgroup tbg,
                          tbperson tbp,
                          tbperson tbp2
                        where tbg.idperson = tbp.idperson
                            AND tbp2.idperson = tbg.idcustomer
                                $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "SELECT
                                      tbg.idgroup,
                                      tbp.name ,
                                      tbg.idperson,
                                      tbg.level_ as lvl,
                                      tbg.status,
                                      tbp2.name   as company
                                    from hdk_tbgroup tbg,
                                      tbperson tbp,
                                      tbperson tbp2
                                    where tbg.idperson = tbp.idperson
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
        $sel = $this->select("SELECT count(idgroup) as total from hdk_tbgroup $where $order $limit");
        return $sel;
    }

    public function groupsDeactivate($id) {
        return $this->db->Execute("UPDATE hdk_tbgroup set status = 'N' where idgroup in ($id)");
    }

    public function groupsActivate($id) {
        return $this->db->Execute("UPDATE hdk_tbgroup set status = 'A' where idgroup in ($id)");
    }

    public function selectGroupData($id) {
        return $this->select("SELECT name, level, idperson, repass_only from hdk_tbgroup where idgroup='$id';");
    }
    
    public function selectRepGroupData($id) {
        return $this->select("SELECT tbp.name, tbg.level, tbp.idperson, tbg.repass_only from hdk_tbgroup tbg, tbperson as tbp where tbg.idperson = tbp.idperson and tbp.idperson='$id';");
    }

    public function updateGroup($id, $name, $company, $repass, $level) {
        return $this->db->Execute("UPDATE hdk_tbgroup set name='$name',idperson='$company', repass_only='$repass', level='$level' where idgroup='$id'");
    }

    public function selectAllGroups() {
        return $this->db->Execute("SELECT idgroup, name from hdk_tbgroup");
    }

    public function selectAttendants() {
        return $this->db->Execute("select name from tbperson where idtypeperson = 3");
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

}

?>
