<?php

namespace App\modules\admin\dao\mysql;
use App\core\Database;

class PersonDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function selectPersonByID(string $userID): array
    {        
        $sql = "SELECT tbp.idperson, tbp.name, tbp.login, tbp.email, tbp.status, tbp.user_vip, tbp.phone_number AS telephone,
                        tbp.branch_number, tbp.cel_phone AS cellphone, tbtp.name AS typeperson, tbtp.idtypeperson, 
                        ctry.printablename AS country, ctry.idcountry, stt.name AS state, stt.abbr AS state_abbr, stt.idstate,
                        nbh.name AS neighborhood, nbh.idneighborhood, ct.name AS city, ct.idcity, tpstr.name AS typestreet,
                        tpstr.idtypestreet, st.name AS street, addr.number, addr.complement, addr.zipcode,
                        pipeMask (addr.zipcode, '#####-###') AS zipcode_fmt, nat.ssn_cpf, pipeMask(nat.ssn_cpf,'###.###.###-##') AS cpf_fmt,
                        pipeMask(nat.ssn_cpf,'###-##-####') AS ssn_fmt, nat.rg, nat.rgoexp, nat.dtbirth, nat.mother,
                        nat.father, nat.gender, a.iddepartment, b.name AS department,
                        (SELECT `name` FROM tbperson WHERE idperson = b.idperson ) AS company,
                        b.idperson idcompany, tbp.idtypelogin, DATE_FORMAT(nat.dtbirth,'%d/%m/%Y') AS dtbirth_fmt,
                        addr.idstreet
                  FROM tbperson tbp, tbtypeperson tbtp, tbaddress addr, tbcity ct, tbcountry ctry, tbstate stt,
                        tbstreet st, tbneighborhood nbh, tbtypeaddress tpad, tbtypestreet tpstr, tbnaturalperson nat,
                        hdk_tbdepartment_has_person a, hdk_tbdepartment b
                 WHERE tbp.idtypeperson = tbtp.idtypeperson
                   AND a.idperson = tbp.idperson
                   AND a.iddepartment = b.iddepartment
                   AND tbp.idperson = nat.idperson
                   AND addr.idperson = tbp.idperson
                   AND addr.idcity = ct.idcity
                   AND addr.idneighborhood = nbh.idneighborhood
                   AND addr.idstreet = st.idstreet
                   AND addr.idtypeaddress = tpad.idtypeaddress
                   AND st.idtypestreet = tpstr.idtypestreet
                   AND ct.idstate = stt.idstate
                   AND stt.idcountry = ctry.idcountry
                   AND tbp.idperson = :userID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        return array("success"=>true,"message"=>"","idtypelogin"=>$aRet['idtypelogin']);
    }
}