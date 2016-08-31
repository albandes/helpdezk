<?php
error_reporting(1);
include 'includes/config/config2.php';
session_start();


if (substr($config['path_default'], 0, 1) != '/') {
    $config['path_default'] = '/' . $config['path_default'];
}
define('path', $config['path_default']);
$document_root = $_SERVER['DOCUMENT_ROOT'];
if (substr($document_root, -1) != '/') {
    $document_root = $document_root . '/';
}
define('DOCUMENT_ROOT', $document_root);
define('theme', $config['theme']);

require_once(SMARTY . 'Smarty.class.php');

$smarty = new Smarty;
$smarty->debugging = false;
$smarty->compile_dir = "system/templates_c/";
$smarty->config_load(DOCUMENT_ROOT . path . '/app/lang/' . $config['lang'] . '.txt', $config['lang']);
$smarty->assign('lang', $config['lang']);
$smarty->assign('pagetitle', $config['page_title']);
$langVars = $smarty->get_config_vars();
$langVars2 = $smarty->get_template_vars();

if (path == '/..') {
    if ($custom_attach_path) {
        $path_attach = DOCUMENT_ROOT . $custom_attach_path;
    } else {
        $path_attach = DOCUMENT_ROOT . "/app/uploads/helpdezk/services/";
    }
} else {
    if ($custom_attach_path) {
        $path_attach = DOCUMENT_ROOT . $custom_attach_path;
    } else {
        $path_attach = DOCUMENT_ROOT . path . "/app/uploads/helpdezk/services/";
    }
}

if (!(isset($_SESSION["SES_COD_ATTACHMENT"])) || ($_SESSION["SES_COD_ATTACHMENT"] == "")) {
    $_SESSION["SES_COD_ATTACHMENT"] = "0";
}

