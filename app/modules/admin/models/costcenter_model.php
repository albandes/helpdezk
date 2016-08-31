<?php
class costcenter_model extends Model{
     public function selectCorporations() {
        return $this->db->Execute("SELECT idperson,name from tbperson where idtypeperson = 4");
    }
    public function selectCostCenter($where, $order, $limit){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "SELECT tbc.idcostcenter as idcodcenter, tbc.name as name, tbc.cod_costcenter as cod_costcenter, tbp.name as company, tbc.status FROM hdk_tbcostcenter tbc, tbperson tbp where tbp.idperson = tbc.idperson $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "SELECT tbc.idcostcenter as idcodcenter, tbc.name as name, tbc.cod_costcenter as cod_costcenter, tbp.name as company, tbc.status FROM hdk_tbcostcenter tbc, tbperson tbp where tbp.idperson = tbc.idperson $where $order";
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
     public function countCostCenter($where){
        return $this->db->Execute("SELECT count(idcostcenter) as total FROM hdk_tbcostcenter tbc, tbperson tbp where tbp.idperson = tbc.idperson $where");
    }
    public function insertCostCenter($name,$company,$cod){
        return $this->db->Execute("insert into hdk_tbcostcenter (idperson,cod_costcenter,name) values ('$company','$cod','$name')");
    }
    public function costcenterDeactivate($id){
        return $this->db->Execute("update hdk_tbcostcenter set status = 'N' where idcostcenter in ($id)");
    }
    public function costcenterActivate($id){
        return $this->db->Execute("update hdk_tbcostcenter set status = 'A' where idcostcenter in ($id)");
    }
    public function getCostCenterData($id){
        return $this->db->Execute("select idperson, name, cod_costcenter from hdk_tbcostcenter where idcostcenter='$id'");
    }
    public function editCostcenter($id, $name, $company, $cod){
        return $this->db->Execute("update hdk_tbcostcenter set name = '$name', cod_costcenter = '$cod', idperson = '$company' where idcostcenter='$id'");
    }
    public function insertCostCenterPerson($idcostcenter, $idperson){
        return $this->db->Execute("insert into hdk_tbcostcenter_has_person (idperson,idcostcenter) values ($idperson,$idcostcenter)");
    }
}
?>
