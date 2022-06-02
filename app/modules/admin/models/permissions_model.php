<?php

/*if(class_exists('Model')) {
    class DynamicIndex_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicIndex_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicIndex_model extends apiModel {}
}*/

class permissions_model extends Model {
    public function selectTypePrograms() {
        $query = "SELECT
                        tbty.name    as type,
                        tbty.idtypeperson as id
                    FROM tbtypeperson tbty";
                
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function selectCountTypePrograms() {
        $query = "SELECT COUNT(tbty.idtypeperson) as total FROM tbtypeperson tbty";
                
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret->fields['total'];
    }

    public function selectProgramFunctions($idprogram, $idperson, $type){
        $query = "SELECT
                        tbaccesstype.type AS accesstype,
                        tbaccesstype.idaccesstype AS idaccess,              
                        tbperm.allow AS perm
                    FROM tbtypeperson tbty,
                        tbprogram tbpr,
                        tbtypepersonpermission tbperm,
                        tbaccesstype tbaccesstype
                   WHERE tbpr.idprogram = '$idprogram'
                     AND tbpr.idprogram = tbperm.idprogram
                     AND tbty.idtypeperson = tbperm.idtypeperson
                     AND tbaccesstype.idaccesstype = tbperm.idaccesstype
                     AND tbperm.idtypeperson = '$idperson'
                     AND tbperm.idaccesstype = '$type'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }
     
    public function selectDefaultOperations($idprogram){
        $query = "SELECT
                        typ.type,
                        typ.idaccesstype as id
                    FROM tbaccesstype as typ,
                        tbdefaultpermission as perm
                   WHERE idprogram = '$idprogram' 
                     AND perm.idaccesstype = typ.idaccesstype";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function checkForPermissions($idprogram){
        $query = "SELECT allow FROM tbtypepersonpermission WHERE idaccesstype = '1' AND idtypeperson = '1' AND allow = 'N' AND idprogram = '$idprogram'";
                
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }
 
    public function grantPermission($idprogram, $idaccesstype, $idtypeperson, $allow){
         //return $this->db->Execute("update tbtypepersonpermission set allow = '$allow' where idprogram = '$idprogram' and idtypeperson = '$idtypeperson' and idaccesstype = '$idaccesstype'");
 
         // Since April 11, 2017
         $sql = "CALL hdk_insertTypePersonPermission($idprogram,$idtypeperson,$idaccesstype,'$allow');";
 
         $ret = $this->db->Execute($sql);
 
         if (!$ret) {
             $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . " query: " .$sql;
             die($sError);
         }
         return $ret ;
 
    } 
 
    public function revokePermission($idprogram, $idaccesstype, $idtypeperson, $allow){
        $query = "UPDATE tbtypepersonpermission SET allow = '$allow' WHERE idprogram = '$idprogram' AND idtypeperson = '$idtypeperson' AND idaccesstype = '$idaccesstype'";
                
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function groupPersonDelete($group, $person){
        $query = "DELETE FROM hdk_tbgroup_has_person WHERE idgroup='$group' AND idperson='$person'";
                
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

    public function groupPersonInsert($group, $person){
        $query = "INSERT INTO hdk_tbgroup_has_person (idgroup,idperson) VALUES ('$group','$person')";
                
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }
     
    public function getPermissionData($idprogram, $idperson){
        $query = "SELECT
                            tbp.idprogram,
                            tbp.name,
                            act.type,
                            act.idaccesstype,
                            perm.allow
                    FROM tbprogram tbp,
                        tbmodule tbm,
                        tbprogramcategory tbtp,
                        tbaccesstype act,
                        tbpermission perm,
                        tbperson per
                   WHERE tbtp.idmodule = tbm.idmodule
                     AND tbtp.idprogramcategory = tbp.idprogramcategory
                     AND perm.idaccesstype = act.idaccesstype
                     AND perm.idperson = per.idperson
                     AND tbp.idprogram = perm.idprogram
                     AND tbp.idprogram = '$idprogram'
                     AND per.idperson = '$idperson";
                
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret; 
    }
     
    public function getPersonPermission($idprogram, $idperson, $idaccesstype){
        $query = "SELECT
                            tbp.name,
                            act.type,
                            perm.allow,
                            per.name AS person
                    FROM tbprogram tbp,
                         tbmodule tbm,
                         tbprogramcategory tbtp,
                         tbaccesstype act,
                         tbpermission perm,
                         tbperson per
                   WHERE tbtp.idmodule = tbm.idmodule
                     AND tbtp.idprogramcategory = tbp.idprogramcategory
                     AND perm.idaccesstype = act.idaccesstype
                     AND perm.idperson = per.idperson
                     AND tbp.idprogram = perm.idprogram
                     AND tbp.idprogram = '$idprogram'
                     AND per.idperson = '$idperson'
                     AND act.idaccesstype = '$idaccesstype'";
                
        $ret = $this->db->Execute($query);
        //echo $query;
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }
     
    public function getDefaultOperations($idprogram){
        $query = "SELECT
                         access.type AS access,
                         access.idaccesstype AS id,              
                         tbpr.name
                    FROM tbprogram AS tbpr,
                         tbdefaultpermission AS tbperm,
                         tbaccesstype AS access
                   WHERE tbpr.idprogram = '$idprogram'
                     AND tbpr.idprogram = tbperm.idprogram
                     AND access.idaccesstype = tbperm.idaccesstype";
        
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }
     
    public function countDefaultOperations($idprogram){
        $query = "SELECT
                        count(access.idaccesstype) AS total
                    FROM tbprogram AS tbpr,
                         tbdefaultpermission AS tbperm,
                         tbaccesstype AS access
                   WHERE tbpr.idprogram = '$idprogram'
                     AND tbpr.idprogram = tbperm.idprogram
                     AND access.idaccesstype = tbperm.idaccesstype";
                
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret->fields['total'];
    }
     
    public function insertPersonExceptions($access,$prog,$person,$allow){
        $query = "INSERT INTO tbpermission (idaccesstype,idprogram,idperson,allow) VALUES ('$access','$prog','$person','$allow')";
                
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    } 
     
    public function grantPermissionPerson($idprogram, $idperson, $idaccesstype, $allow){
        $query = "CALL hdk_insertPersonPermission($idprogram,$idperson,$idaccesstype,'$allow')";
        //echo $query;
        $ret = $this->db->Execute($query);
 
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
         
        return $ret;
    }
     
    public function revokePermissionPerson($idprogram, $idperson, $type, $check){
        $query = "UPDATE tbpermission SET allow = '$check' WHERE idprogram = '$idprogram' AND idperson = '$idperson' AND idaccesstype = '$type'";
                
        $ret = $this->db->Execute($query);
 
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
         
        return $ret;
    }
     
    public function removeExceptions($idprogram, $idperson){
        $query = "DELETE FROM tbpermission WHERE idperson = '$idperson' AND idprogram = '$idprogram'";
                
        $ret = $this->db->Execute($query);
 
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
         
        return $ret;
    }
     
    public function getDefaultPerms($idprogram){
        $query = "SELECT idaccesstype FROM tbdefaultpermission WHERE idprogram = $idprogram";
                
        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }
        
        return $ret;
    }

}

?>