if (isset($_FILES['ARQUIVO'])) {
            // Checks if the file was sent without errors
            if (!isset($_FILES['ARQUIVO']) || $_FILES['ARQUIVO']['error'] != 0) {
                hddie($langVars['Manage_fail_import_file'].'<!--' . $_FILES['ARQUIVO']['error'] . '-->');
            }

            // Initializes the arrays
            $company = $department = array();
            
			$DB = new person_model();

			$tmpCompanies = $DB->getCompanies();
            foreach ($tmpCompanies as $data) {
                $company[trim(strtoupper($data['name']))] = $data['idcompany'];
            }
			$company = array_map('clean_encode', $company);
			//print_r($company); 
            // Frees up some memory ...
            unset($tmpCompanies);

            //  Move the target file to prevent it from being deleted from tmp...
            $csvFile = $path_attach . "/ATIS.csv";
            if (!@move_uploaded_file($_FILES['ARQUIVO']["tmp_name"], $csvFile)) {
                die($langVars['Manage_fail_move_file']);
            }
            // Inicia a transaçao
            $DB->BeginTrans();

            // Abre o arquivo e inicia o contador
            if (!@$arquivo = fopen($csvFile, "r")) {
				die($langVars['Manage_fail_open_file_in']."$csvFile.".$langVars['Manage_fail_open_file_per']);
            }
            $i = 0;
			$write = 0;
            while (!feof($arquivo)) {
                $i++;
                $linha = fgets($arquivo, 4096);

                if (strlen($linha) == 0) {
                    continue; // Empty line....
                }

                $data = split(";", $linha);
                $numCols = count($data);
                if ($numCols != 14 && $numCols != 16 ) {
                    $DB->RollbackTrans();
					$error = str_replace("%", $i, $langVars['Error_Number_columns'] ); 
                    die($error . " Num cols: ".$numCols . '<hr>');
                } 

				// Clean and encode the array
				$data = array_map('clean_encode', $data);
                // Cadastra ou recupera a Area
				//echo "<pre>";
				//print_r($company);
				//print "<br>";
				//print_r($data);

				// Check login	
				$rs_person = $DB->selectPersonFromLogin($data[0]);
				if ($rs_person->RecordCount() != 0) {
						echo str_replace("%", $i, $langVars['Error_Login_Found']) . '<hr>';
						continue ;
				} 				
				// Check Login Type
				if( !($data[3] >= 1 and $data[3] <= 3) ) {
					echo str_replace("%", $i, $langVars['Error_Login_Type']) . '<hr>';
					continue ;
				}
				// Check Access Level
				if( $data[5] == "user" ) {
					$typeuser = 2;
				} elseif( $data[5] == "operator" ) {
					$typeuser = 3;
				} else {
					echo str_replace("%", $i, $langVars['Error_Invalid_AccessLevel']) . '<hr>';
					continue ;
				}
				// Check Email
				if (strlen($data[4]) > 0 ) {
					if(!filter_var($data[4], FILTER_VALIDATE_EMAIL)){ 
						echo str_replace("%", $i, $langVars['Error_Invalid_Email']) . '<hr>';
						continue ;
					}
				}
				// Check Company and Department
                if (!array_key_exists(strtoupper($data[6]), $company)) 
				{
                    echo str_replace("%", $i, $langVars['Error_Company_NotFound']) . '<hr>';
					continue ;
                } else {
					$idcompany = $company[strtoupper($data[6])] ;
					$name = strtoupper($data[7]);

					if($config["db_connect"] == "mysqlt"){
						$rs = $DB->getDepartment(" WHERE ucase(name) = '$name' and idperson = $idcompany ");
					}elseif($config["db_connect"] == "oci8po"){
						$rs = $DB->getDepartment(" WHERE upper(name) = '$name' and idperson = $idcompany ");
					}
					
					if ($rs->RecordCount() == 0) {
						$insDep = $DB->insertDepartment($idcompany, $data[7]);						
						if($insDep){
							$iddepartment = $DB->TableMaxID('hdk_tbdepartment','iddepartment');
							echo $langVars['Recorded_Department'] . " - ".$data[7] .'<br>';
						}else{
							echo str_replace("%", $i, $langVars['Error_Department_Insert']) . '<hr>';
							$DB->RollbackTrans();
							continue;
						}
					} else {
						$iddepartment = $rs->fields['iddepartment'] ;
					}
					
				}
				//print "dep: " . $iddepartment . "<br>";
				// Gender
				if( !($data[13] == "M" or $data[13] == "F") ) {
					echo str_replace("%", $i, $langVars['Error_Invalid_Gender']) . '<hr>';
					continue ;
				}
				// End Checks
				

				// Vip user
				if ($data[8] >=0 and $data[8] <=1) {
					if($data[8] == 0) { 
						$vip = 'N'; 
					} else {
						$vip = 'Y'; 
					}	
				} else {
					$vip = 'N';
				}	
				// Status
				if ($data[12] >=0 and $data[12] <=1) {
					if($data[12] == 0) { 
						$status = 'N'; 
					} else {
						$status = 'A'; 
					}	
				} else {
					$status = 'N';
				}	
				$logintype		= $data[3];
				$natureperson 	= '1';
				$idtheme 		= '1';
				$name 			= $data[2] ;
				$email 			= $data[4] ;
				$dtcreate 		= date('Y-m-d H:i:s');
				$phone  		= $data[9] ;
				$branch 		= $data[10] ;
				$mobile 		= $data[11] ;
				$login 			= $data[0] ;
				$password 		= md5($data[1]);

				$ins = $DB->insertPerson($logintype, $typeuser, $natureperson, $idtheme, $name, $email, $dtcreate, $status, $vip, $phone, $branch, $mobile, $login, $password);
				if(!$ins) {
					echo str_replace("%", $data[0], $langVars['Error_Unable_Write']) . '<hr>';
					$DB->RollbackTrans();
					continue;
				} else {	
					$idperson = $ins;
				}
				
				$insNatural = $DB->insertNaturalData($idperson, '', '', $data[13]);
				if(!$insNatural) {
					echo str_replace("%", $data[0], $langVars['Error_Unable_Write']) . '<hr>';
					$DB->RollbackTrans();
					continue;
				}
				$insAddress = $DB->insertAdressData($idperson, 1, 1, 1, 2, '', '', '');
				if(!$insAddress) {
					echo str_replace("%", $data[0], $langVars['Error_Unable_Write']) . '<hr>';
					$DB->RollbackTrans();
					continue;
				}
				$insDepart = $DB->insertInDepartment($idperson, $iddepartment);
				
				if(!$insDepart) {
					echo str_replace("%", $data[0], $langVars['Error_Unable_Write']) . '<hr>';
					$DB->RollbackTrans();
					continue;
				} 
				
				
				if($data[14] && $data[15]){					
					$cc = new costcenter_model();
					$name_cc = strtoupper($data[14]);
					$code_cc = strtoupper($data[15]);
					//Check id exist costcenter
					if($config["db_connect"] == "mysqlt"){
						$rs = $cc->selectCostCenter("AND ucase(tbc.name) = '$name_cc' and tbp.idperson = $idcompany and ucase(tbc.cod_costcenter) = '$code_cc'");
					}elseif($config["db_connect"] == "oci8po"){
						$rs = $cc->selectCostCenter("AND upper(tbc.name) = '$name_cc' and tbp.idperson = $idcompany and upper(tbc.cod_costcenter) = '$code_cc'");
					}
					//If not exist is created
					if ($rs->RecordCount() == 0) {
						$ins = $cc->insertCostCenter($data[14],$idcompany,$data[15]);
						if($ins){
							$idcostcenter = $cc->TableMaxID('hdk_tbcostcenter','idcostcenter');
							echo $langVars['Recorded_Cost_Center'] .'<br>';
						}else{
							echo str_replace("%", $data[14], $langVars['Error_Insert_Cost_Center']) . '<br>';
							$DB->RollbackTrans();
							continue;
						}
					} else {
						$idcostcenter = $rs->fields['idcodcenter'];
						echo str_replace("%", $idcostcenter, $langVars['Using_Cost_Center']) . '<br>';
					}					
					//Insert relationship person with cost center
					$ins_ccp = $cc->insertCostCenterPerson($idcostcenter, $idperson);
					if(!$ins_ccp){
						echo str_replace("%", "insertCostCenterPerson idcc: $idcostcenter idper: $idperson", $langVars['Error_Insert_Cost_Center']) . '<hr>';
						$DB->RollbackTrans();
						continue;
					}					
				}
				
				echo str_replace("%", $data[0], $langVars['Import_People_Registered']) . '<hr>';
				$DB->CommitTrans();
				$write++;
				
			}	
				
			$msg = str_replace("%", $i, $langVars['Import_People_Finish'])	;
			$msg = str_replace("#", $write, $msg)	;

            
			
            echo '<br>' . $msg . '<br><br>';
            echo '<div style="width:100%; text-align:left"><a href="javascript:window.print()">'.$langVars['Print'].'</a></div>';
            exit;
        }
		
		
		function clean_encode($str) {
			return utf8_encode(trim(addslashes($str)));
		}   
  		
		
?>
<html>
    <head>
        
        <link rel='stylesheet' type='text/css' href='<?php echo path; ?>/app/themes/<?php echo theme; ?>/style.css' />

        <script>
            function validaArquivoCSV() {
                arquivo = document.formSe.ARQUIVO.value.toLowerCase();
                tipo = arquivo.split(".");
                if (tipo[1] != "csv") {
                    alert("<?php echo $langVars['Alert_wrong_extension_csv']?>");
                    return false;
                } else
                    return true;
            }
                       
        </script>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    </head>
    <body id="upload-anexo" bgcolor="black">
        <form action="importservices/import/" accept-charset=utf-8 method="post" enctype="multipart/form-data" name="formSe" onSubmit="return validaArquivoCSV();" target="_oframe">    
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>                       
                        <input name="ARQUIVO" id="ARQUIVO" type="file"/>
						<input name="Submit" type="submit" class="btnOrange tp1" id="Submit" value="<?=$langVars['Import']?>"/>
                    </td>
                </tr>
                <tr>
                	
                </tr>
            </table>
        </form>
    </body>
</html>
