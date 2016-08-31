<?php

class Downloads extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
    public function index() {
        $smarty = $this->retornaSmarty();
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("downloads/");
        $access = $this->access($user, $program, $typeperson);		
        $db = new downloads_model();
        $categories = $db->selectCategories();
        while (!$categories->EOF) {
            $campos[] = $categories->fields['iddownloadcategory'];
            $valores[] = $categories->fields['category'];
            $categories->MoveNext();
        }
        $smarty->assign('categoryids', $campos);
        $smarty->assign('categoryvals', $valores);
        $date = date('Y-m-d');
        $date = $this->formatDate($date);
        $smarty->assign('date', $date);
        $smarty->display('downloads.tpl.html');
    }

    public function json() {
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
            switch ($qtype) {
                case 'name':
                    $where = "and  $qtype LIKE '$query%' ";
                    break;
                default:
                    $where = "";
                    break;
            }
        }
        if (!$sortname or !$sortorder) {
            
        } else {
            $database = $this->getConfig('db_connect');
            if ($database == 'oci8po') {
                if($sortname == "date") $sortname = "date_";
            } 

            $order = " ORDER BY $sortname $sortorder ";
        }

        $limit = "LIMIT $start, $rp";

        $bd = new downloads_model();
        $rs = $bd->getDownloads($where, $order, $limit);

        $qcount = $bd->countDownloads($where);
        $total = $qcount->fields['total'];

        $data['page'] = $page;
        $data['total'] = $total;
        while (!$rs->EOF) {
            if ($rs->fields['status'] == 'A') {
                $status = "<img src='".path."/app/themes/".theme."/images/active.gif' height='10px' width='10px'>";
            } else {
                $status = "<img src='".path."/app/themes/".theme."/images/notactive.gif' height='10px' width='10px'>";
            }
            $date1 = $this->formatDate($rs->fields['dt']);
            $rows[] = array(
                "id" => $rs->fields['iddownload'],
                "cell" => array(
                    "<img src='" . path . "/app/themes/".theme."/images/floppy.png' height='15px' width='15px'>",
                    $rs->fields['name'],
                    $rs->fields['category'],
                    $rs->fields['version_description'],
                    $date1,
                    $status
                )
            );
            $rs->MoveNext();
        }
        $data['rows'] = $rows;
        $data['params'] = $_POST;
        echo json_encode($data);
    }

    public function upload() {    	
        $this->view('upload_file.php');
    }

    public function categoryInsert() {
        $categoryname = $_POST['newcategoryname'];
        $bd = new downloads_model();
        $ins = $bd->insertDownloadCategory($categoryname);
        $total = $bd->selectMaxDownCategory();
        if ($ins) {
            echo $total;
        } else {
            return false;
        }
    }

    public function categories() {
    	$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
        $bd = new downloads_model();
        $sel = $bd->selectCategories();
        $count = $sel->RecordCount();
        
		echo '<option value="">'.$langVars['Select_category'].'</option>';
		
        $i = 0;
        while (!$sel->EOF) {
            $campos[] = $sel->fields['iddownloadcategory'];
            $valores[] = $sel->fields['category'];
            echo "<option value='$campos[$i]' >$valores[$i]</option>";
            $i++;
            $sel->MoveNext();
        }
        
    }

    public function showinstruction() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new downloads_model();
        $ins = $bd->getInstruction($id);
        $instructions = $ins->fields['instruction'];
        $smarty->assign('instructions', $instructions);
        $smarty->display('downloadinstructions.tpl.html');
    }

	public function sessionCheck(){
		if($_SESSION['filename'] && $_SESSION['tempname'])
			echo 1;
		else 
			return false;
	}
	
	public function modalinsert(){
    	
		unset($_SESSION['filename']);
		unset($_SESSION['tempname']);
		
        $smarty = $this->retornaSmarty();
        
        $db = new downloads_model();
        $categories = $db->selectCategories();
        while (!$categories->EOF) {
            $campos[] = $categories->fields['iddownloadcategory'];
            $valores[] = $categories->fields['category'];
            $categories->MoveNext();
        }
        $smarty->assign('categoryids', $campos);
        $smarty->assign('categoryvals', $valores);
        $smarty->display('modais/downloads/downloadsinsert.tpl.html');
    }

    public function insert() {
		$title = $_POST['title'];
		$date  = $this->formatSaveDate($_POST['date']);
		$categories = $_POST['categories'];
		$version = $_POST['version'];
		$shortdesc = addslashes($_POST['shortdesc']);
		$downdescription = addslashes($_POST['downdescription']);
		$local = $_POST['local'];
		$url = $_POST['url'];
		$restrict = $_POST['restricted'];
		$filename = $_SESSION['filename'];
		$downloadfilename = $_SESSION['tempname'];

		if($local == "U"){
			if($filename){
				$path = DOCUMENT_ROOT . path . "/app/uploads/files/";
				unlink($path.$filename); 
			}	
			$filename = "";
			$downloadfilename = "";
		}
		if($local == "C"){
			$url = "";
		}
		if(!$restrict)	$restrict = "N";

        
        $bd = new downloads_model();
        $ret = $bd->insertDownload($categories, $title, $shortdesc, $filename, $date, $downloadfilename, $url, $version, $restrict, $downdescription);
        if ($ret) {
            echo "ok";
        } else {
            return false;
        }

    }

    public function getFile() {
        error_reporting(E_ALL);
        $path = $this->getParam('path');
        $name = $this->getParam('name');
        $filename = $this->getParam('filename');

        $path = str_replace('-', '/', $path);
        $download = new httpdownload();
        $download->set_byfile(DOCUMENT_ROOT . $path .$filename);
        $download->filename = $name;
        $download->use_resume = false;
        $download->set_mime();
        $download->download();

        echo "OK";
    }
    
    public function modaledit(){
        
        $smarty = $this->retornaSmarty();
        $db = new downloads_model();
        $id = $this->getParam('id');
        $data = $db->getDownloadData($id);
		
        $title = $data->fields['name'];
        $short_desc = $data->fields['description'];
        $filename = $data->fields['file_name'];
        $version = $data->fields['version_description'];
        $instruction = $data->fields['instruction'];
        $category = $data->fields['iddownloadcategory'];
        $url = $data->fields['file_url'];
		$restricted = $data->fields['restricted'];
		$date = $data->fields['dt'];
		
        $downloadname = utf8_encode($data->fields['downloadname']);
        $_SESSION['filename'] = $filename;
        $_SESSION['tempname'] = $downloadname;
        $categories = $db->selectCategories();
        while (!$categories->EOF) {
            $campos[] = $categories->fields['iddownloadcategory'];
            $valores[] = $categories->fields['category'];
            $categories->MoveNext();
        }

		if($restricted == "N")
			$restricted = "";
		else 
			$restricted = "checked='checked'";
		
        $smarty->assign('id', $id);
        $smarty->assign('categoryids', $campos);
        $smarty->assign('categoryvals', $valores);
        $smarty->assign('title', $title);
        $smarty->assign('short_desc', $short_desc);
        $smarty->assign('filename', $filename);
        $smarty->assign('version', $version);
        $smarty->assign('instruction', $instruction);
        $smarty->assign('category', $category);
		$smarty->assign('restricted', $restricted);
        $smarty->assign('url', $url);
		$path_default = $this->getConfig('path_default');
        $file = "<a href='javascript:;' onclick=\"openDownloadPopUP('".$path_default."-app-uploads-files-','$filename','$downloadname');\" class='file' id='" . path . "/app/uploads/files/".$filename."' name='" . path . "/app/uploads/files/".$filename."'><img src='" . path . "/app/themes/".theme."/images/floppy.png' width='15px' height='15px'>" . $downloadname . "</a>";
        $date = $this->formatDate($date);
        $smarty->assign('downloadname', $downloadname);
        $smarty->assign('file', $file);
        $smarty->assign('date_down', $date);
        $smarty->display('modais/downloads/downloadsedit.tpl.html');
    }
    
    public function update(){
    	$id = $_POST['id'];
    	$title = $_POST['title'];
		$date  = $this->formatSaveDate($_POST['date']);
		$categories = $_POST['categories'];
		$version = $_POST['version'];
		$shortdesc = addslashes($_POST['shortdesc']);
		$downdescription = addslashes($_POST['downdescription']);
		$local = $_POST['local'];
		$url = $_POST['url'];
		$restrict = $_POST['restricted'];
		$filename = $_SESSION['filename'];
		$downloadfilename = $_SESSION['tempname'];
	
		if($local == "U"){
			if($filename){
				$path = DOCUMENT_ROOT . path . "/app/uploads/files/";
				unlink($path.$filename); 
			}	
			$filename = "";
			$downloadfilename = "";
		}
		if($local == "C"){
			$url = "";
		}
		if(!$restrict)	$restrict = "N";
    	
        $bd = new downloads_model();
        
        $ret = $bd->updateDownload($categories, $title, $shortdesc, $filename, $date, $downloadfilename, $url, $version, $restrict, $downdescription, $id);
        if ($ret) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	
	public function deactivatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/downloads/downloadsdisable.tpl.html');
	}
    
    public function deactivate() {
        $id = $_POST['id'];		
        $bd = new downloads_model();
        $dea = $bd->downloadDeactivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function activatemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/downloads/downloadsactive.tpl.html');
	}

    public function activate() {
        $id = $_POST['id'];
        $bd = new downloads_model();
        $dea = $bd->downloadActivate($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function deletemodal() {
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/downloads/downloadsdelete.tpl.html');
	}
    
    public function delete() {
    	$id = $_POST['id'];
    	$bd = new downloads_model();
		$res = $bd->getDownloadData($id);
		$file = $res->fields['file_name'];
		
		if($file){
			$path = DOCUMENT_ROOT . path . "/app/uploads/files/".$file;
			if(file_exists($path)){
				unlink($path);
			}
		}
		
        $dea = $bd->downloadDelete($id);
        if ($dea) {
            echo "ok";
        } else {
            return false;
        }
    }
    

}
?>
