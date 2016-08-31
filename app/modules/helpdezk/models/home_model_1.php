<?php
class home_model extends Model{
    public function selectUserLogin($cod_usu){
        $ret = $this->select("select login from tbperson where idperson = $cod_usu");
        $nom = $ret->fields['login'];
        return $nom;
    }
    
    public function selectMenu(){
        return $this->select("select tbm.idmodule as idmodule_pai, tbm.name as module, tbpc.idmodule as idmodule_origem, tbpc.name as category, 
            tbpc.idprogramcategory as category_pai, tbp.idprogramcategory as idcategory_origem, tbp.name as program,   tbp.controller as controller,
            tbp.idprogram as idprogram from tbmodule tbm, tbprogramcategory tbpc, tbprogram tbp where tbm.idmodule=tbpc.idmodule and 
            tbp.idprogramcategory=tbpc.idprogramcategory and tbp.status='A' order by idmodule_pai");        
    }
    
}
?>
