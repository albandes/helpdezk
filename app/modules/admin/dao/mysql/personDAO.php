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
                        //->setZipCodeFmt($rows['zipcode_fmt'])
                        ->setSsnCpf($rows['ssn_cpf'])
                        //->setCpfFmt($rows['cpf_fmt'])
                        //->setSsnFmt($rows['ssn_fmt'])
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
     * en_us Returns a list with registered countries
     * pt_br Retorna uma lista com países cadastrdos
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchCountries(personModel $personModel): array
    {        
        $sql = "SELECT idcountry, iso, printablename FROM tbcountry WHERE idcountry != 1 ORDER BY name";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setCountryList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting countries ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
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

    /**
     * en_us Returns a list with registered cities
     * pt_br Retorna uma lista com cidades cadastradas
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchCities(personModel $personModel): array
    {        
        $sql = "SELECT idcity, `name` FROM tbcity WHERE idstate = :stateID ORDER BY `name`";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":stateID",$personModel->getIdState());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setCitiesList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting cities ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a list with registered neighborhoods
     * pt_br Retorna uma lista com bairros cadastrados
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchNeighborhoods(personModel $personModel): array
    {        
        $sql = "SELECT idneighborhood, `name` FROM tbneighborhood WHERE idcity = :cityID ORDER BY `name`";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":cityID",$personModel->getIdCity());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setNeighborhoodList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting neighborhoods ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a list with registered street types
     * pt_br Retorna uma lista com tipos de logradouros cadastrados
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchStreetTypes(personModel $personModel): array
    {        
        $sql = "SELECT idtypestreet, `name` 
                  FROM tbtypestreet 
                 WHERE idtypestreet != 1 
                   AND UPPER(location) = UPPER(:location) 
              ORDER BY `name` ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":location",$personModel->getLocation());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setStreetTypeList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting street types ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a list with registered streets
     * pt_br Retorna uma lista com endereços cadastrados
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchStreets(personModel $personModel): array
    {        
        $sql = "SELECT idstreet,idtypestreet,`name` FROM tbstreet WHERE idtypestreet = :streetTypeID ORDER BY `name` ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":streetTypeID",$personModel->getIdTypeStreet());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setStreetList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting streets ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns company's data
     * pt_br Retorna os dados da empresa
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getCompanyByID(personModel $personModel): array
    {        
        $sql = "SELECT a.idperson, a.idnatureperson, b.name naturetype, a.name, phone_number, cel_phone, contact_person, a.status,
                        c.ein_cnpj, h.idcountry,  i.printablename country, f.idstate, h.name state, h.abbr uf,
                        e.idcity, f.name city, e.idneighborhood, g.name neighborhood, j.idtypestreet, k.name type_street, e.idstreet,
                        j.name street, e.number, e.complement, e.zipcode                       
                  FROM tbperson a
                  JOIN tbnatureperson b
                    ON b.idnatureperson = a.idnatureperson
                  JOIN tbjuridicalperson c
                    ON c.idperson = a.idperson 
       LEFT OUTER JOIN tbaddress e
                    ON e.idperson = a.idperson
       LEFT OUTER JOIN tbcity f
                    ON f.idcity = e.idcity
       LEFT OUTER JOIN tbneighborhood g
                    ON g.idneighborhood = e.idneighborhood
       LEFT OUTER JOIN tbstate h
                    ON h.idstate = f.idstate
       LEFT OUTER JOIN tbcountry i
                    ON i.idcountry = h.idcountry
       LEFT OUTER JOIN tbstreet j
                    ON j.idstreet = e.idstreet
       LEFT OUTER JOIN tbtypestreet k
                    ON (k.idtypestreet = j.idtypestreet AND
                        UPPER(location) = UPPER('pt_br'))
                 WHERE a.idtypeperson IN (4,5,7)
                   AND a.idperson = :companyID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':companyID', $personModel->getIdPerson());
            $stmt->execute();
            $rows = $stmt->fetch(\PDO::FETCH_ASSOC);
        
            $personModel->setIdPerson($rows['idperson'])
                        ->setName($rows['name'])
                        ->setTelephone((!empty($rows['phone_number']) && !is_null($rows['phone_number'])) ? $rows['phone_number'] : "")
                        ->setCellphone((!empty($rows['cel_phone']) && !is_null($rows['cel_phone'])) ? $rows['cel_phone'] : "")
                        ->setCountry($rows['country'])
                        ->setIdCountry($rows['idcountry'])
                        ->setState($rows['state'])
                        ->setStateAbbr($rows['uf'])
                        ->setIdState($rows['idstate'])
                        ->setNeighborhood($rows['neighborhood'])
                        ->setIdNeighborhood($rows['idneighborhood'])
                        ->setCity($rows['city'])
                        ->setIdCity($rows['idcity'])
                        ->setTypeStreet((!empty($rows['typestreet']) && !is_null($rows['typestreet'])) ? $rows['typestreet'] : "")
                        ->setIdTypeStreet((!empty($rows['idtypestreet']) && !is_null($rows['idtypestreet'])) ? $rows['idtypestreet'] : 0)
                        ->setStreet((!empty($rows['street']) && !is_null($rows['street'])) ? $rows['street'] : "")
                        ->setNumber((!empty($rows['number']) && !is_null($rows['number'])) ? $rows['number'] : "")
                        ->setComplement((!empty($rows['complement']) && !is_null($rows['complement'])) ? $rows['complement'] : "")
                        ->setZipCode((!empty($rows['zipcode']) && !is_null($rows['zipcode'])) ? $rows['zipcode'] : "")
                        ->setIdStreet((!empty($rows['idstreet']) && !is_null($rows['idstreet'])) ? $rows['idstreet'] : "")
                        ->setEinCnpj((!empty($rows['ein_cnpj']) && !is_null($rows['ein_cnpj'])) ? $rows['ein_cnpj'] : "");
            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting company data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}