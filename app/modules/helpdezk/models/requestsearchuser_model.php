<?php

class requestsearchuser_model extends Model {

    public function selectUser($where = NULL, $order = NULL, $limit = NULL) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "select
                                person.idperson,
                                person.name        as pname,
                                juridical.idperson as idcompany,
                                juridical.name     as cname
                              from tbperson person,
                                tbperson juridical,
                                hdk_tbdepartment_has_person rela,
                                hdk_tbdepartment dep
                              where person.idperson = rela.idperson
                                  AND juridical.idperson = dep.idperson
                                  AND dep.iddepartment = rela.iddepartment
                                  AND person.status = 'A' $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "select
                                person.idperson,
                                person.name        as pname,
                                juridical.idperson as idcompany,
                                juridical.name     as cname
                              from tbperson person,
                                tbperson juridical,
                                hdk_tbdepartment_has_person rela,
                                hdk_tbdepartment dep
                              where person.idperson = rela.idperson
                                  AND juridical.idperson = dep.idperson
                                  AND dep.iddepartment = rela.iddepartment
                                  AND person.status = 'A' $where $order";
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

    //aqui deixaremos a tabela com alias, para que la na action json possamos usar a mesma variavel where pros 2 select
    public function countUser($where = NULL, $order = NULL, $limit = NULL) {
        $ret = $this->select("SELECT count(person.IDPERSON) as total from tbperson person,
                                  tbperson juridical,
                                  hdk_tbdepartment_has_person rela,
                                  hdk_tbdepartment dep
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
                                  pe.name as pname,
                                  pe.idperson,
                                  com.idperson as idcompany
                                from tbperson pe,
                                  tbperson com,
                                  hdk_tbdepartment_has_person phj,
                                  hdk_tbdepartment dep
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