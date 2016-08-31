<?php
session_start();
class requestsearchuser extends Controllers {
     public function index() {
        $smarty = $this->retornaSmarty();
        $smarty->display('searchUser.tpl.html');
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
                case 'NAME':
                    $where = "and  p.NAME LIKE '%$query%' ";
                    break;
                default:
                    $where = "";
                    break;
            }            
        }
        
        if($_POST['letter']){
            $letter = $_POST['letter'];
            $where = "and p.NAME like '".$letter."%'";
        }
        
        if (!$sortname or !$sortorder) {
            
        } else {
            $order = " ORDER BY $sortname $sortorder ";
        }

        //$limit = "LIMIT $start, $rp";        
        $db = new requestsearchuser_model();
        $rs = $db->selectUser($where, $order, $limit);
        
        //echo $rs;
        $qcount = $db->countUser($where, $order, $limit);
        
        $total = $qcount->fields['total'];
        
        $data['page'] = $page;
        $data['total'] = $total;
        
        if($total>=1){
            while (!$rs->EOF) {
                $rows[] = array(
                    "id" => $rs->fields['IDPERSON'],
                    "cell" => array(
                        utf8_encode($rs->fields['NAME'])
                        ,utf8_encode($rs->fields['COMPANY'])
                    )
                );
                $rs->MoveNext();
            }
        }else{
            $rows[] = array(
                "id" => null,
                "cell" => array(
                    null
                    ,null
                )
            );
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
         $data=$rs->fields['IDPERSON'].'|'.$rs->fields['NAME'].'|'.$rs->fields['IDCOMPANY'];         
         echo $data;
     }
}
?>
