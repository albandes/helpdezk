<?php

class Faq extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
 	
	public function getCategories(){		
		$smarty = $this->retornaSmarty();
    	$langVars = $smarty->get_config_vars();
		$root = $this->getParam('root');
		$i=0;
		if($root == "source"){
			$id = 0;
			$categories[] = array(
				"text"  => $langVars['Show_all_grid'],
				"data" => "all"
			);
			$i++;
		}else{
			$id = $root;
		}		
		$db = new knowledgebase_model();
		$cat = $db->getCategories("WHERE idcategory_reference = $id");		
		while (!$cat->EOF) {
			$categories[] = array(
				"text"  => $cat->fields['name'],
				"expanded" => false,
				"data" => $cat->fields['idcategory']
			);				
			if( $cat->fields['total'] > 0 ){
				$categories[$i]['children'] = $this->getChilds($cat->fields['idcategory']);
			}
			$i++;
			$cat->MoveNext();
		}
		echo json_encode($categories);
	}

 	private function getChilds($id){
 		$db = new knowledgebase_model();
		$cat = $db->getCategories("WHERE idcategory_reference = $id");
		$i=0;
		while (!$cat->EOF) {
			$childs[] = array(
				"text"  => $cat->fields['name'],
				"data"  => $cat->fields['idcategory']
			);
			if($cat->fields['total'] > 0){
				$childs[$i]['id'] = $cat->fields['idcategory'];
				$childs[$i]['hasChildren'] = true;
			}
			$i++;
			$cat->MoveNext();
		}	
		return $childs;
 	}

    public function index() {
        $smarty = $this->retornaSmarty();
        $smarty->display('faq.tpl.html');
    }	
	
	
    public function json() {
    	$smarty = $this->retornaSmarty();
    	$langVars = $smarty->get_config_vars();		
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
        if($_POST['ID_CATEGORY'] == "all"){
			$where .= "";
		}else{
			$where .= "WHERE a.idcategory = ".$_POST['ID_CATEGORY'];
		}
        if ($query) {
            switch ($qtype) {
                default:
                    if($_POST['ID_CATEGORY'] == "all"){
						$where .= "WHERE  $qtype LIKE '%$query%' ";
					}else{
						$where .= "AND  $qtype LIKE '%$query%' ";
					}
                    break;
            }
        }		
        if (!$sortname or !$sortorder) {
            
        } else {
            $order = " ORDER BY $sortname $sortorder ";
        }
        $limit = "LIMIT $start, $rp";		
		$idperson = $_SESSION['SES_COD_USUARIO'];
		$idcompany = $_SESSION['SES_COD_EMPRESA'];		
        $bd = new knowledgebase_model();		
		$rsArticles = $bd->getArticles($where, $order, $limit);		
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$rstotal = $this->found_rows();
        	$total = $rstotal->fields['found_rows'];
		} elseif ($database == 'oci8po') {
			$total = $rsArticles->fields['rnum'];
			if(!$total) $total = 0;
		}		
        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rsArticles->EOF) {			
            $rows[] = array(
                "id" => $rsArticles->fields['idbase'],
                "cell" => array(
                	"<a href='javascript:;' data-id='".$rsArticles->fields['idbase']."' class='linhas openArticle'>".$rsArticles->fields['category']."</a>",
					"<a href='javascript:;' data-id='".$rsArticles->fields['idbase']."' class='linhas openArticle'>".$rsArticles->fields['name']."</a>",
					$rsArticles->fields['author']
                )
            );
            $rsArticles->MoveNext();
        }        
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }	
	
	public function getArticleInfo(){
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$path_default = $this->getConfig('path_default');
		$bd = new knowledgebase_model();	
		$where = "WHERE a.idbase = $id";
		$rsArticles = $bd->getArticles($where);

		$smarty->assign("idbase",$rsArticles->fields['idbase']);
		$smarty->assign("category",$rsArticles->fields['category']);
		$smarty->assign("title",$rsArticles->fields['name']);
		$smarty->assign("problem",$rsArticles->fields['problem']);
		$smarty->assign("solution",$rsArticles->fields['solution']);
		$smarty->assign("author",$rsArticles->fields['author']);
		$smarty->assign("idattachment",$rsArticles->fields['idattachment']);
		$smarty->assign("real_filename",$rsArticles->fields['real_filename']);
		$smarty->assign("filename",$rsArticles->fields['filename']);
		$smarty->assign("path_file",$path_default . "-app-uploads-helpdezk-knowledgebase-");		

		$smarty->assign("date",$this->formatDateHour($rsArticles->fields['date_register']));

		$smarty->assign("author_edit",$rsArticles->fields['author_edit']);
		if($rsArticles->fields['date_edit'])
			$smarty->assign("date_edit",$this->formatDateHour($rsArticles->fields['date_edit']));
		else
			$smarty->assign("date_edit","");
		$smarty->assign("faq",$rsArticles->fields['faq']);
		$smarty->display('modais/faq/info_article.tpl.html');
	}



}
