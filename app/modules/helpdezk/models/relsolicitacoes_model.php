<?php //MODEL DO PROGRAMA RELATÓRIO SOLICITAÇÕES

    //DETERMINAÇÃO DO CONTEÚDO CLASSE AUXILIAR
    if(class_exists('Model')) {
        class DynamicRelsolicitacoes_model extends Model {}
    } elseif(class_exists('cronModel')) {
        class DynamicRelsolicitacoes_model extends cronModel {}
    } elseif(class_exists('apiModel')) {
        class DynamicRelsolicitacoes_model extends apiModel {}
    }

class relsolicitacoes_model extends DynamicRelsolicitacoes_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }
    
    public function getFormData_Rel1($where=null,$group=null,$order=null,$limit=null) {

        //Esta SQL traz os dados quando o tipo de relatório é igual a 1
        $sql = "SELECT a.idperson idcompany, a.name company_name,SUM(d.minutes) AS total_min, COUNT(DISTINCT c.code_request) AS total_request
        FROM (tbperson a,tbperson b,hdk_tbrequest c)
        LEFT JOIN hdk_tbnote d
        ON (c.code_request = d.code_request)
        WHERE c.idperson_juridical = a.idperson 
        AND d.idperson = b.idperson
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
   
    }


    public function getFormData_Rel2($dtinterval_note, $dtinterval_request, $where=null,$group=null,$order=null,$limit=null) {

        //Esta SQL traz os dados quando o tipo de relatório é igual a 1
        $sql = " SELECT d.name operator,b.name department, c.name company,
        ROUND(SUM(CASE WHEN ($dtinterval_note AND e.minutes > 0) THEN e.minutes END)) TOTAL_TEMPO,
        COUNT(DISTINCT (CASE WHEN (f.idstatus = 1 AND $dtinterval_request AND g.ind_in_charge = 1) THEN (f.code_request) END)) NEW,
        COUNT(DISTINCT (CASE WHEN (f.idstatus = 2 AND $dtinterval_request AND g.ind_in_charge = 1) THEN f.code_request END)) REPASSED,
        COUNT(DISTINCT (CASE WHEN (f.idstatus = 3 AND $dtinterval_request AND g.ind_in_charge = 1) THEN f.code_request END)) ON_ATTENDANCE,
        COUNT(DISTINCT (CASE WHEN (f.idstatus IN(4,5) AND $dtinterval_request AND g.ind_in_charge = 1) THEN f.code_request END)) FINISH
        FROM hdk_tbdepartment_has_person a, hdk_tbdepartment b, tbperson c, tbperson d,
            hdk_tbnote e, hdk_tbrequest f, hdk_tbrequest_in_charge g
        WHERE a.iddepartment = b.iddepartment
        AND b.idperson = c.idperson
        AND a.idperson = d.idperson
        AND a.idperson = e.idperson
        AND e.code_request = f.code_request
        AND f.code_request = g.code_request
        AND a.idperson = g.id_in_charge
        $where $group $order $limit"; //echo "$sql\n";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
   
    }

    public function getFormDataArea($field, $where=null,$group=null,$order=null,$limit=null){

        //echo "ok";

        $sql = "SELECT $field, ROUND(SUM(minutes)) total_time
        FROM hdk_viewRequestData a, hdk_tbnote b
        WHERE a.code_request = b.code_request $where $group $order $limit"; //echo $sql;

        $ret = $this->db->Execute($sql); //echo "{$sql}\n"; 
        //if($ret != null){
            //echo "ok"; die();
        //}

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function comboAtendente($where=null,$group=null,$order=null,$limit=null){

        $sql = "SELECT a.idperson, b.name operator, a.iddepartment, d.name department,c.idperson companyID, c.name company
        FROM hdk_tbdepartment_has_person a, tbperson b, tbperson c, hdk_tbdepartment d
        WHERE a.idperson = b.idperson
        AND a.iddepartment = d.iddepartment
        AND d.idperson = c.idperson
        AND b.idtypeperson IN (1,3) $where $group $order $limit";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n"; //$ret = $this->db->Execute($sql)

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
            
    }

    public function getFinishedRequests($where=null,$group=null,$order=null,$limit=null){

        $sql = "SELECT 
        a.code_request AS Code,
        CONVERT(a.subject USING utf8) AS `Subject`,
        d.name AS Operator,
        b.MIN_EXPENDED_TIME AS Minutes,
        f.name AS Status FROM
        hdk_tbrequest a,
        hdk_tbrequest_times b,
        hdk_tbrequest_in_charge c,
        tbperson d,
        hdk_tbrequest_dates e,
        hdk_tbstatus f $where $group $order $limit";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n"; //$ret = $this->db->Execute($sql)

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }



}