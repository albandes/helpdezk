<?php
class logos_model extends Model{
    public function getHeaderLogo(){
        $sql = "SELECT name, height, width, file_name FROM tblogos WHERE name = 'header'";
        $ret = $this->db->Execute($sql);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }
    
    public function getLoginLogo(){
        $sql = "SELECT name, height, width, file_name FROM tblogos WHERE name = 'login'";
        $ret = $this->db->Execute($sql);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }
    
    public function getReportsLogo(){
        $sql = "SELECT name, height, width, file_name FROM tblogos WHERE name = 'reports'";
        $ret = $this->db->Execute($sql);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }
    
    public function upload($filename, $height, $width, $where){
        $sql = "UPDATE tblogos SET file_name = '$filename', height = '$height', width = '$width' WHERE name = '$where'";
        $ret = $this->db->Execute($sql);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function getLogosData($logo){

        $sql = "SELECT * FROM tblogos WHERE `name` = '{$logo}' AND   height > 0 AND width > 0";
        $ret = $this->db->Execute($sql);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

}
?>
