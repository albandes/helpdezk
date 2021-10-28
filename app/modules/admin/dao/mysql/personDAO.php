<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\personModel;

class personDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * getPersonByID
     *
     * @param  string $userID
     * @return personModel
     */
    public function getPersonByID(string $userID): ?personModel
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
            $this->loggerDB->error('Error getting person data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $rows = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $person = new Person();
        $person->setIdperson($rows['idperson'])
               ->setName($rows['name'])
               ->setLogin($rows['login'])
               ->setEmail($rows['email'])
               ->setStatus($rows['status'])
               ->setUserVip($rows['user_vip'])
               ->setTelephone($rows['telephone'])
               ->setBranchNumber($rows['branch_number'])
               ->setCellphone($rows['cellphone'])
               ->setTypeperson($rows['typeperson'])
               ->setIdtypeperson($rows['idtypeperson'])
               ->setCountry($rows['country'])
               ->setIdcountry($rows['idcountry'])
               ->setState($rows['state'])
               ->setStateAbbr($rows['state_abbr'])
               ->setIdstate($rows['idstate'])
               ->setNeighborhood($rows['neighborhood'])
               ->setIdneighborhood($rows['idneighborhood'])
               ->setCity($rows['city'])
               ->setIdcity($rows['idcity'])
               ->setTypestreet($rows['typestreet'])
               ->setIdtypestreet($rows['idtypestreet'])
               ->setStreet($rows['street'])
               ->setNumber($rows['number'])
               ->setComplement($rows['complement'])
               ->setZipcode($rows['zipcode'])
               ->setZipcodeFmt($rows['zipcode_fmt'])
               ->setSsnCpf($rows['ssn_cpf'])
               ->setCpfFmt($rows['cpf_fmt'])
               ->setSsnFmt($rows['ssn_fmt'])
               ->setRg($rows['rg'])
               ->setRgoexp($rows['rgoexp'])
               ->setDtbirth($rows['dtbirth'])
               ->setMother($rows['mother'])
               ->setFather($rows['father'])
               ->setGender($rows['gender'])
               ->setIddepartment($rows['iddepartment'])
               ->setDepartment($rows['department'])
               ->setCompany($rows['company'])
               ->setIdcompany($rows['idcompany'])
               ->setIdtypelogin($rows['idtypelogin'])
               ->setDtbirthFmt($rows['dtbirth_fmt'])
               ->setIdstreet($rows['idstreet']);

        return $person;
    }
}