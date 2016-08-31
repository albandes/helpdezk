<?php

class Downloads extends Controllers {
 
    public function showinstruction() {
        $smarty = $this->retornaSmarty();
        $id = $this->getParam('id');
        $bd = new downloads_model();
        $ins = $bd->getInstruction($id);
        $instructions = $ins->fields['instruction'];
        $smarty->assign('instructions', $instructions);
        $smarty->display('downloadinstructions.tpl.html');
    }

    public function getFile() {
        //error_reporting(E_ALL);
        $path = $this->getParam('pathAt');
        $name = $this->getParam('nameAt');
        $filename = $this->getParam('filename');
		$path = str_replace('-', '/', $path);
		$path = str_replace('../', '', $path);
        $download = new httpdownload();
        $download->set_byfile(DOCUMENT_ROOT . $path .$filename);
        $download->filename = $name;
        $download->use_resume = false;
        $download->set_mime();
        $download->download();

  //      echo "OK";
    }
	
	public function getFile2(){
		$bd = new downloads_model();
		$filename = $this->getParam('id');
		$type = $this->getParam('type');
		$path_default = $this->getConfig('path_default');
		switch ($type) {
			case 'note':
				$res = $bd->getDownloadNote($filename);
				if (path == "/.." || path == "") $path = "app/uploads/helpdezk/noteattachments/";
				else $path = $path_default . "/app/uploads/helpdezk/noteattachments/";
				$name = $res->fields['file_name'];
				$ext = strrchr($name, '.');
				break;
				
			case 'request':
				$res = $bd->getDownloadRequest($filename);
				if (path == "/.." || path == "") $path = "app/uploads/helpdezk/attachments/";
				else $path = $path_default . "/app/uploads/helpdezk/attachments/";
				$name = $res->fields['file_name'];
				$ext = strrchr($name, '.');
				break;			
		}
		
		$file_name = DOCUMENT_ROOT . $path . $filename . $ext;
		//die($file_name);
		// required for IE		  
		if(ini_get('zlib.output_compression')) {
 			ini_set('zlib.output_compression', 'Off');  
		}

		// get the file mime type using the file extension
		switch(strtolower(substr(strrchr($file_name, '.'), 1))) {
			case 'pdf': $mime = 'application/pdf'; break;
			case 'zip': $mime = 'application/zip'; break;
			case 'jpeg':
			case 'jpg': $mime = 'image/jpg'; break;
			default: $mime = 'application/force-download';
		}
		header('Pragma: public');   // required
		header('Expires: 0');    // no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_name)).' GMT');
		header('Cache-Control: private',false);
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename="'.basename($name).'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($file_name));  // provide file size
		header('Connection: close');
		readfile($file_name);    // push it out
		exit();
	}
}
?>
