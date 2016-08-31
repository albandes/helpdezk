<?php

class index_model extends Model {

    public function selectDataLogin($F_LOGIN, $F_SENHA) {
        $ret = $this->select("SELECT idperson
			FROM tbperson
			WHERE
			(login = '" . $F_LOGIN . "'  AND (password = '" . $F_SENHA . "' OR password IS NULL)and status = 'A')
			");
        return $ret->fields['idperson'];
    }

    public function checkUser($F_LOGIN) {
        $des_login = $this->select("SELECT login from tbperson where login = '" . $F_LOGIN . "'");
        if ($des_login->fields) {
            $act = $this->select("SELECT status from tbperson where login = '$F_LOGIN'");
            if ($act->fields['status'] == "A") {
                echo "Senha incorreta, digite novamente.";
            } else {
                echo "Usuário inativo, entre em contato com o administrador.";
            }
        } else {
            echo "Usuario n&atilde;o existe, verifique a digita&ccedil;&atilde;o!";
        }
    }
    public function selectDataSession($id){
        return $this->select("select person.idtypeperson as idtypeperson, person.name as name, juridical.idperson as idjuridical from tbperson as person, tbperson as juridical,
                              tbperson_has_juridical as rela where person.idperson = '$id'  and person.idperson=rela.idperson and juridical.idperson=rela.juridical");        
    }
    public function selectTypePerson($idperson){
        return $this->db->Execute("select tp.name from tbtypeperson as tp,tbperson as p where p.idtypeperson = tp.idtypeperson and idperson = '$idperson'");
    }

    /**
     * returns the Id of the person's department
     *
     * @param  string $login       Access login
     * @return int                 Id of the person's department
     * @since Version 1.2
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function getIdPersonDepartment($login)
    {
        $ret =  $this->select("
                                SELECT
                                  b.iddepartment
                                FROM
                                  tbperson a,
                                  hdk_tbdepartment_has_person b
                                WHERE a.login = '$login'
                                  AND a.idperson = b.idperson
                            ");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return  $ret->fields['iddepartment'];
    }

}

?>
