<?php
class relStatus_model extends Model{
    public function getStatusRequest($where = NULL){
        $sel = $this->select("select stat.user_view, count(sol.code_request) as QTD_SOLICITACAO from hdk_tbstatus stat, hdk_tbrequest sol, hdk_tbrequest_in_charge solgrup where sol.idstatus = stat.idstatus and sol.code_request = solgrup.code_request AND ind_in_charge = 1 $where group by stat.idstatus_source, stat.user_view,stat.idstatus order by stat.idstatus_source,stat.idstatus");
        return $sel;
    }
}
?>
