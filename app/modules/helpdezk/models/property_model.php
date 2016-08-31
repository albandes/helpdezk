<?php
class property_model extends Model {
    public function selectProperty($where = NULL, $order = NULL, $limit = NULL){
        return $this->select("SELECT idproperty,idcategory,idperson,idcompany,iddepartment,situation,idprovider,idmanufacturer,name,tag_number,model,
                serial_number,location,purchasing_date,receipt_number,warranty_date,observations,entry_date,idcostcenter,days_attendance,hours_attendance,
                maintenance_date,ip_number,mac_adress  from hdk_tbproperty $where $order $limit");
    }
    public function selectCountProperty($where = NULL, $order = NULL, $limit = NULL){
        return $this->select("SELECT count(idproperty) as total from hdk_tbproperty $where $order $limit");
    }
}
?>
