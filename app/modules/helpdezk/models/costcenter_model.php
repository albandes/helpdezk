<?php
class costcenter_model extends Model{
     public function selectCorporations() {
        return $this->db->Execute("SELECT idperson,name from tbperson where idtypeperson = 4");
    }
    public function selectCostCenter($where, $order, $limit){
        return $this->db->Execute("Select tbc.idcostcenter as idcodcenter, tbc.name as name, tbc.cod_costcenter as cod_costcenter, tbp.name as company from hdk_tbcostcenter as tbc, tbperson as tbp where tbp.idperson = tbc.idperson $where $order $limit");
    }
     public function countCostCenter($where, $order, $limit){
        return $this->db->Execute("select count(idcostcenter) as total from hdk_tbcostcenter $where $order $limit");
    }
    public function insertCostCenter($name,$company,$cod){
        return $this->db->Execute("insert into hdk_tbcostcenter (idperson,cod_costcenter,name) values ('$company','$cod','$name')");
    }
}
?>
