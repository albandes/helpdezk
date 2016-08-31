<?php

class permissions_model extends Model {

    public function selectTypePrograms() {
       return $this->select("select
  tbty.name    as type,
  tbty.idtypeperson as id
from tbtypeperson tbty");
    }
    public function selectCountTypePrograms() {
       $ret = $this->select("select
          count(tbty.idtypeperson) as total
        from tbtypeperson tbty");
       return $ret->fields['total'];
    }
    public function selectProgramFunctions($idprogram, $idperson, $type){
        return $this->db->Execute("select
                                    tbaccesstype.type as accesstype,
                                    tbaccesstype.idaccesstype as idaccess,              
                                    tbperm.allow as perm
                                  from tbtypeperson tbty,
                                    tbprogram tbpr,
                                    tbtypepersonpermission tbperm,
                                    tbaccesstype tbaccesstype
                                  where tbpr.idprogram = '$idprogram'
                                      and tbpr.idprogram = tbperm.idprogram
                                      and tbty.idtypeperson = tbperm.idtypeperson
                                      AND tbaccesstype.idaccesstype = tbperm.idaccesstype
                                      and tbperm.idtypeperson = '$idperson'
                                      AND tbperm.idaccesstype = '$type'");
    }
    
    public function selectDefaultOperations($idprogram){
        return $this->select("
            select
  typ.type,
  typ.idaccesstype as id
from tbaccesstype as typ,
  tbdefaultpermission as perm
where idprogram = '$idprogram' 
and perm.idaccesstype = typ.idaccesstype");
    }
    public function checkForPermissions($idprogram){
        return $this->select("select allow from tbtypepersonpermission where idaccesstype = '1' and idtypeperson = '1' and allow = 'N' and idprogram = '$idprogram'");
    }
    public function grantPermission($idprogram, $idaccesstype, $idtypeperson, $allow){
        return $this->db->Execute("update tbtypepersonpermission set allow = '$allow' where idprogram = '$idprogram' and idtypeperson = '$idtypeperson' and idaccesstype = '$idaccesstype'");
    }
    public function revokePermission($idprogram, $idaccesstype, $idtypeperson, $allow){
        return $this->db->Execute("update tbtypepersonpermission set allow = '$allow' where idprogram = '$idprogram' and idtypeperson = '$idtypeperson' and idaccesstype = '$idaccesstype'");
    }
    public function groupPersonDelete($group, $person){
        return $this->db->Execute("delete from hdk_tbgroup_has_person where idgroup='$group' and idperson='$person'");
    }
    public function groupPersonInsert($group, $person){
        return $this->db->Execute("insert into hdk_tbgroup_has_person (idgroup,idperson) values ('$group','$person')");
    }
    
    public function getPermissionData($idprogram, $idperson){
        return $this->select("SELECT
  tbp.idprogram,
  tbp.name,
  act.type,
  act.idaccesstype,
  perm.allow
from tbprogram tbp,
  tbmodule tbm,
  tbprogramcategory tbtp,
  tbaccesstype act,
  tbpermission perm,
  tbperson per
where tbtp.idmodule = tbm.idmodule
    and tbtp.idprogramcategory = tbp.idprogramcategory
    AND perm.idaccesstype = act.idaccesstype
    AND perm.idperson = per.idperson
    AND tbp.idprogram = perm.idprogram
    and tbp.idprogram = '$idprogram'
    AND per.idperson = '$idperson'"); 
    }
    
    public function getPersonPermission($idprogram, $idperson, $idaccesstype){
       return $this->select("SELECT
          tbp.name,
          act.type,
          perm.allow,
          per.name as person
        from tbprogram tbp,
          tbmodule tbm,
          tbprogramcategory tbtp,
          tbaccesstype act,
          tbpermission perm,
          tbperson per
        where tbtp.idmodule = tbm.idmodule
            and tbtp.idprogramcategory = tbp.idprogramcategory
            AND perm.idaccesstype = act.idaccesstype
            AND perm.idperson = per.idperson
            AND tbp.idprogram = perm.idprogram
            AND tbp.idprogram = '$idprogram'
            AND per.idperson = '$idperson'
            AND act.idaccesstype = '$idaccesstype'");
    }
    
    public function getDefaultOperations($idprogram){
        return $this->db->Execute("select
          access.type as access,
          access.idaccesstype as id,              
          tbpr.name
        from tbprogram as tbpr,
          tbdefaultpermission as tbperm,
          tbaccesstype as access
        where tbpr.idprogram = '$idprogram'
            and tbpr.idprogram = tbperm.idprogram
            AND access.idaccesstype = tbperm.idaccesstype");
    }
    
    public function countDefaultOperations($idprogram){
        $ret = $this->db->Execute("select
          count(access.idaccesstype) as total
        from tbprogram as tbpr,
          tbdefaultpermission as tbperm,
          tbaccesstype as access
        where tbpr.idprogram = '$idprogram'
            and tbpr.idprogram = tbperm.idprogram
            AND access.idaccesstype = tbperm.idaccesstype");
        return $ret->fields['total'];
    }
    
    public function insertPersonExceptions($access,$prog,$person,$allow){
        return $this->db->Execute("insert into tbpermission (idaccesstype,idprogram,idperson,allow) values ('$access','$prog','$person','$allow')");
    } 
    
    public function grantPermissionPerson($idprogram, $idperson, $type, $check){
        return $this->db->Execute("update tbpermission set allow = '$check' where idprogram = '$idprogram' and idperson = '$idperson' and idaccesstype = '$type'");
    }
    
    public function revokePermissionPerson($idprogram, $idperson, $type, $check){
        return $this->db->Execute("update tbpermission set allow = '$check' where idprogram = '$idprogram' and idperson = '$idperson' and idaccesstype = '$type'");
    }
    
    public function removeExceptions($idprogram, $idperson){
        return $this->db->Execute("delete from tbpermission where idperson = '$idperson' and idprogram = '$idprogram'");
    }
	
	public function getDefaultPerms($idprogram){
        return $this->db->Execute("SELECT idaccesstype FROM tbdefaultpermission WHERE idprogram = $idprogram");
    }
	
}

?>
