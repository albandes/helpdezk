<?php
class bank_model extends Model
{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function getCompanyBanks($where = null,$order = null)
    {
        $query = "SELECT DISTINCT a.idbank, `name`, `code`, ticketgroup, insurance_number
                    FROM fin_tbbank_has_company a, fin_tbbank b
                   WHERE a.idbank = b.idbank $where $order";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }

    public function getCheckSendItem($where = null,$order = null)
    {
        $query = "SELECT DISTINCT idlayoutdetail, idtypesection, a.`name`,idtypedata,`text`, (start_position - 1) start_position, 
                    (end_position - start_position) + 1  `fieldlength`, e.title
                    FROM fin_tblayoutdetail a
                    JOIN fin_tblayout b
                      ON b.idlayout = a.idlayout
                    JOIN fin_tblayout_has_bankcompany c
                      ON c.idlayout = b.idlayout
                    JOIN fin_tbbank_has_company d
                      ON d.idbankcompany = c.idbankcompany
         LEFT OUTER JOIN fin_tblayoutcheck e
                      ON e.idlayoutcheck = a.idlayoutcheck
                    $where $order";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }

    public function getBankConfig($where = null,$order = null)
    {
        $query = "SELECT DISTINCT a.idbank, `name`, `code`, ticketgroup, nossonro_mask, flag_protest, insurance_number
                    FROM fin_tbbank_has_company a, fin_tbbank b
                   WHERE a.idbank = b.idbank $where $order";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }

    public function getDominioCustomerID($where = null,$order = null)
    {
        $query = "SELECT idperseus, iddominio
                    FROM fin_tbdominio_customer
                   $where $order";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }

    public function getCompanyBanksExport($where = null,$order = null)
    {
        $query = "SELECT DISTINCT a.idbank, 
                          IF(`code` != '001' AND flag_protest = 'N',CONCAT(`name`,'-SP'),`name`) `name`, 
                          `code`, ticketgroup, insurance_number,
                          idperseus, flag_protest, iddominio
                    FROM fin_tbbank_has_company a, fin_tbbank b
                   WHERE a.idbank = b.idbank $where $order";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }




}
