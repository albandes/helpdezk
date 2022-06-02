<?php 

    if(class_exists('Model')) {
        class DynamicLgpreportgroup_model extends Model {}
    } elseif(class_exists('cronModel')) {
        class DynamicLgpreportgroup_model extends cronModel {}
    } elseif(class_exists('apiModel')) {
        class DynamicLgpreportgroup_model extends apiModel {}
    }

class lgpreportgroup_model extends DynamicLgpreportgroup_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }
    
    public function getPersonsByGroup($where=null,$order=null,$limit=null) { 

      $sql = "SELECT a.idgroup, b.name group_name, d.name person_name
      FROM lgp_tbgroup a, tbperson b, tbperson d, lgp_tbgroup_has_person c 
      WHERE a.idperson = b.idperson 
      AND c.idperson = d.idperson AND a.idgroup = c.idgroup
      $where $order $limit"; //echo "{$sql}\n";

      $ret = $this->db->Execute($sql); //echo "{$sql}\n";

      if($ret)
          return array('success' => true, 'message' => '', 'data' => $ret);
      else
          return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

  }

  //Search for the entities that, in tbperson, the idtyperson is equal to $typePerson value
  public function getGroups($typePerson){

    $sql = "SELECT a.idgroup, b.name 
    FROM lgp_tbgroup a, tbperson b WHERE a.idperson = b.idperson AND idtypeperson = $typePerson ORDER BY b.name ASC"; //echo "{$sql}\n";

    $ret = $this->db->Execute($sql); //echo "{$sql}\n";

    if($ret)
        return array('success' => true, 'message' => '', 'data' => $ret);
    else
        return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

  }

  public function getLgpTypePerson($typeName) {

    $sql = "SELECT idtypeperson FROM tbtypeperson WHERE `name` = '{$typeName}'";

    return $this->selectPDO($sql);

  }

  public function getGroupName($idgroup){

    $sql = "SELECT a.name
    FROM tbperson a, lgp_tbgroup b WHERE a.idperson = b.idperson AND b.idgroup = $idgroup";

    return $this->selectPDO($sql);

  }

}