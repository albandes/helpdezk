  <?php
class Knowledgebase extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
		if($_SESSION['SES_TYPE_PERSON'] != 1 && $_SESSION['SES_TYPE_PERSON'] != 3 ){
			$smarty = $this->retornaSmarty();
			$smarty->display('nopermission.tpl.html');
			die();
		}
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
        $smarty->display('knowledgebase.tpl.html');
    }	
	
	public function modalInsertCategories(){
		$smarty = $this->retornaSmarty();
		$smarty->assign('categories', $this->getCatArray());
		$smarty->display('modais/knowledge_base/insert_categories.tpl.html');
	} 
	
	public function insertCategory(){
		$name = $_POST['txtName'];
		$idref = $_POST['cmbCategory'];
		if(!$name) return false;		
		$db = new knowledgebase_model();
		if(!$idref) $idref = 0;
		$ins = $db->setCategory($name, $idref);
		if($ins) echo "ok";
		else return false;		
	}
	
	private function getCatArray(){
    	$db = new knowledgebase_model();
		$cat = $db->getCategories("WHERE idcategory_reference = 0");
		$option = array();
    	while (!$cat->EOF) {
			$category = array("id" => $cat->fields['idcategory'], "name" => $cat->fields['name']);
			array_push($option, $category);
			if( $cat->fields['total'] > 0 ){
				$childs = $this->getChildsArray($cat->fields['idcategory'],2);
				foreach ($childs as $child) {
					array_push($option, $child);	
				}				
			}			
			$cat->MoveNext();
		}
        
		return $option;
    }

    private function getChildsArray($id, $ident){
 		$db = new knowledgebase_model();
		$cat = $db->getCategories("WHERE idcategory_reference = $id");
		$j=0;
		$iden = "";
		$option = array();
		while($j != $ident) {
			$iden .= "-";
			$j++;
		}
		while (!$cat->EOF) {
			$category = array("id" => $cat->fields['idcategory'], "name" => $iden." ".$cat->fields['name']);
			array_push($option, $category);
			if($cat->fields['total'] > 0){
				$ident_shild = $ident + 2;
				$childs = $this->getChildsArray($cat->fields['idcategory'],$ident_shild);
				foreach ($childs as $child) {
					array_push($option, $child);	
				}	
			}
			$cat->MoveNext();
		}	
		return $option;
 	}

	public function modalListEditCategories(){
		$smarty = $this->retornaSmarty();
        $smarty->assign('categories', $this->getCatArray());
		$smarty->display('modais/knowledge_base/edit_list_categories.tpl.html');
	}
	
	public function modalEditCategories(){
		$smarty = $this->retornaSmarty();
       	$id = $this->getParam('id');		
		$db = new knowledgebase_model();
		$catid = $db->getCategories("WHERE idcategory = $id");		
		$smarty->assign("idcategory",$catid->fields['idcategory']);
		$smarty->assign("name",$catid->fields['name']);		
		$smarty->assign("idcategory_reference",$catid->fields['idcategory_reference']);
		$smarty->assign('categories', $this->getCatArray());
		$smarty->display('modais/knowledge_base/edit_categories.tpl.html');
	}

	public function modalInsertArticle(){
		$smarty = $this->retornaSmarty();		
		$smarty->assign('categories', $this->getCatArray());
		$smarty->display('modais/knowledge_base/insert_article.tpl.html');
	}
	
	public function insertArticle(){
		$cmbCategory 	= $_POST['cmbCategory'];
		$txtTitle 		= addslashes($_POST['txtTitle']);
		$chkFAQ 		= $_POST['chkFAQ'];
		if(!$chkFAQ) 
		    $chkFAQ 		= 0;
		$txtDescPro 	= $_POST['txtDescPro'];
		$txtSolPro 		= $_POST['txtSolPro'];
		$idperson 		= $_SESSION['SES_COD_USUARIO'];		
		$db = new knowledgebase_model();
		$db->BeginTrans();		
		$ins = $db->setArticle($cmbCategory, $txtTitle, $chkFAQ, $txtDescPro, $txtSolPro, $idperson);
		if(!$ins){
			$db->RollbackTrans();
			return false;
		}
		if($_SESSION["filename"] && $_SESSION["tempname"]){
		 	$idbase = $db->TableMaxID('hdk_base_knowledge','idbase');
		 	$insatt = $db->insertAttachment($idbase, $_SESSION["filename"], $_SESSION["tempname"]);
		 	unset($_SESSION['filename']);
			unset($_SESSION['tempname']);
		 	if(!$insatt){
				$db->RollbackTrans();
				return false;
			}
		}
		$db->CommitTrans();
		echo "ok";
	}

	public function editCategory(){
        print_r($_POST);
        die();
		$name = $_POST['txtName'];
		$idref = $_POST['cmbCategory'];
		$id = $_POST['id'];
		if(!$name) return false;		
		$db = new knowledgebase_model();
		$ins = $db->updateCategory($name, $idref, $id);
		if($ins) echo "ok";
           else return false;
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
			$total = $rsArticles->fields['totalrows'];
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
		$smarty->display('modais/knowledge_base/info_article.tpl.html');
	}

	public function getArticleInfoNote(){
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
		$smarty->display('modais/knowledge_base/info_article_note.tpl.html');
	}

	public function modalEditArticle(){		
		$smarty = $this->retornaSmarty();
		$id = $_POST['id'];		
		$db = new knowledgebase_model();	
		$where = "WHERE a.idbase = $id";
		$rsArticles = $db->getArticles($where);
		$smarty->assign("idbase",$rsArticles->fields['idbase']);
		$smarty->assign("idcategory",$rsArticles->fields['idcategory']);
		$smarty->assign("title",$rsArticles->fields['name']);
		$smarty->assign("problem",$rsArticles->fields['problem']);
		$smarty->assign("solution",$rsArticles->fields['solution']);
        //die('aqui: '.$rsArticles->fields['solution']);
		$smarty->assign('categories', $this->getCatArray());
		$smarty->assign("faq",$rsArticles->fields['faq']);
		$smarty->assign("idattachment",$rsArticles->fields['idattachment']);
		$smarty->assign("real_filename",$rsArticles->fields['real_filename']);
		$smarty->display('modais/knowledge_base/edit_article.tpl.html');
	}


	public function editArticle(){
		$cmbCategory 	= $_POST['cmbCategory'];
		$txtTitle 		= addslashes($_POST['txtTitle']);
		$chkFAQ 		= $_POST['chkFAQ'];
		if(!$chkFAQ) 
			$chkFAQ 	= 0;
		$txtDescPro 	= $_POST['txtDescPro'];
		$txtSolPro 		= $_POST['txtSolPro'];		
		$idbase 		= $_POST['idbase'];
		$idperson_edit	= $_SESSION['SES_COD_USUARIO'];		
		$db = new knowledgebase_model();
		$db->BeginTrans();
		$ins = $db->updateArticle($cmbCategory, $txtTitle, $chkFAQ, $txtDescPro, $txtSolPro, $idbase, $idperson_edit);
		if(!$ins){
			$db->RollbackTrans();
			return false;
		}
		if($_SESSION["filename"] && $_SESSION["tempname"]){
		 	$insatt = $db->insertAttachment($idbase, $_SESSION["filename"], $_SESSION["tempname"]);
		 	unset($_SESSION['filename']);
			unset($_SESSION['tempname']);
		 	if(!$insatt){
			 	$db->RollbackTrans();
				return false;
			}
		 }
		$db->CommitTrans();
		echo "ok";		
	}

	public function upload() {    	
        $this->view('upload_file_knowledgebase.php');
    }
	
	public function removeAtt(){
		$id = $this->getParam('id');
		if(!$id) return false;
		$db = new knowledgebase_model();
		$getAtt = $db->getAttachment("WHERE idattachment = $id");
		$filename = $getAtt->fields['filename'];

		$path = DOCUMENT_ROOT . path . "/app/uploads/helpdezk/knowledgebase/";
		if(unlink($path.$filename)){
			$ren = $db->deleteAttachment($id);
			if(!$ren) return false;
		}else{
			return false;
		}
		echo "ok";
	}

	public function modalDeleteArticle(){
		$smarty = $this->retornaSmarty();		
		$id = $this->getParam('id');
		$smarty->assign("id",$id);
		$smarty->display('modais/knowledge_base/delete_article.tpl.html');
	}

	public function deleteArticle(){
		$id = $_POST['id'];
		$db = new knowledgebase_model();
		$db->BeginTrans();
		$getAtt = $db->getAttachment("WHERE idbase = $id");
		$filename = $getAtt->fields['filename'];
		if($filename){
			$path = DOCUMENT_ROOT . path . "/app/uploads/helpdezk/knowledgebase/";
			if(unlink($path.$filename)){
				$ren = $db->deleteAttachment($getAtt->fields['idattachment']);
				if(!$ren) return false;
			}else{
				return false;
			}
		}
		$rmv = $db->deleteArticle($id);
		if(!$rmv){
			$db->RollbackTrans();
			return false;
		}
		$db->CommitTrans();
		echo "ok";	
	}

}