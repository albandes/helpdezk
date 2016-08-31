<?php

class Evaluate extends Controllers {
 

	public function index(){
		$smarty = $this->retornaSmarty();
		$langVars = $smarty->get_config_vars();
		$token = $this->getParam('token');
		
		$db = new logos_model();
        $loginlogo = $db->getLoginLogo();
        $smarty->assign('loginlogo', $loginlogo->fields['file_name']);
        $smarty->assign('loginheight', $loginlogo->fields['height']);
        $smarty->assign('loginwidth', $loginlogo->fields['width']);
		$smarty->assign('versao', 'Parracho');
        $smarty->assign('Company_name', 'Sistema Helpdezk - Pipegrep ');
        $smarty->assign('release', '1.0');
		$smarty->assign('lang_default', $this->getConfig('lang'));
		$smarty->assign('path', path);
		$smarty->assign('hdk_url', $this->getConfig('hdk_url'));
		$csvFile = DOCUMENT_ROOT . path . "/version.txt";
        if ($arquivo = fopen($csvFile, "r")) {
            while (!feof($arquivo)) {
                $i++;
                $version = fgets($arquivo, 4096);
            }
            $smarty->assign('version', $version);
        } else {
            $smarty->assign('version', '');
        }
		if($enterprise) {
			$smarty->assign('site', 'HelpDEZK.com.br');
			$smarty->assign('urlsite', 'http://www.helpdezk.com.br');
		} else {
			$smarty->assign('site', 'HelpDEZK.org');
			$smarty->assign('urlsite', 'http://helpdezk.org');		
		}
		
		///////////////////
		
		$ev = new evaluation_model();		
		$check = $ev->checkToken($token);
		$code_request = $check->fields['code_request'];
		if($code_request){
			//echo $check->fields['code_request'];
			$q = 0;
			
			$eval = "";
			$bd = new operatorview_model();
            $questions = $bd->getQuestions();
            while (!$questions->EOF) {
                $idquestion = $questions->fields['idquestion'];
                $question = $questions->fields['question'];
                $eval.= "<p><strong>" . $question . "</strong></p><ul class='lstEval clearfix mtb10'>";
                $answers = $bd->getAnswers($idquestion);
				$sel = 0;
				$chk = 0;
                while (!$answers->EOF) {
                	
                	if($answers->fields['checked']==1){ $checked = "checked='checked'"; $chk = 1;}
					else {
						if(count($answers->fields) == $sel+1 && $chk == 0){
							$checked = "checked='checked'";
						}else{
							$checked = "";	
						}
					}
                    $idanswer = $answers->fields['idevaluation'];
                    $answer = $answers->fields['name'];
                    $ico = $answers->fields['icon_name'];
                    $eval.= "
                    <li>
                    	<label for='eval$idanswer'><input type='radio' $checked value='$idanswer' name='Answer$q' id='eval$idanswer'/>
                    	<img src=" . path . "/app/uploads/icons/". $ico . " height='18' /> $answer</label>
                    </li>";
					
                    $sel++;
                    $answers->MoveNext();
                }
                $eval.= "</ul>";
                $q++;
                $questions->MoveNext();
            }


			$req = new request_model();
			$info = $req->getRequestInfo($code_request);

			$smarty->assign('evaluationform', $eval);
			$smarty->assign('code_request', $check->fields['code_request']);
			$smarty->assign('subject', $info->fields['subject']);
			$smarty->assign('operator', $info->fields['name']);
			$smarty->assign('token', $token);
			$smarty->assign('numQuest',  $q);
			
						
			
			
		}else{
			$error = $langVars['Evaluation_token_exist'];
			$smarty->assign('error', $error);
		}
		
		$smarty->display('evaluation_token.tpl.html');
	}
	
	
	public function send(){
		session_start();
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
		
		$token = $_POST['token'];
		$database = $this->getConfig('db_connect');
		
		$ev = new evaluation_model();		
		$check = $ev->checkToken($token);
		$code = $check->fields['code_request'];
		if($code){
			$bd = new operatorview_model();
			$bd->BeginTrans();
			
			$req = new request_model();
			$user = $req->getRequestUser($code);
			if(!$user){
				$bd->RollbackTrans();
				return false;
			}
			
	        $person = $user->fields['idperson_owner'];
			$ipadress = $_SERVER['REMOTE_ADDR'];
			
			if(!$person || !$code) return false;
	
			switch ($_POST['approve']) {
				case 'A':
					$status = '5';
			        $reopened = '0';
			        $inslog = $bd->changeRequestStatus($status, $code, $person);
			        if (!$inslog) {
			            $bd->RollbackTrans();
			            return false;
			        }
					
			        $callback = '0';
			        $idtype = '3';
			        $public = '1';
			        $note = '<p><b>' . $langVars['Request_closed'] . '</b></p>';
			        if ($database == 'oci8po') {
	                    $date = "sysdate";
	                }
					elseif($database == 'mysqlt')
	                {
	                    $date = "now()";
	                }
			        $insNote = $bd->insertNote($code, $person, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
			        if (!$insNote) {
			        	$bd->RollbackTrans();
			            return false;
			        }
					
			        $changeStat = $bd->updateReqStatus($status, $code);
			        if (!$changeStat) {
						$bd->RollbackTrans();
			            return false;
			        }				
					
					$clearEval = $bd->clearEvaluation($code);
					if (!$clearEval) {
						$bd->RollbackTrans();
			            return false;
			        }

					if ($database == 'oci8po') {
	                    $date = "sysdate";
	                }
					elseif($database == 'mysqlt')
	                {
	                    $date = "now()";
	                }
					
					for($i = 0; $i <= $_POST['numQuest']-1; $i++){					
						$idAnswer = $_POST['Answer'.$i];
						$ins = $bd->insertEvaluation($idAnswer, $code, $date);
						if (!$ins) {
							$bd->RollbackTrans();
				            return false;
				        }
					}
					
					$rmToken = $ev->removeTokenByToken($token);
					if(!$rmToken){
						$bd->RollbackTrans();
				        return false;
					}
					
					/*if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['FINISH_MAIL'] == '1') {
		                $this->sendEmail('close', $code);
		            }*/
					
					if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['EM_EVALUATED']) {
		                $this->sendEmail('afterevaluate', $code);
		            }
					
					$bd->CommitTrans();
		            echo "OK";
					
					break;
				
				case 'N':				
			        $status = '3';
			        $reopened = '1';
			        $inslog = $bd->changeRequestStatus($status, $code, $person);
			        if (!$inslog) {
			            $bd->RollbackTrans();
				        return false;
			        }
					
			        $callback = '0';
			        $idtype = '3';
			        $public = '1';
			        $note = "<p><b><span style=\"color: #FF0000;\">" . $langVars['Request_not_approve'] . "</span></b></p>";
					$note .= "<p><strong>" . $langVars['Reason'] . ":</strong> " . nl2br($_POST['approveobs']) . "</p>";
			        $date = 'now()';
			        $insNote = $bd->insertNote($code, $person, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
			        if (!$insNote) {
			            $bd->RollbackTrans();
				        return false;
			        }
					
			        $changeStat = $bd->updateReqStatus($status, $code);
			        if (!$changeStat) {
			            $bd->RollbackTrans();
				        return false;
			        }
					
					$rmToken = $ev->removeTokenByToken($token);
					if(!$rmToken){
						$bd->RollbackTrans();
				        return false;
					}
					
			        if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['REQUEST_REOPENED'] == '1') {
		                $this->sendEmail('reopen', $code);
		            }         
					 
					$bd->CommitTrans();
					echo "OK";
					break;
				
				case 'O':
					$status = '5';
			        $reopened = '0';
			        $inslog = $bd->changeRequestStatus($status, $code, $person);
			        if (!$inslog) {
			            $bd->RollbackTrans();
			            return false;
			        }
					
			        $callback = '0';
			        $idtype = '3';
			        $public = '1';
			        $note = '<p><b>' . $langVars['Request_closed'] . '</b></p>';
					$note .= "<p><strong>" . $langVars['Observation'] . ":</strong> " . nl2br($_POST['approveobs']) . "</p>";
			        $date = 'now()';
			        $insNote = $bd->insertNote($code, $person, $note, $date, NULL, NULL, NULL, NULL, NULL, NULL, $public, $idtype, $ipadress, $callback, 'NULL');
			        if (!$insNote) {
			        	$bd->RollbackTrans();
			            return false;
			        }
					
			        $changeStat = $bd->updateReqStatus($status, $code);
			        if (!$changeStat) {
						$bd->RollbackTrans();
			            return false;
			        }				
					
					$clearEval = $bd->clearEvaluation($code);
					if (!$clearEval) {
						$bd->RollbackTrans();
			            return false;
			        }
					
					if ($database == 'oci8po') {
	                    $date = "sysdate";
	                }
					elseif($database == 'mysqlt')
	                {
	                    $date = "now()";
	                }
					for($i = 0; $i <= $_POST['numQuest']-1; $i++){					
						$idAnswer = $_POST['Answer'.$i];
						$ins = $bd->insertEvaluation($idAnswer, $code, $date);
						if (!$ins) {
							$bd->RollbackTrans();
				            return false;
				        }
					}
					
					$rmToken = $ev->removeTokenByToken($token);
					if(!$rmToken){
						$bd->RollbackTrans();
				        return false;
					}
					
					if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['FINISH_MAIL'] == '1') {
		                $this->sendEmail('close', $code);
		            }
					
					if ($_SESSION['SEND_EMAILS'] == '1' && $_SESSION['EM_EVALUATED']) {
		                $this->sendEmail('afterevaluate', $code);
		            }
					
					$bd->CommitTrans();
		            echo "OK";
					
					break;
				
				default:
					
					break;
			}
		}
	}
}