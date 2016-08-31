<?php
class programs_model extends Model{
    public function insertProgram($name,$controller,$smarty,$idpc){
        return $this->db->Execute("INSERT into tbprogram (name,controller,smarty,idprogramcategory,status) values('$name','$controller','$smarty','$idpc','A')");
    }

    public function selectProgram($where = NULL, $order = NULL, $limit = NULL){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return $this->select("SELECT tbp.idprogram, tbp.name, tbp.controller, tbm.name as module, tbp.status, tbtp.name as category, tbp.smarty from tbprogram as tbp, tbmodule as tbm, tbprogramcategory as tbtp where tbtp.idmodule = tbm.idmodule and tbtp.idprogramcategory = tbp.idprogramcategory $where $order $limit");
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
            return $this->db->Execute($query) ;
        }

    }
    public function countProgram($where = NULL, $order = NULL, $limit = NULL){
        $sel = $this->select("SELECT count(idprogram) as total from tbprogram tbp, tbmodule tbm, tbprogramcategory tbtp where tbtp.idmodule = tbm.idmodule and tbtp.idprogramcategory = tbp.idprogramcategory $where");
        return $sel;
    }
    public function selectModules(){
        $sel = $this->select("SELECT idmodule, name from tbmodule");
        return $sel;
    }
    public function selectCategory($id){
        return $this->select("Select idprogramcategory, name from tbprogramcategory where idmodule = $id");
    }
    public function countCategory($id){
        return $this->select("SELECT count(idprogramcategory) from tbprogramcategory where idprogramcategory = $id");
    }
    public function programDeactivate($id){
       return $this->db->Execute("UPDATE tbprogram set status = 'N' where idprogram in ($id)");  
    }
    public function programActivate($id){
       return $this->db->Execute("UPDATE tbprogram set status = 'A' where idprogram in ($id)");  
    }
    public function categoryInsert($name,$module){
       return $this->db->Execute("insert into tbprogramcategory (name,idmodule) values ('$name',$module)");
    }
    public function lastIdCategory(){
      $ret = $this->db->Execute("select max(idprogramcategory) as last from tbprogramcategory");
      return $ret->fields['last'];
    }
    public function selectProgramData($id){
        return $this->db->Execute("select name, controller, smarty, idprogramcategory from tbprogram where idprogram='$id'");
    }
    public function selectProgramModule($cat){
        return $this->db->Execute("select idmodule from tbprogramcategory where idprogramcategory='$cat'");
    }
    public function updateProgram($id,$name, $controller, $smarty,$category){
        return $this->db->Execute("update tbprogram set name = '$name', controller = '$controller', smarty = '$smarty', idprogramcategory='$category' where idprogram='$id'");
    }
    public function selectProgramID($name, $idc, $controller){
        $ret = $this->select("select idprogram from tbprogram where name = '$name' and controller = '$controller' and idprogramcategory='$idc'");
        return $ret->fields['idprogram'];        
    }
    public function insertDefaultPermission($idprogram, $idaccess, $allow){
        return $this->db->Execute("insert into tbdefaultpermission (idprogram, idaccesstype, allow) values ('$idprogram','$idaccess','$allow')");
    }
    public function getDefaultPermission($idprogram){
        return $this->db->Execute("SELECT iddefaultpermission,idaccesstype FROM tbdefaultpermission WHERE idprogram = $idprogram");
    }
    public function clearDefaultPerm($idprogram){
        return $this->db->Execute("DELETE FROM tbdefaultpermission WHERE idprogram = $idprogram");
    }
    
    
    public function countTypePerson(){
        $count = $this->select("select count(idtypeperson) as count from tbtypeperson");
        return $count->fields['count'];
    }
    public function insertGroupPermission($idprogram,$idtype,$idaccess){
        return $this->db->Execute("insert into tbtypepersonpermission (idprogram,idtypeperson,idaccesstype,allow) values ('$idprogram','$idtype','$idaccess','N')");
    }
	public function clearGroupPerm($idprogram){
        return $this->db->Execute("DELETE FROM tbtypepersonpermission WHERE idprogram = $idprogram");
    }
} 
?>
