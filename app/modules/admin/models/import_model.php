<?php

class import_model extends Model {

    public function selectPrograms() {
        return $this->select("select
  pr.idprogram,
  pr.name,
  pr.idprogramcategory,
  cat.idmodule
from tbprogram as pr,
  tbprogramcategory as cat
where pr.idprogramcategory = cat.idprogramcategory
order by idprogram");
    }
    public function insertDefaultPermissions($access,$program) {
        return $this->db->Execute("insert into tbdefaultpermission (idaccesstype,idprogram,allow) values ('$access','$program','Y')");
    }
    public function selectDefaults() {
        return $this->select("select idprogram, idaccesstype from tbdefaultpermission");
    }
    public function insertGroupPermissions($idprogram,$typeperson,$accesstype,$allow) {
        return $this->db->Execute("insert into tbtypepersonpermission (idprogram,idtypeperson,idaccesstype,allow) values ('$idprogram','$typeperson','$accesstype','$allow')");
    }
    public function selectGroups(){
        return $this->select("select idpermissiongroup from tbtypepersonpermission");
    }

}

?>
