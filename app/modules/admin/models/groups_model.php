<?php

class groups_model extends Model {

    public function selectCorporations() {
        return $this->db->Execute("SELECT idperson,name from tbperson where idtypeperson = 4 ORDER BY name ASC");
    }

    public function insertGroup($name, $level, $costumer, $repass) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return $this->db->Execute("INSERT into hdk_tbgroup (idperson,level,idcustomer,repass_only) values ($name,$level,$costumer,'$repass')");
        }
        elseif ($database == 'oci8po') {
            return $this->db->Execute("INSERT into hdk_tbgroup (idperson,level_,idcustomer,repass_only) values ($name,$level,$costumer,'$repass')");
        }
    }

    public function selectGroup($where = NULL, $order = NULL, $limit = NULL) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
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
        return $this->db->Execute($query);


    }

    public function countGroups($where = NULL, $order = NULL, $limit = NULL) {
        $sel = $this->select("SELECT count(idgroup) as total from hdk_tbgroup tbg, tbperson tbp where tbg.idperson = tbp.idperson $where");
        return $sel;
    }

    public function groupsDeactivate($id) {
        return $this->db->Execute("UPDATE hdk_tbgroup set status = 'N' where idgroup in ($id)");
    }

    public function groupsActivate($id) {
        return $this->db->Execute("UPDATE hdk_tbgroup set status = 'A' where idgroup in ($id)");
    }

    public function selectGroupData($id) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return $this->select("SELECT tbp.name, tbp.idperson, tbg.level as lvl, tbg.idcustomer, tbg.repass_only from hdk_tbgroup tbg, tbperson tbp where tbg.idperson = tbp.idperson and idgroup='$id'");
        }
        elseif ($database == 'oci8po') {
            return $this->select("SELECT tbp.name, tbp.idperson, tbg.level_ as lvl, tbg.idcustomer, tbg.repass_only from hdk_tbgroup tbg, tbperson tbp where tbg.idperson = tbp.idperson and idgroup='$id'");
        }
    }

    public function updateGroup($id, $company, $repass, $level) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return $this->db->Execute("UPDATE hdk_tbgroup set idcustomer='$company', repass_only='$repass', level='$level' where idgroup='$id'");
        }
        elseif ($database == 'oci8po') {
            return $this->db->Execute("UPDATE hdk_tbgroup set idcustomer='$company', repass_only='$repass', level_='$level' where idgroup='$id'");
        }
    }

    public function selectAllGroups() {
        return $this->db->Execute("SELECT idgroup, name from hdk_tbgroup");
    }

    public function selectAllServices() {
        return $this->db->Execute("select
                                      serv.name as service,
                                      serv.idservice,
                                      item.name as item,
                                      item.iditem
                                    from
                                    hdk_tbcore_service serv,
                                    hdk_tbcore_item item
                                    where serv.iditem = item.iditem");
    }

    public function selectServiceGroup($id) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return $this->db->Execute("select serv.name as service, serv.idservice, grp.idgroup, grppers.name as groupname, grp.level as lvl from hdk_tbcore_service serv, hdk_tbgroup grp, tbperson grppers, hdk_tbgroup_has_service relat where relat.idgroup = grp.idgroup and serv.idservice = relat.idservice and relat.idservice ='$id' And grppers.idperson = grp.idperson");
        }
        elseif ($database == 'oci8po') {
            return $this->db->Execute("select serv.name as service, serv.idservice, grp.idgroup, grppers.name as groupname, grp.level_ as lvl from hdk_tbcore_service serv, hdk_tbgroup grp, tbperson grppers, hdk_tbgroup_has_service relat where relat.idgroup = grp.idgroup and serv.idservice = relat.idservice and relat.idservice ='$id' And grppers.idperson = grp.idperson");
        }

        
    }

    public function selectAttendants() {
        return $this->db->Execute("select name, idperson from tbperson where idtypeperson in(1, 3) and idperson !=1 order by name ");
    }

    public function selectGroupAttendants($id) {
        return $this->select("SELECT
  usuario.name,
  usuario.idperson,
  grupo.name         as nomegroup,
  usu_grupo.idgroup,
  usu_grupo.idperson as COD_USU
FROM tbperson usuario,
hdk_tbgroup grupo,
hdk_tbgroup_has_person usu_grupo
WHERE usuario.idtypeperson = 3
    AND usuario.status = 'A'
    AND usuario.idperson = usu_grupo.idperson
    AND grupo.idgroup = usu_grupo.idgroup
    AND grupo.idgroup = '$id'
ORDER BY usuario.name ASC");
    }

    public function getGroupFirstLevel() {
        return $this->select("SELECT idgroup, `name`, `level`, idcustomer, repass_only, `status` FROM hdk_tbgroup WHERE `level` = 1");
    }

    public function checkAttendantGroup($idperson, $id) {
        return $this->select("select idperson, name from tbperson where idperson = '$idperson' and idperson in(
					select
					  p.idperson
					from hdk_tbgroup_has_person gp,
					tbperson p,
					hdk_tbgroup g
					where gp.idgroup = '$id'
					and gp.idgroup = g.idgroup
					AND p.idperson = gp.idperson)");
    }

	public function InsertID() {
        return $this->db->Insert_ID( );	
    }
    
    public function getGroupsRepass() {
        return $this->select("SELECT a.idgroup, a.idperson, b.name AS grp, c.name AS company FROM hdk_tbgroup a, tbperson b, tbperson c WHERE a.idperson = b.idperson AND a.idcustomer = c.idperson AND a.repass_only = 'Y' ORDER BY company, grp ASC");
    }
    
    public function getGroupsAlias($idalias) {
        return $this->select("SELECT idgroup, idalias FROM hdk_tbgroup_alias WHERE idalias = '$idalias'");
    }
    
    public function checkGroupOnlyRepass($idperson){
    	return $this->select("SELECT repass_only FROM hdk_tbgroup WHERE idperson = '$idperson'");
    }
    
    public function getNewGroupOnlyRepass($idperson, $idcustomer){
    	return $this->select("SELECT a.idperson FROM hdk_tbgroup a, hdk_tbgroup_alias b WHERE b.idalias = '$idperson' AND a.idperson = b.idgroup AND a.idcustomer = '$idcustomer'");
    }
    
    public function deleteGroupsRepass($idalias) {
        return $this->select("DELETE FROM hdk_tbgroup_alias WHERE idalias = '$idalias'");
    }
    
    public function insertGroupsRepass($idGroups,$idalias){
    	return $this->select("INSERT into hdk_tbgroup_alias (idgroup,idalias) values ($idGroups,$idalias)");
    }
	
	public function checkNameGroup($id, $name){
        $sel = $this->select("SELECT COUNT(*) as total FROM hdk_tbgroup, tbperson WHERE hdk_tbgroup.idcustomer = $id AND tbperson.idperson = hdk_tbgroup.idperson AND tbperson.name = '$name'");
    	return $sel->fields['total'];
    }
    
}

?>
