<?php

class requestsearchuser_model extends Model {

    public function selectUser($where = NULL, $order = NULL, $limit = NULL) {
        $ret = $this->select("select
                                  person.idperson AS IDPERSON,
                                  person.name         as `NAME`,
                                  juridical.idperson  as IDCOMPANY,
                                  juridical.name as COMPANY
                                from tbperson as person,
                                  tbperson as juridical,
                                  hdk_tbdepartment_has_person as rela,
                                  hdk_tbdepartment as dep
                                where person.idperson = rela.idperson
                                    AND juridical.idperson = dep.idperson
                                    AND dep.iddepartment = rela.iddepartment
                                    AND person.status = 'A' $where $order $limit");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    //aqui deixaremos a tabela com alias, para que la na action json possamos usar a mesma variavel where pros 2 select
    public function countUser($where = NULL, $order = NULL, $limit = NULL) {
        $ret = $this->select("SELECT count(person.IDPERSON) as total from tbperson as person,
                                  tbperson as juridical,
                                  hdk_tbdepartment_has_person as rela,
                                  hdk_tbdepartment as dep
                                where person.idperson = rela.idperson
                                    AND juridical.idperson = dep.idperson
                                    AND dep.iddepartment = rela.iddepartment
                                    AND person.status = 'A' $where");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getUser($id) {
        $ret = $this->select("SELECT
                                  pe.name,
                                  pe.idperson,
                                  com.idperson idcompany
                                from tbperson as pe,
                                  tbperson as com,
                                  hdk_tbdepartment_has_person as phj,
                                  hdk_tbdepartment as dep
                                where pe.idperson = phj.idperson
                                    AND com.idperson = dep.idperson
                                    AND dep.iddepartment = phj.iddepartment
                                    AND pe.idperson = '$id'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

}

?>
