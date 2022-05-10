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
     * Return an array with person to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array  Parameters returned in array: 
     *                [status = true/false
     *                 push =  [message = PDO Exception message 
     *                          object = model's object]]
     */
    public function queryPersons($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT
                       tbp.idperson      as idperson,
                       tbp.name          as name,
                       tbp.login         as login,
                       tbp.email         as email,
                       tbp.status        as status,
                       tbtp.idtypeperson as idtypeperson,
                       tbtp.name         as typeperson,
                       comp.name         as company,
                       dep.name          as department
                  FROM (tbperson as tbp,
                       tbtypeperson as tbtp)
             LEFT JOIN hdk_tbdepartment_has_person as depP
                    ON (tbp.idperson = depP.idperson)
             LEFT JOIN hdk_tbdepartment as dep
                    ON (depP.iddepartment = dep.iddepartment)
             LEFT JOIN tbperson as comp
                    ON (dep.idperson = comp.idperson)
                 WHERE tbp.idtypeperson = tbtp.idtypeperson
                   AND tbp.idperson != 1
                   AND tbp.idtypeperson < 6 
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $personModel = new personModel();
            $personModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting persons ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return an array with total of persons to display in grid
     *
     * @param  string $where
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function countPersons($where=null): array
    {        
        $sql = "SELECT
                        tbp.idperson      as idperson,
                        tbp.name          as name,
                        tbp.login         as login,
                        tbp.email         as email,
                        tbp.status        as status,
                        tbtp.idtypeperson as idtypeperson,
                        tbtp.name         as typeperson,
                        comp.name         as company,
                        dep.name          as department
                  FROM (tbperson as tbp,
                        tbtypeperson as tbtp)
             LEFT JOIN hdk_tbdepartment_has_person as depP
                    ON (tbp.idperson = depP.idperson)
             LEFT JOIN hdk_tbdepartment as dep
                    ON (depP.iddepartment = dep.iddepartment)
             LEFT JOIN tbperson as comp
                    ON (dep.idperson = comp.idperson)
                 WHERE tbp.idtypeperson = tbtp.idtypeperson
                   AND tbp.idperson != 1
                   AND tbp.idtypeperson < 6 
                $where";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $personModel = new personModel();
            $personModel->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting person ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns user's data
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getPersonByID(personModel $personModel): array
    {        
        $sql = "SELECT tbp.idperson, tbp.name, tbp.login, tbp.email, tbp.status, tbp.user_vip, tbp.phone_number AS telephone,
                        tbp.branch_number, tbp.cel_phone AS cellphone, tbtp.name AS typeperson, tbtp.idtypeperson, 
                        ctry.printablename AS country, ctry.idcountry, stt.name AS state, stt.abbr AS state_abbr, stt.idstate,
                        nbh.name AS neighborhood, nbh.idneighborhood, ct.name AS city, ct.idcity, tpstr.name AS typestreet,
                        tpstr.idtypestreet, st.name AS street, addr.number, addr.complement, addr.zipcode,
                        pipeMask (addr.zipcode, '#####-###') AS zipcode_fmt, nat.ssn_cpf, pipeMask(nat.ssn_cpf,'###.###.###-##') AS cpf_fmt,
                        pipeMask(nat.ssn_cpf,'###-##-####') AS ssn_fmt, IFNULL(nat.rg,'') rg, IFNULL(nat.rgoexp,'') rgoexp, nat.dtbirth, IFNULL(nat.mother,'') mother,
                        IFNULL(nat.father,'') father, nat.gender, a.iddepartment, b.name AS department,
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
            $stmt->bindParam(':userID', $personModel->getIdPerson());
            $stmt->execute();
            $rows = $stmt->fetch(\PDO::FETCH_ASSOC);
        
            $personModel->setIdPerson($rows['idperson'])
                        ->setName($rows['name'])
                        ->setLogin($rows['login'])
                        ->setEmail($rows['email'])
                        ->setStatus($rows['status'])
                        ->setUserVip($rows['user_vip'])
                        ->setTelephone($rows['telephone'])
                        ->setBranchNumber($rows['branch_number'])
                        ->setCellphone($rows['cellphone'])
                        ->setTypeperson($rows['typeperson'])
                        ->setIdTypePerson($rows['idtypeperson'])
                        ->setCountry($rows['country'])
                        ->setIdCountry($rows['idcountry'])
                        ->setState($rows['state'])
                        ->setStateAbbr($rows['state_abbr'])
                        ->setIdState($rows['idstate'])
                        ->setNeighborhood($rows['neighborhood'])
                        ->setIdNeighborhood($rows['idneighborhood'])
                        ->setCity($rows['city'])
                        ->setIdCity($rows['idcity'])
                        ->setTypeStreet($rows['typestreet'])
                        ->setIdTypeStreet($rows['idtypestreet'])
                        ->setStreet($rows['street'])
                        ->setNumber($rows['number'])
                        ->setComplement($rows['complement'])
                        ->setZipCode($rows['zipcode'])
                        ->setZipCodeFmt($rows['zipcode_fmt'])
                        ->setSsnCpf($rows['ssn_cpf'])
                        ->setCpfFmt($rows['cpf_fmt'])
                        ->setSsnFmt($rows['ssn_fmt'])
                        ->setRg($rows['rg'])
                        ->setRgoExp($rows['rgoexp'])
                        ->setDtBirth($rows['dtbirth'])
                        ->setMother($rows['mother'])
                        ->setFather($rows['father'])
                        ->setGender($rows['gender'])
                        ->setIdDepartment($rows['iddepartment'])
                        ->setDepartment($rows['department'])
                        ->setCompany($rows['company'])
                        ->setIdCompany($rows['idcompany'])
                        ->setIdTypeLogin($rows['idtypelogin'])
                        ->setDtBirthFmt($rows['dtbirth_fmt'])
                        ->setIdStreet($rows['idstreet']);
            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting person data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns a list with registered companies
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchCompanies(personModel $personModel): array
    {        
        $sql = "SELECT  idperson as idcompany, name FROM tbperson WHERE idtypeperson IN (7,4,5) ORDER BY name ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setCompanyList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting extra modules ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Return an array with states data
     *
     * @param  mixed $where
     * @param  mixed $group
     * @param  mixed $order
     * @param  mixed $limit
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function queryStates($where=null,$group=null,$order=null,$limit=null): array
    {        
        $where = !$where ? "WHERE idstate != 1" : $where;
        $order = !$order ? "ORDER BY `name`" : $order;

        $sql = "SELECT idstate, `name` FROM tbstate $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $personModel = new personModel(); 
            $personModel->setStateList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting states data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}