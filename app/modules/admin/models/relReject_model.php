<?php
class relReject_model extends Model{
    public function getRejectRequest($where = NULL){
        $sel = $this->select("SELECT sol.code_request, sol.subject, apont.description FROM hdk_tbrequest sol, hdk_tbnote apont WHERE sol.code_request = apont.code_request AND sol.idstatus = 6 AND apont.idtype = 3 AND (apont.description LIKE '%Solicita&ccedil;&atilde;o n&atilde;o pode ser atendida:%' OR apont.description LIKE '%Request Could not be attended:%') $where order by sol.code_request");
        return $sel;
    }
}
?>
