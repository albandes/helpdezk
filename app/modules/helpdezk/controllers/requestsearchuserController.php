<?php
session_start();
class requestsearchuser extends Controllers {
     public function index() {
        $smarty = $this->retornaSmarty();
        $smarty->display('modais/searchUser.tpl.html');
     }
     
     public function json(){
        $prog = "";
        $path = "";

        $page = $_POST['page'];
        $rp = $_POST['rp'];

        if (!$sortorder)
            $sortorder = 'asc';

        if (!$page)
            $page = 1;
        if (!$rp)
            $rp = 10;

        $start = (($page - 1) * $rp);

        $limit = "LIMIT $start, $rp";

        $query = $_POST['query'];
        $qtype = $_POST['qtype'];

        $sortname = $_POST['sortname'];
        $sortorder = $_POST['sortorder'];
        $where = "";
        if ($query) {
            $_POST['letter'] = null;
            switch ($qtype) {
                case 'person.name':
                    $where = "and  person.name LIKE '%$query%' ";
                    break;
                default:
                    $where = "";
                    break;
            }
        }
        
        if($_POST['letter']){
            $letter = $_POST['letter'];
            $where = "and person.name like '".$letter."%'";
        }
        
        if (!$sortname or !$sortorder) {
            
        } else {
            $order = " ORDER BY $sortname $sortorder ";
        }

        $db = new requestsearchuser_model();
        $rs = $db->selectUser($where, $order, $limit);
        //echo $rs;
        $qcount = $db->countUser($where, $order, $limit);
        
        $total = $qcount->fields['total'];
        
        $data['page'] = $page;
        $data['total'] = $total;
        
        while (!$rs->EOF) {
            $rows[] = array(
                "id" => $rs->fields['idperson'],
                "cell" => array(
                    $rs->fields['pname'],
                    $rs->fields['cname']
                )
            );
            $rs->MoveNext();
        }
     
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
     }
     
     function getUser(){
         $id=$_POST['id'];
         $db = new requestsearchuser_model();
         $where=" AND person.idperson='$id'";
         $rs = $db->selectUser($where);
         $data=$rs->fields['idperson'].'|'.$rs->fields['pname'].'|'.$rs->fields['idcompany'];         
         echo $data;
     }
}
?>
