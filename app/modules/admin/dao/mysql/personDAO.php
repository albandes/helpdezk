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
                       ptvoc.key_value   as typeperson,
                       comp.name         as company,
                       dep.name          as department
                  FROM tbperson as tbp
                  JOIN tbtypeperson as tbtp
                    ON tbp.idtypeperson = tbtp.idtypeperson
             LEFT JOIN hdk_tbdepartment_has_person as depP
                    ON (tbp.idperson = depP.idperson)
             LEFT JOIN hdk_tbdepartment as dep
                    ON (depP.iddepartment = dep.iddepartment)
             LEFT JOIN tbperson as comp
                    ON (dep.idperson = comp.idperson)
                  JOIN tbvocabulary ptvoc
                    ON ptvoc.key_name = tbtp.lang_key_name
                  JOIN tblocale ptloc
                    ON (ptloc.idlocale = ptvoc.idlocale AND
                        LOWER(ptloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                 WHERE tbp.idperson != 1
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
        $sql = "SELECT COUNT(tbp.idperson) total
                  FROM tbperson as tbp
                  JOIN tbtypeperson as tbtp
                    ON tbp.idtypeperson = tbtp.idtypeperson
             LEFT JOIN hdk_tbdepartment_has_person as depP
                    ON (tbp.idperson = depP.idperson)
             LEFT JOIN hdk_tbdepartment as dep
                    ON (depP.iddepartment = dep.iddepartment)
             LEFT JOIN tbperson as comp
                    ON (dep.idperson = comp.idperson)
                 WHERE tbp.idperson != 1
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
                        ->setTypePerson($rows['typeperson'])
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
       LEFT OUTER JOIN tbjuridicalperson c
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
                        ->setCountry((!empty($rows['country']) && !is_null($rows['country'])) ? $rows['country'] : "")
                        ->setIdCountry((!empty($rows['idcountry']) && !is_null($rows['idcountry'])) ? $rows['idcountry'] : 0)
                        ->setState((!empty($rows['state']) && !is_null($rows['state'])) ? $rows['state'] : "")
                        ->setStateAbbr((!empty($rows['uf']) && !is_null($rows['uf'])) ? $rows['uf'] : "")
                        ->setIdState((!empty($rows['idstate']) && !is_null($rows['idstate'])) ? $rows['idstate'] : 0)
                        ->setNeighborhood((!empty($rows['neighborhood']) && !is_null($rows['neighborhood'])) ? $rows['neighborhood'] : "")
                        ->setIdNeighborhood((!empty($rows['idneighborhood']) && !is_null($rows['idneighborhood'])) ? $rows['idneighborhood'] : 0)
                        ->setCity((!empty($rows['city']) && !is_null($rows['city'])) ? $rows['city'] : "")
                        ->setIdCity((!empty($rows['idcity']) && !is_null($rows['idcity'])) ? $rows['idcity'] : 0)
                        ->setTypeStreet((!empty($rows['typestreet']) && !is_null($rows['typestreet'])) ? $rows['typestreet'] : "")
                        ->setIdTypeStreet((!empty($rows['idtypestreet']) && !is_null($rows['idtypestreet'])) ? $rows['idtypestreet'] : 0)
                        ->setStreet((!empty($rows['street']) && !is_null($rows['street'])) ? $rows['street'] : "")
                        ->setNumber((!empty($rows['number']) && !is_null($rows['number'])) ? $rows['number'] : "")
                        ->setComplement((!empty($rows['complement']) && !is_null($rows['complement'])) ? $rows['complement'] : "")
                        ->setZipCode((!empty($rows['zipcode']) && !is_null($rows['zipcode'])) ? $rows['zipcode'] : "")
                        ->setIdStreet((!empty($rows['idstreet']) && !is_null($rows['idstreet'])) ? $rows['idstreet'] : 0)
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

    /**
     * en_us Returns a list with registered login types
     * pt_br Retorna uma lista com os tipos de login cadastrados
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchLoginTypes(personModel $personModel): array
    {        
        $sql = "SELECT idtypelogin, `name` FROM tbtypelogin ORDER BY name ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setLoginTypeList($aRet);

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
     * en_us Returns a list with registered access levels for natural person
     * pt_br Retorna uma lista com os tipos de nível de acessos cadastrados para pessoas físicas
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchNaturalPersonTypes(personModel $personModel): array
    {        
        $sql = "SELECT idtypeperson, a.name, permissiongroup, lang_key_name, b.key_value name_fmt, a.status 
                  FROM tbtypeperson a, tbvocabulary b, tblocale c
                 WHERE a.lang_key_name = b.key_name
                   AND b.idlocale = c.idlocale
                   AND LOWER(c.name) = LOWER('{$_ENV['DEFAULT_LANG']}')
                   AND a.idtypeperson IN (1,2,3)
              ORDER BY name_fmt ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setNaturalPersonTypeList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting natural person types ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a list with registered access levels for juridical person
     * pt_br Retorna uma lista com os tipos de nível de acessos cadastrados para pessoas jurídicas
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchJuridicalPersonTypes(personModel $personModel): array
    {        
        $sql = "SELECT idtypeperson, a.name, permissiongroup, lang_key_name, b.key_value name_fmt, a.status 
                  FROM tbtypeperson a, tbvocabulary b, tblocale c
                 WHERE a.lang_key_name = b.key_name
                   AND b.idlocale = c.idlocale
                   AND LOWER(c.name) = LOWER('{$_ENV['DEFAULT_LANG']}')
                   AND a.idtypeperson IN (4,5)
              ORDER BY name_fmt ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setJuridicalPersonTypeList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting juridical person types ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a list with registered access levels for juridical person
     * pt_br Retorna uma lista com os tipos de nível de acessos cadastrados para pessoas jurídicas
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchPermissionGroups(personModel $personModel): array
    {        
        $sql = "SELECT idtypeperson, a.name, permissiongroup, lang_key_name, b.key_value name_fmt, a.status 
                  FROM tbtypeperson a, tbvocabulary b, tblocale c
                 WHERE a.lang_key_name = b.key_name
                   AND b.idlocale = c.idlocale
                   AND LOWER(c.name) = LOWER('{$_ENV['DEFAULT_LANG']}')
                   AND a.permissiongroup = 'Y'
              ORDER BY name_fmt ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setPermissionGroupsList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting permission groups ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a list with registered locations
     * pt_br Retorna uma lista com as localizações cadastradas
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchLocations(personModel $personModel): array
    {        
        $sql = "SELECT idlocation, `name` FROM tblocation ORDER BY name ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setLocationsList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting locations data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with countries data
     * pt_br Retorna um array com os dados dos países
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
    public function queryCountries($where=null,$group=null,$order=null,$limit=null): array
    {        
        $where = !$where ? "WHERE idcountry != 1" : $where;
        $order = !$order ? "ORDER BY `name`" : $order;

        $sql = "SELECT idcountry, iso, printablename FROM tbcountry $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $personModel = new personModel(); 
            $personModel->setCountryList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting countries data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with cities data
     * pt_br Retorna um array com os dados das cidades
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
    public function queryCities($where=null,$group=null,$order=null,$limit=null): array
    {        
        $where = !$where ? "WHERE idcity != 1" : $where;
        $order = !$order ? "ORDER BY `name` ASC" : $order;

        $sql = "SELECT idcity, `name` FROM tbcity $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $personModel = new personModel(); 
            $personModel->setCitiesList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting cities data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with neighborhoods data
     * pt_br Retorna um array com os dados dos bairros
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
    public function queryNeighborhoods($where=null,$group=null,$order=null,$limit=null): array
    {        
        $where = !$where ? "WHERE idneighborhood != 1" : $where;
        $order = !$order ? "ORDER BY `name` ASC" : $order;

        $sql = "SELECT idneighborhood, `name` FROM tbneighborhood $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $personModel = new personModel(); 
            $personModel->setNeighborhoodList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting neighborhoods data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with strees data
     * pt_br Retorna um array com os dados das ruas
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
    public function queryStreets($where=null,$group=null,$order=null,$limit=null): array
    {        
        $where = !$where ? "AND idstreet != 1" : $where;
        $order = !$order ? "ORDER BY `name` ASC" : $order;

        $sql = "SELECT a.idstreet,a.idtypestreet,a.name, b.name type_street 
                  FROM tbstreet a, tbtypestreet b
                  WHERE a.idtypestreet = b.idtypestreet
                    AND UPPER(b.location) = UPPER('{$_ENV['DEFAULT_LANG']}')  
                  $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $personModel = new personModel(); 
            $personModel->setStreetList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting streets data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts person data into tbperson table
     * pt_br Insere os dados da pessoa na tabela tbperson
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertPerson(personModel $personModel): array
    {        
        $sql = "INSERT INTO tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,name,login,password,email,dtcreate,user_vip,
                                      phone_number,branch_number,cel_phone,fax,cod_location,time_value,overtime,change_pass) 
                              VALUES (:loginTypeId,:personTypeId,:personNature,:themeId,:name,:login,:password,:email,NOW(),:isUserVip,:phone,
                                      :branchNumber,:mobile,:fax,:locationId,:timeValue,:overtime,:changePassword)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":loginTypeId",$personModel->getIdTypeLogin());
        $stmt->bindValue(":personTypeId",$personModel->getIdTypePerson());
        $stmt->bindValue(":personNature",$personModel->getPersonNatureId());
        $stmt->bindValue(":themeId",$personModel->getThemeId());
        $stmt->bindValue(":name",$personModel->getName());
        $stmt->bindValue(":login",$personModel->getLogin());
        $stmt->bindValue(":password",$personModel->getPassword());
        $stmt->bindValue(":email",$personModel->getEmail());
        $stmt->bindValue(":isUserVip",$personModel->getUserVip());
        $stmt->bindValue(":phone",$personModel->getTelephone());
        $stmt->bindValue(":branchNumber",$personModel->getBranchNumber());
        $stmt->bindValue(":mobile",$personModel->getCellphone());
        $stmt->bindValue(":fax",$personModel->getFax());
        $stmt->bindValue(":locationId",$personModel->getLocationId());
        $stmt->bindValue(":timeValue",$personModel->getTimeValue());
        $stmt->bindValue(":overtime",$personModel->getOvertimeWork());
        $stmt->bindValue(":changePassword",$personModel->getChangePasswordFlag());
        $stmt->execute();

        $personModel->setIdPerson($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts person address into tbaddress tabel
     * pt_br Insere o endereço da pessoa na tabela tbaddress
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertAddress(personModel $personModel): array
    {        
        $sql = "INSERT INTO tbaddress (idperson,idcity,idneighborhood,idstreet,idtypeaddress,number,complement,zipcode)  
                     VALUES (:personId,:cityId,:neighborhoodId,:streetId,:addressTypeId,:number,:complement,:zipcode)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->bindValue(":cityId",$personModel->getIdCity());
        $stmt->bindValue(":neighborhoodId",$personModel->getIdNeighborhood());
        $stmt->bindValue(":streetId",$personModel->getIdStreet());
        $stmt->bindValue(":addressTypeId",$personModel->getAddressTypeId());
        $stmt->bindValue(":number",$personModel->getNumber());
        $stmt->bindValue(":complement",$personModel->getComplement());
        $stmt->bindValue(":zipcode",$personModel->getZipCode());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts natural person data into tbnaturalperson table
     * pt_br Insere os dados da pessoa física na tabela tbnaturalperson
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertNaturalData(personModel $personModel): array
    {        
        $sql = "INSERT INTO tbnaturalperson (idperson, ssn_cpf, dtbirth, gender) VALUES (:personId, :ssnCpf, :birthDt, :gender)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->bindValue(":ssnCpf",$personModel->getSsnCpf());
        $stmt->bindValue(":birthDt",$personModel->getDtBirth());
        $stmt->bindValue(":gender",$personModel->getGender());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts the bind of the person with the department
     * pt_br Insere o vínculo da pessoa com o departamento
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertInDepartment(personModel $personModel): array
    {        
        $sql = "INSERT INTO hdk_tbdepartment_has_person (idperson, iddepartment) VALUES (:personId, :departmentId)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->bindValue(":departmentId",$personModel->getIdDepartment());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts the bind of the person with the department
     * pt_br Insere o vínculo da pessoa com o departamento
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertPermissionGroup(personModel $personModel): array
    {        
        $sql = "INSERT INTO tbpersontypes (idperson, idtypeperson) VALUES (:personId, :permissionGroupId)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->bindValue(":permissionGroupId",$personModel->getPermissionGroupId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts the bind of the person with group(s)
     * pt_br Insere o vínculo da pessoa com o(s) grupo(s) 
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertGroup(personModel $personModel): array
    {        
        $sql = "INSERT INTO hdk_tbgroup_has_person (idgroup,idperson) VALUES (:groupId,:personId)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":groupId",$personModel->getGroupId());
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts natural person data into tbnaturalperson table
     * pt_br Insere os dados da pessoa física na tabela tbnaturalperson
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertJuridicalData(personModel $personModel): array
    {        
        $sql = "INSERT INTO tbjuridicalperson (idperson, ein_cnpj, contact_person, observation) VALUES (:personId, :einCnpj, :contact, :observation)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->bindValue(":einCnpj",$personModel->getEinCnpj());
        $stmt->bindValue(":contact",$personModel->getContactName());
        $stmt->bindValue(":observation",$personModel->getObsevation());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts the bind of the person with the department
     * pt_br Insere o vínculo da pessoa com o departamento
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertDepartment(personModel $personModel): array
    {        
        $sql = "INSERT INTO hdk_tbdepartment (idperson, cod_area, `name`) VALUES (:personId, 0, :departmentName)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->bindValue(":departmentName",$personModel->getDepartment());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * en_us Saves the new person into DB
     * pt_br Grava a nova pessoa no banco de dados
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function savePersonData(personModel $personModel): array
    {   
        $aPermissionGroups = $personModel->getPermissionGroupsList();
        $aGroups = $personModel->getPersonGroupsList();
        
        try{
            $this->db->beginTransaction();

            $ins = $this->insertPerson($personModel);

            if($ins['status']){
                //insert person addrress
                $insAddress = $this->insertAddress($ins['push']['object']);
                if($insAddress['status'])
                    $this->loggerDB->info('Person address was included', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                
                // insert natural person data
                if($ins['push']['object']->getPersonNatureId() == 1){
                    // inserts data into tbnaturalperson table
                    $insNatural = $this->insertNaturalData($ins['push']['object']);
                    if($insNatural['status'])
                        $this->loggerDB->info('Natural person data was included', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

                    // bind person with the department
                    if($ins['push']['object']->getIdDepartment() > 0){
                        $insInDepartment = $this->insertInDepartment($ins['push']['object']);
                        if($insInDepartment['status'])
                            $this->loggerDB->info('Natural person has been linked to the department', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    }

                    // insert permission group
                    if(!empty($aPermissionGroups) && count($aPermissionGroups) > 0) {
                        foreach($aPermissionGroups as $k=>$v){
                            $ins['push']['object']->setPermissionGroupId($v);
    
                            $retPermGrps = $this->insertPermissionGroup($ins['push']['object']);
                            if($retPermGrps['status'])
                                $this->loggerDB->info('Natural person has been linked to the permission group', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        }
                    }

                    // insert group - (attendant)
                    if(!empty($aGroups) && count($aGroups) > 0) {
                        foreach($aGroups as $k=>$v){
                            $ins['push']['object']->setGroupId($v);
    
                            $retGroup = $this->insertGroup($ins['push']['object']);
                            if($retGroup['status'])
                                $this->loggerDB->info('Natural person has been linked to the group', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        }
                    }
                }else{
                    // inserts data into tbjuridicalperson table
                    $insJuridical = $this->insertJuridicalData($ins['push']['object']);
                    if($insJuridical['status'])
                        $this->loggerDB->info('Juridical person data was included', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

                    // insert department
                    $insDepartment = $this->insertDepartment($ins['push']['object']);
                    if($insDepartment['status'])
                        $this->loggerDB->info('Department of juridica person was included', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                }
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save person info', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns person's data
     * pt_br Retorna os dados da pessoa
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getPerson(personModel $personModel): array
    {        
        $sql = "SELECT tbp.idperson, tbp.name, tbp.login, tbp.email, tbp.status, tbp.user_vip, tbp.phone_number,
                        tbp.branch_number, tbp.cel_phone, tbp.fax, tbtp.name AS typeperson, tbtp.idtypeperson, 
                        ctry.printablename AS country, ctry.idcountry, stt.name AS state, stt.abbr AS state_abbr, stt.idstate,
                        nbh.name AS neighborhood, nbh.idneighborhood, ct.name AS city, ct.idcity, tpstr.name AS typestreet,
                        tpstr.idtypestreet, st.name AS street, addr.idstreet, addr.number, addr.complement, addr.zipcode, nat.ssn_cpf, 
                        IFNULL(nat.rg,'') rg, IFNULL(nat.rgoexp,'') rgoexp, nat.dtbirth, IFNULL(nat.mother,'') mother,
                        IFNULL(nat.father,'') father, nat.gender, a.iddepartment, b.name AS department,
                        pcomp.name AS company, b.idperson idcompany, tbp.idtypelogin,
                        pjur.ein_cnpj, pjur.iestadual, pjur.contact_person, pjur.observation,
                        GROUP_CONCAT(DISTINCT c.idtypeperson) idpermission_groups,
                        GROUP_CONCAT(DISTINCT d.name) permission_groups,
                        GROUP_CONCAT(DISTINCT f.idgroup) idgroups,
                        GROUP_CONCAT(DISTINCT pgroup.name) groups,
                        ptvoc.key_value persontype_fmt, tbp.idnatureperson, np.name natureperson,
                        tbp.time_value,tbp.overtime,tbp.cod_location
                  FROM tbperson tbp
                  JOIN tbtypeperson tbtp
                    ON tbtp.idtypeperson = tbp.idtypeperson
                  JOIN tbnatureperson np
                    ON np.idnatureperson = tbp.idnatureperson
       LEFT OUTER JOIN tbaddress addr
                    ON addr.idperson = tbp.idperson
       LEFT OUTER JOIN tbcity ct
                    ON ct.idcity = addr.idcity
       LEFT OUTER JOIN tbneighborhood nbh
                    ON nbh.idneighborhood = addr.idneighborhood
       LEFT OUTER JOIN tbstreet st
                    ON st.idstreet = addr.idstreet
       LEFT OUTER JOIN tbtypeaddress tpad
                    ON tpad.idtypeaddress = addr.idtypeaddress
       LEFT OUTER JOIN tbtypestreet tpstr
                    ON tpstr.idtypestreet = st.idtypestreet
       LEFT OUTER JOIN tbstate stt
                    ON stt.idstate = ct.idstate
       LEFT OUTER JOIN tbcountry ctry 
                    ON ctry.idcountry = stt.idcountry
       LEFT OUTER JOIN tbnaturalperson nat
                    ON nat.idperson = tbp.idperson
       LEFT OUTER JOIN hdk_tbdepartment_has_person a
                    ON a.idperson = tbp.idperson
       LEFT OUTER JOIN hdk_tbdepartment b
                    ON b.iddepartment = a.iddepartment
       LEFT OUTER JOIN tbperson pcomp
                    ON pcomp.idperson = b.idperson
       LEFT OUTER JOIN tbpersontypes c
                    ON c.idperson = tbp.idperson
       LEFT OUTER JOIN tbtypeperson d
                    ON d.idtypeperson = c.idtypeperson
       LEFT OUTER JOIN hdk_tbgroup_has_person e
                    ON e.idperson = tbp.idperson
       LEFT OUTER JOIN hdk_tbgroup f
                    ON f.idgroup = e.idgroup
       LEFT OUTER JOIN tbperson pgroup
                    ON pgroup.idperson = f.idperson
       LEFT OUTER JOIN tbjuridicalperson pjur
                    ON pjur.idperson = tbp.idperson
                  JOIN tbvocabulary ptvoc
                    ON ptvoc.key_name = tbtp.lang_key_name
                  JOIN tblocale ptloc
                    ON (ptloc.idlocale = ptvoc.idlocale AND
                        LOWER(ptloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                 WHERE tbp.idperson = :userID
              GROUP BY tbp.idperson";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $personModel->getIdPerson());
            $stmt->execute();
            $rows = $stmt->fetch(\PDO::FETCH_ASSOC);
        
            $personModel->setIdPerson($rows['idperson'])
                        ->setName($rows['name'])
                        ->setLogin((!empty($rows['login']) && !is_null($rows['login'])) ? $rows['login'] : '')
                        ->setEmail((!empty($rows['email']) && !is_null($rows['email'])) ? $rows['email'] : '')
                        ->setStatus($rows['status'])
                        ->setUserVip($rows['user_vip'])
                        ->setTelephone((!empty($rows['phone_number']) && !is_null($rows['phone_number'])) ? $rows['phone_number'] : '')
                        ->setBranchNumber((!empty($rows['branch_number']) && !is_null($rows['branch_number'])) ? $rows['branch_number'] : '')
                        ->setCellphone((!empty($rows['cel_phone']) && !is_null($rows['cel_phone'])) ? $rows['cel_phone'] : '')
                        ->setFax((!empty($rows['fax']) && !is_null($rows['fax'])) ? $rows['fax'] : '')
                        ->setTypePerson((!empty($rows['persontype_fmt']) && !is_null($rows['persontype_fmt'])) ? $rows['persontype_fmt'] : '')
                        ->setIdTypePerson((!empty($rows['idtypeperson']) && !is_null($rows['idtypeperson'])) ? $rows['idtypeperson'] : 0)
                        ->setCountry((!empty($rows['country']) && !is_null($rows['country'])) ? $rows['country'] : '')
                        ->setIdCountry((!empty($rows['idcountry']) && !is_null($rows['idcountry'])) ? $rows['idcountry'] : 0)
                        ->setState((!empty($rows['state']) && !is_null($rows['state'])) ? $rows['state'] : '')
                        ->setStateAbbr((!empty($rows['state_abbr']) && !is_null($rows['state_abbr'])) ? $rows['state_abbr'] : '')
                        ->setIdState((!empty($rows['idstate']) && !is_null($rows['idstate'])) ? $rows['idstate'] : 0)
                        ->setNeighborhood((!empty($rows['neighborhood']) && !is_null($rows['neighborhood'])) ? $rows['neighborhood'] : '')
                        ->setIdNeighborhood($rows['idneighborhood'])
                        ->setCity((!empty($rows['city']) && !is_null($rows['city'])) ? $rows['city'] : '')
                        ->setIdCity((!empty($rows['idcity']) && !is_null($rows['idcity'])) ? $rows['idcity'] : 0)
                        ->setTypeStreet((!empty($rows['typestreet']) && !is_null($rows['typestreet'])) ? $rows['typestreet'] : '')
                        ->setIdTypeStreet((!empty($rows['idtypestreet']) && !is_null($rows['idtypestreet'])) ? $rows['idtypestreet'] : 0)
                        ->setStreet((!empty($rows['street']) && !is_null($rows['street'])) ? $rows['street'] : '')
                        ->setIdStreet((!empty($rows['idstreet']) && !is_null($rows['idstreet'])) ? $rows['idstreet'] : 0)
                        ->setNumber((!empty($rows['number']) && !is_null($rows['number'])) ? $rows['number'] : '')
                        ->setComplement((!empty($rows['complement']) && !is_null($rows['complement'])) ? $rows['complement'] : '')
                        ->setZipCode((!empty($rows['zipcode']) && !is_null($rows['zipcode'])) ? $rows['zipcode'] : '')
                        ->setSsnCpf((!empty($rows['ssn_cpf']) && !is_null($rows['ssn_cpf'])) ? $rows['ssn_cpf'] : '')
                        ->setRg((!empty($rows['rg']) && !is_null($rows['rg'])) ? $rows['rg'] : '')
                        ->setRgoExp((!empty($rows['rgoexp']) && !is_null($rows['rgoexp'])) ? $rows['rgoexp'] : '')
                        ->setDtBirth((!empty($rows['dtbirth']) && !is_null($rows['dtbirth'])) ? $rows['dtbirth'] : '')
                        ->setMother((!empty($rows['mother']) && !is_null($rows['mother'])) ? $rows['mother'] : '')
                        ->setFather((!empty($rows['father']) && !is_null($rows['father'])) ? $rows['father'] : '')
                        ->setGender((!empty($rows['gender']) && !is_null($rows['gender'])) ? $rows['gender'] : '')
                        ->setIdDepartment((!empty($rows['iddepartment']) && !is_null($rows['iddepartment'])) ? $rows['iddepartment'] : 0)
                        ->setDepartment((!empty($rows['department']) && !is_null($rows['department'])) ? $rows['department'] : '')
                        ->setCompany((!empty($rows['company']) && !is_null($rows['company'])) ? $rows['company'] : '')
                        ->setIdCompany((!empty($rows['idcompany']) && !is_null($rows['idcompany'])) ? $rows['idcompany'] : 0)
                        ->setIdTypeLogin((!empty($rows['idtypelogin']) && !is_null($rows['idtypelogin'])) ? $rows['idtypelogin'] : 0)
                        ->setEinCnpj((!empty($rows['ein_cnpj']) && !is_null($rows['ein_cnpj'])) ? $rows['ein_cnpj'] : '')
                        ->setIestadual((!empty($rows['iestadual']) && !is_null($rows['iestadual'])) ? $rows['iestadual'] : '')
                        ->setContactName((!empty($rows['contact_person']) && !is_null($rows['contact_person'])) ? $rows['contact_person'] : '')
                        ->setObsevation((!empty($rows['observation']) && !is_null($rows['observation'])) ? $rows['observation'] : '')
                        ->setPermissionGroupsIdList((!empty($rows['idpermission_groups']) && !is_null($rows['idpermission_groups'])) ? explode(',',$rows['idpermission_groups']) : array())
                        ->setPermissionGroupsList((!empty($rows['permission_groups']) && !is_null($rows['permission_groups'])) ? explode(',',$rows['permission_groups']) : array())
                        ->setPersonGroupsIdList((!empty($rows['idgroups']) && !is_null($rows['idgroups'])) ? explode(',',$rows['idgroups']) : array())
                        ->setPersonGroupsList((!empty($rows['groups']) && !is_null($rows['groups'])) ? explode(',',$rows['groups']) : array())
                        ->setPersonNatureId($rows['idnatureperson'])
                        ->setPersonNature($rows['natureperson'])
                        ->setTimeValue((!empty($rows['time_value']) && !is_null($rows['time_value'])) ? $rows['time_value'] : 0)
                        ->setOvertimeWork((!empty($rows['overtime']) && !is_null($rows['overtime'])) ? $rows['overtime'] : 0)
                        ->setLocationId((!empty($rows['cod_location']) && !is_null($rows['cod_location'])) ? $rows['cod_location'] : 0);

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
     * en_us Updates person data into tbperson table
     * pt_br Atualiza os dados da pessoa na tabela tbperson
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updatePerson(personModel $personModel): array
    {        
        $sql = "UPDATE tbperson 
                   SET idtypelogin = :loginTypeId,
                       idtypeperson = :personTypeId,
                       `name` = :name,
                       email = :email,
                       user_vip = :isUserVip,
                       phone_number = :phone,
                       branch_number = :branchNumber,
                       cel_phone = :mobile,
                       fax = :fax,
                       cod_location = :locationId,
                       time_value = :timeValue,
                       overtime = :overtime
                 WHERE idperson = :personId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":loginTypeId",$personModel->getIdTypeLogin());
        $stmt->bindValue(":personTypeId",$personModel->getIdTypePerson());
        $stmt->bindValue(":name",$personModel->getName());
        $stmt->bindValue(":email",$personModel->getEmail());
        $stmt->bindValue(":isUserVip",$personModel->getUserVip());
        $stmt->bindValue(":phone",$personModel->getTelephone());
        $stmt->bindValue(":branchNumber",$personModel->getBranchNumber());
        $stmt->bindValue(":mobile",$personModel->getCellphone());
        $stmt->bindValue(":fax",$personModel->getFax());
        $stmt->bindValue(":locationId",$personModel->getLocationId());
        $stmt->bindValue(":timeValue",$personModel->getTimeValue());
        $stmt->bindValue(":overtime",$personModel->getOvertimeWork());
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates person address into tbaddress tabel
     * pt_br Atualiza o endereço da pessoa na tabela tbaddress
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateAddress(personModel $personModel): array
    {        
        $sql = "UPDATE tbaddress 
                   SET idcity = :cityId,
                       idneighborhood = :neighborhoodId,
                       idstreet = :streetId,
                       number = :number,
                       complement = :complement,
                       zipcode = :zipcode
                 WHERE idperson = :personId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":cityId",$personModel->getIdCity());
        $stmt->bindValue(":neighborhoodId",$personModel->getIdNeighborhood());
        $stmt->bindValue(":streetId",$personModel->getIdStreet());
        $stmt->bindValue(":number",$personModel->getNumber());
        $stmt->bindValue(":complement",$personModel->getComplement());
        $stmt->bindValue(":zipcode",$personModel->getZipCode());
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates natural person data into tbnaturalperson table
     * pt_br Atualiza os dados da pessoa física na tabela tbnaturalperson
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateNaturalData(personModel $personModel): array
    {        
        $sql = "UPDATE tbnaturalperson 
                   SET ssn_cpf = :ssnCpf, 
                       dtbirth = :birthDt, 
                       gender = :gender
                WHERE idperson = :personId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ssnCpf",$personModel->getSsnCpf());
        $stmt->bindValue(":birthDt",$personModel->getDtBirth());
        $stmt->bindValue(":gender",$personModel->getGender());
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates the bind of the person with the department
     * pt_br Atualiza o vínculo da pessoa com o departamento
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateInDepartment(personModel $personModel): array
    {        
        $sql = "UPDATE hdk_tbdepartment_has_person SET iddepartment = :departmentId WHERE idperson = :personId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":departmentId",$personModel->getIdDepartment());
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes the bind of the person with permission group
     * pt_br Elimina o vínculo da pessoa com o grupo de permissões
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deletePermissionGroup(personModel $personModel): array
    {        
        $sql = "DELETE FROM tbpersontypes WHERE idperson = :personId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes the bind of the person with group(s)
     * pt_br Elimina o vínculo da pessoa com o(s) grupo(s) 
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteGroup(personModel $personModel): array
    {        
        $sql = "DELETE FROM hdk_tbgroup_has_person WHERE idperson = :personId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates natural person data into tbnaturalperson table
     * pt_br Atualiza os dados da pessoa física na tabela tbnaturalperson
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateJuridicalData(personModel $personModel): array
    {        
        $sql = "UPDATE tbjuridicalperson 
                   SET ein_cnpj = :einCnpj, 
                       contact_person = :contact, 
                       observation = :observation
                 WHERE idperson = :personId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":einCnpj",$personModel->getEinCnpj());
        $stmt->bindValue(":contact",$personModel->getContactName());
        $stmt->bindValue(":observation",$personModel->getObsevation());
        $stmt->bindValue(":personId",$personModel->getIdPerson());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$personModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates person data
     * pt_br Atualiza os dados da pessoa
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updatePersonData(personModel $personModel): array
    {   
        $aPermissionGroups = $personModel->getPermissionGroupsList();
        $aGroups = $personModel->getPersonGroupsList();
        
        try{
            $this->db->beginTransaction();

            $upd = $this->updatePerson($personModel);

            if($upd['status']){
                //update person address
                $updAddress = $this->updateAddress($upd['push']['object']);
                if($updAddress['status'])
                    $this->loggerDB->info('Person address was updated', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                
                // update natural person data
                if($upd['push']['object']->getPersonNatureId() == 1){
                    // updates data into tbnaturalperson table
                    $updNatural = $this->updateNaturalData($upd['push']['object']);
                    if($updNatural['status'])
                        $this->loggerDB->info('Natural person data was updated', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

                    // bind person with the department
                    if($upd['push']['object']->getIdDepartment() > 0){
                        $updInDepartment = $this->updateInDepartment($upd['push']['object']);
                        if($updInDepartment['status'])
                            $this->loggerDB->info('Natural person has been linked to the department', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                    }

                    // remove link person with the permission groups
                    $delPermGrps = $this->deletePermissionGroup($upd['push']['object']);
                    if($delPermGrps['status'])
                        $this->loggerDB->info('Link between natural person and permission groups was deleted', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

                    // insert new permission group
                    if(!empty($aPermissionGroups) && count($aPermissionGroups) > 0) {
                        foreach($aPermissionGroups as $k=>$v){
                            $upd['push']['object']->setPermissionGroupId($v);
    
                            $retPermGrps = $this->insertPermissionGroup($upd['push']['object']);
                            if($retPermGrps['status'])
                                $this->loggerDB->info('Natural person has been linked to the permission group', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        }
                    }

                    // remove link person with the group
                    $delPermGrps = $this->deletePermissionGroup($upd['push']['object']);
                    if($delPermGrps['status'])
                        $this->loggerDB->info('Link between natural person and group was deleted', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);

                    // insert new group - (attendant)
                    if(!empty($aGroups) && count($aGroups) > 0) {
                        foreach($aGroups as $k=>$v){
                            $upd['push']['object']->setGroupId($v);
    
                            $retGroup = $this->insertGroup($upd['push']['object']);
                            if($retGroup['status'])
                                $this->loggerDB->info('Natural person has been linked to the group', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                        }
                    }
                }else{
                    // updates data into tbjuridicalperson table
                    $updJuridical = $this->updateJuridicalData($upd['push']['object']);
                    if($updJuridical['status'])
                        $this->loggerDB->info('Juridical person data was updated', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
                }
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$upd['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update person info', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates person's status
     * pt_br Atualiza o status da pessoa
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function changePersonStatus(personModel $personModel): array
    {   
        $sql = "UPDATE tbperson SET `status` = :status  WHERE idperson = :personId";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":status",$personModel->getStatus());
            $stmt->bindValue(":personId",$personModel->getIdPerson());
            $stmt->execute();
            
            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying change module data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a list with the attendant's groups
     * pt_br Retorna uma lista com os grupos do atendente
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAttendantGroups(personModel $personModel): array
    {        
        $sql = "SELECT b.idgroup, c.idperson, c.name, d.name company_name 
                  FROM hdk_tbgroup_has_person a, hdk_tbgroup b, tbperson c, tbperson d
                 WHERE a.idgroup = b.idgroup
                   AND b.idperson = c.idperson
                   AND b.idcustomer = d.idperson
                   AND a.idperson = :personId
              ORDER BY c.name ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":personId",$personModel->getIdPerson());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $personModel->setPersonGroupsList((!empty($aRet)) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting the attendant's groups ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Link group to attendant
     * pt_br Vincula o grupo ao atendente
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function linkAttendantGroup(personModel $personModel): array
    {   
        try{
            $this->db->beginTransaction();

            $ins = $this->insertGroup($personModel);

            if($ins['status']){
                $this->loggerDB->info('Group was linked to attendant {$personModel->getIdPerson()}', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying link attendant to group', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Remove the link between group and attendant
     * pt_br Deleta o vincula entre o grupo e o atendente
     *
     * @param  personModel $personModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteAttendantGroup(personModel $personModel): array
    {   
        $sql = "DELETE FROM hdk_tbgroup_has_person WHERE idgroup = :groupId AND idperson = :personId";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":groupId",$personModel->getGroupId());
            $stmt->bindValue(":personId",$personModel->getIdPerson());
            $stmt->execute();
            
            $ret = true;
            $result = array("message"=>"","object"=>$personModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying change module data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
}