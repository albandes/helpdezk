<?php

error_reporting(0);

class Services extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}
	
	public function modalArea(){
		$smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
		$db = new services_model();
		$rsareas = $db->selectAreas();
        $listaareas = "<table id='browser' class='tab-services pop' width='100%' border='0' cellpadding='0' cellspacing='0'>";
        $listaareas .= "
        	<colgroup>
        		<col width='5%'/>
        		<col width='90%'/>
        		<col width='5%'/>				
        	</colgroup>";
        while (!$rsareas->EOF) {
            if ($rsareas->fields['status'] == 'A') {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            $listaareas.= "
            <tr>
            	<td class='pr0'><input id='" . $rsareas->fields['idarea'] . "' name='" . $rsareas->fields['idarea'] . "'  type='checkbox' value='" . $rsareas->fields['idarea'] . "' $checked /></td>
            	<td>" . $rsareas->fields['name'] . "</td>
            	<td><a href='" . $rsareas->fields['idarea'] . "' class='btnEdit-tp1' alt='".$langVars['edit']."'>".$langVars['edit']."</a></td>            	
            </tr>";
            
            $rsareas->MoveNext();
        }
        $listaareas.= "</table>";
        $smarty->assign('areas', $listaareas);
		
		$smarty->display('modais/services/areainsert.tpl.html');
	}
	
	public function modalType(){
		$smarty = $this->retornaSmarty();
        $db = new services_model();
		$select = $db->selectAvailabeAreas();
        while (!$select->EOF) {
            $campos[] = $select->fields['idarea'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('areaids', $campos);
        $smarty->assign('areavals', $valores);
		
		$smarty->display('modais/services/typeinsert.tpl.html');
	}
	
	public function modalItem(){
		$smarty = $this->retornaSmarty();
       	$id = $this->getParam('id');
		$smarty->assign('id', $id);
		$smarty->display('modais/services/iteminsert.tpl.html');
	}
	
	public function modalService(){
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
       	$db2 = new groups_model();
		$db = new services_model();
       	$rsgroups = $db2->selectGroup(" AND tbg.status = 'A'", " ORDER BY tbp.name ASC");
        while (!$rsgroups->EOF) {
            $groupscp[] = $rsgroups->fields['idgroup'];
            $groupsval[] = $rsgroups->fields['name'];
            $rsgroups->MoveNext();
        }
		
		$rspriority = $db->selectPriority();
        while (!$rspriority->EOF) {
            $prioritycp[] = $rspriority->fields['idpriority'];
            $priorityval[] = $rspriority->fields['name'];
            $rspriority->MoveNext();
        }
		$smarty->assign('id', $id);
        $smarty->assign('prioritycp', $prioritycp);
        $smarty->assign('priorityval', $priorityval);
        $smarty->assign('groupscp', $groupscp);
        $smarty->assign('groupsvals', $groupsval);
		$smarty->display('modais/services/serviceinsert.tpl.html');
	}
	
	public function modalConfApproval(){
		$smarty = $this->retornaSmarty();
        
		$db_areas = new services_model();
        $rs = $db_areas->selectAreas();
		
		while (!$rs->EOF) {
            $down = $db_areas->getTypeFromAreas($rs->fields['idarea']);
			if ($down->fields) {
				$option .= "<option disabled='disabled'>".$rs->fields['name']."</option>";
				while (!$down->EOF) {
					$option .= "<option value='".$down->fields['type']."'>".$down->fields['type_name']."</option>";
					$down->MoveNext();
				}
			}
			$rs->MoveNext();
		}		
		$smarty->assign('typeoptions', $option);
		$smarty->display('modais/services/confapproval.tpl.html');
	}
	
	public function getUsersApprove()
	{
		$iditem = $this->getParam('iditem');
		$idservice = $this->getParam('idservice');				
		$db = new requestrules_model();		
		$user = $db->getUsers($iditem, $idservice);
		$uapp = $db->getUsersApprove($iditem, $idservice);
		$i = 0;
		while (!$user->EOF) {
			$resul['resul']['user'][$i]['id'] = $user->fields['idperson'];
			$resul['resul']['user'][$i]['name'] = $user->fields['name'];
			$i++;
			$user->MoveNext();
		}
		$i = 0;
		while (!$uapp->EOF) {
			$resul['resul']['uapp'][$i]['id'] = $uapp->fields['idperson'];
			$resul['resul']['uapp'][$i]['name'] = $uapp->fields['name'];
			$resul['resul']['uapp'][$i]['recal'] = $uapp->fields['fl_recalculate'];
			$i++;
			$uapp->MoveNext();
		}
		echo json_encode($resul);		
	}
	
	public function modalConfApprovalSave(){
		$cmbType = $_POST['cmbType']; 
    	$cmbItem = $_POST['cmbItem'];
    	$cmbService = $_POST['cmbService'];
    	$cmbAval = $_POST['cmbAval'];
    	$txtRecalc = $_POST['txtRecalc'];
		$i = 1;
		$j = 1;
		$bd = new requestrules_model();
		$bd->BeginTrans();
		if($bd->deleteUsersApprove($cmbItem, $cmbService)){
			if($cmbAval == 0){
				$bd->CommitTrans();
				echo "OK";
			}else{
				$cmbAval = explode(",",$_POST['cmbAval']);
				foreach($cmbAval as $idperson){			
					$ret = $bd->insertUsersApprove($cmbItem, $cmbService, $idperson, $i, $txtRecalc);
					if($ret){
						$j++;
					}else{
						$bd->RollbackTrans();
						return false;
					}
					$i++;
				}
				
				if($i == $j){
					$bd->CommitTrans();
					echo "OK";
				}
				else{
					$bd->RollbackTrans();
					return false;
				} 
			}			
		}else{
			$bd->RollbackTrans();
			return false;
		}
				
	}
	
    public function index() {
		$user = $_SESSION['SES_COD_USUARIO'];
		$bd = new home_model();
		$typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("services/");
		$access = $this->access($user, $program, $typeperson);
		
		
        $smarty = $this->retornaSmarty();
        $db = new services_model();
        $rs = $db->selectAreas();
        $langVars = $smarty->get_config_vars();
		
        $lista = "	<table id='lista' class='tab-services' cellspacing='0' cellpadding='0'>
	        		<colgroup>
	        			<col width='145'/>
	        			<col width='55'/>
	        		</colgroup>";
        
        while (!$rs->EOF) {
            $down = $db->getTypeFromAreas($rs->fields['idarea']);
             if ($rs->fields['status'] == 'A') {
                $checkedarea = 'checked="checked"';
            } else {
                $checkedarea = '';
            }
            if ($down->fields) {
                $lista.="<th colspan='2'>
            				<input type='checkbox'" . $checkedarea . " class='checkArea' id='area-" . $rs->fields['idarea'] . "' name='area-" . $rs->fields['idarea'] . "' value='" . $rs->fields['idarea'] . "' />
            				<label for='area-" . $rs->fields['idarea'] . "'> ". $rs->fields['name'] ."</label>
            			</th>";
                while (!$down->EOF) {
                    if ($down->fields['type_status'] == 'A') {
                        $checkedtype = 'checked="checked"';
                    } else {
                        $checkedtype = '';
                    }
                    $idtype = $down->fields['type'];
                    $lista.= "<tr>
                    			<td>
                    				<input type='checkbox' id='type-".$idtype."' class='checkType' name='type-".$idtype."'  ".$checkedtype." value='".$idtype."' />
                    				<label for='type-".$idtype."'>" . $down->fields['type_name'] . "</label>
                    			</td>
                    			<td>
                    				<a href='$idtype' class='btnEdit-tp1 mr5' alt='".$langVars['edit']."'>".$langVars['edit']."</a>
                    				<a href='$idtype' class='btnSearch-tp1' alt='".$langVars['show']."'>".$langVars['show']."</a>
                    			</td>
                    		</tr>";
                    $down->MoveNext();
                }

                $lista.="</tr>";
            }
            $rs->MoveNext();
        }

        $lista.="</table>";
        $smarty->assign('lista', $lista);

        
        $smarty->display('services.tpl.html');
    }

	public function getInitList(){
		$smarty = $this->retornaSmarty();
		$db = new services_model();
        $rs = $db->selectAreas();
        $langVars = $smarty->get_config_vars();
        $lista = "	<table id='lista' class='tab-services' cellspacing='0' cellpadding='0'>
	        		<colgroup>
	        			<col width='145'/>
	        			<col width='55'/>
	        		</colgroup>";
        
        while (!$rs->EOF) {
            $down = $db->getTypeFromAreas($rs->fields['idarea']);
             if ($rs->fields['status'] == 'A') {
                $checkedarea = 'checked="checked"';
            } else {
                $checkedarea = '';
            }
            if ($down->fields) {
                $lista.="<th colspan='2'>
            				<input type='checkbox'" . $checkedarea . " class='checkArea' id='area-" . $rs->fields['idarea'] . "' name='area-" . $rs->fields['idarea'] . "' value='" . $rs->fields['idarea'] . "' />
            				<label for='area-" . $rs->fields['idarea'] . "'> ". $rs->fields['name'] ."</label>
            			</th>";
                
                while (!$down->EOF) {
                    if ($down->fields['type_status'] == 'A') {
                        $checkedtype = 'checked="checked"';
                    } else {
                        $checkedtype = '';
                    }
                    $idtype = $down->fields['type'];
                    $lista.= "<tr>
                    			<td>
                    				<input type='checkbox' id='type-".$idtype."' class='checkType' name='type-".$idtype."'  ".$checkedtype." value='".$idtype."' />
                    				<label for='type-".$idtype."'>" . $down->fields['type_name'] . "</label>
                    			</td>
                    			<td>
                    				<a href='$idtype' class='btnEdit-tp1 mr5' alt='".$langVars['edit']."'>".$langVars['edit']."</a>
                    				<a href='$idtype' class='btnSearch-tp1' alt='".$langVars['show']."'>".$langVars['show']."</a>
                    			</td>
                    		</tr>";
                    $down->MoveNext();
                }

                $lista.="</tr>";
            }
            $rs->MoveNext();
        }

        $lista.="</table>";
		
		echo $lista;
	}

    public function items() {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $id = $_POST['id'];
        $pos = strrpos($id, "/");
        $pos++;
        $id = substr($id, $pos);
        $db = new services_model();
        $name = $db->selectTypeName($id);
        $rs = $db->selectItens($id);

        if ($rs) {
            $x = 1;
            $y = 1;
            while (!$rs->EOF) {
                //agrupa as categorias tirando as duplicadas
                if (in_array($rs->fields['item'], $item['iditem'])) {
                    
                } else {
                    $item[$y++] = array('iditem' => $rs->fields['item'], 'type_pai' => $rs->fields['type_pai'], 'item' => $rs->fields['item_name'], 'item_status' => $rs->fields['item_status']);
                }
                $rs->MoveNext();
            }
            $lista1.= "<table id='lista1' class='tab-services' cellspacing='0' cellpadding='0'>
            			<colgroup>
		        			<col width='145'/>
		        			<col width='55'/>
		        		</colgroup>";
            $lista1.="<tr><th colspan='2'>" . $name . "</th></tr>";
            $lista1.= "	<tr>
        					<td colspan='2'>
        						<a href='#' id='addItem' class='btnAdd-tp1' title='" . $langVars['Add_item'] . "'>        							
        							" . $langVars['Add_item'] . "
        						</a>
        					</td>
        				</tr>";
            
            for ($j = 0; $j < sizeof($item); $j++) {
                if ($item[$j + 1]['item_status'] == 'A') {
                    $checkeditem = 'checked="checked"';
                } else {
                    $checkeditem = '';
                }
                $iditem = $item[$j + 1]['iditem'];
                $lista1.= "<tr>
                				<td>
                					<input type='checkbox' id='item-$iditem' value='$iditem' name='item-$iditem' class='checkStatus' " . $checkeditem . ">
                					<label for='item-$iditem'>
                						" . $item[$j + 1]['item'] . "
                					</label>
                				</td>
                				<td>
                    				<a href='$iditem' class='btnEdit-tp1 mr5' alt='".$langVars['edit']."'>".$langVars['edit']."</a>
                    				<a href='$iditem' class='btnSearch-tp1' alt='".$langVars['show']."'>".$langVars['show']."</a>
                    			</td>
                			</tr>";	
            }
            
            $lista1.="</table><input type='hidden' id='idtype2' name='idtype2' value='" . $id . "'/>";

            echo $lista1;
        } else {
            echo "--------";
        }
    }

    public function servicebox() {

        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $id = $_POST['id2'];
        $pos = strrpos($id, "/");
        $pos++;
        $id = substr($id, $pos);
        $db = new services_model();
        $name = $db->selectItemName($id);
        $rs = $db->selectServices($id);

        if ($rs) {
            $x = 1;
            $y = 1;
            while (!$rs->EOF) {
                //agrupa as categorias tirando as duplicadas
                if (in_array($rs->fields['service'], $service['idservice'])) {
                    
                } else {
                    $service[$y++] = array('idservice' => $rs->fields['service'], 'service_pai' => $rs->fields['service_pai'], 'service' => $rs->fields['service_name'], 'service_status' => $rs->fields['service_status']);
                }
                $rs->MoveNext();
            }
            $lista2.= "<table id='lista2'  class='tab-services' cellspacing='0' cellpadding='0'>
            			<colgroup>
		        			<col width='175'/>
		        			<col width='25'/>
		        		</colgroup>";
            $lista2.= "<tr><th colspan='2'>" . $name . "</th></tr>";
			
            $lista2.= "<tr>
            				<td colspan='2'>
            					<a href='#' id='addService' class='btnAdd-tp1' title='" . $langVars['Add_service'] . "'>
									" . $langVars['Add_service'] . " 
            					</a>
            				</td>
            			</tr>";
            
            for ($j = 0; $j < sizeof($service); $j++) {
                if ($service[$j + 1]['service_status'] == 'A') {
                    $checkedservice = 'checked="checked"';
                } else {
                    $checkedservice = '';
                }
                $idservice = $service[$j + 1]['idservice'];
                $lista2.= "<tr>
                				<td>
                					<input type='checkbox' id='service-$idservice' name='service-$idservice' class='serviceCheck' value='$idservice' " . $checkedservice . ">
                					<label for='service-$idservice'>" 
                						. $service[$j + 1]['service'] . 
                					"</label>
                				</td>
                				<td>
                    				<a href='$idservice' class='btnEdit-tp1' alt='".$langVars['edit']."'>".$langVars['edit']."</a>
                    			</td>
                			</tr>";
            }
            $lista2.="</tr>";
            $lista2.="</table><input type='hidden' id='iditem2' name='iditem2' value='" . $id . "'>";

            echo $lista2;
        } else {
            echo "---";
        }
    }

    public function iteminsert() {
        $name = $_POST['itemName'];
        $default = $_POST['defaultItem'];
        $status = $_POST['availableItem'];
        $classify = $_POST['classificationItem'];
        $id = $_POST['id'];        
		if(!$default) $default = 0;
		if(!$status) $status = "N";
		if(!$classify) $classify = 0;		
        $bd = new services_model();
        $bd->BeginTrans();        
		if($default == 1){
			$clear = $bd->clearDefaultItem($id);
			if(!$clear){
				$bd->RollbackTrans();
				return false;
			}
		}		
        $ret = $bd->insertItem($name, $default, $status, $classify, $id);
        if ($ret) {
            $max = $bd->selectMaxItem();
            $max = $max->fields['last'];
            $bd->CommitTrans();
            echo $max;
        } else {
        	$bd->RollbackTrans();
            return false;
        }
    }

    public function serviceInsert() {			
		$name = $_POST['serviceName'];		
        $vardefault = $_POST['defaultService'];		
        $availableitem = $_POST['availableService'];
        $classifyitem = $_POST['classificationService'];
		$id = $_POST['id'];		
        $priority = $_POST['servicePriority'];
		$days = $_POST['limit_days'];
		$limit_time = $_POST['limit_time'];
		$time = $_POST['time'];
		$group = $_POST['serviceGroup'];		
		
		if(!$vardefault) $vardefault = 0;
		if(!$availableitem) $availableitem = "N";
		if(!$classifyitem) $classifyitem = 0;

        $bd = new services_model();
		$bd->BeginTrans();
		if($vardefault == 1){
			$clear = $bd->clearDefaultService($id);
			if(!$clear){
				$bd->RollbackTrans();
				return false;
			}
		}
		
        $ret = $bd->serviceInsert($name, $vardefault, $availableitem, $classifyitem, $id, $priority, $time, $days, $limit_time);
        if (!$ret) {
            $bd->RollbackTrans();
            return false;
        }

        $max = $bd->selectMax();
        if (!$max) {
            $bd->RollbackTrans();
            return false;
        }

        $max = $max->fields['last'];
        $grpInsert = $bd->serviceGroupInsert($max, $group);
        if(!$grpInsert){
            $bd->RollbackTrans();
            return false;
        }
    	$bd->CommitTrans();
        echo $max;
    }

    public function areaInsert() {
        $name = $_POST['areaName'];
		$default = $_POST['defaultArea'];		
		$bd = new services_model();
		$bd->BeginTrans();
		if($default == 1){
			$clear = $bd->clearDefaultArea();
			if(!$clear){
				$bd->RollbackTrans();
				return false;
			}
		}
        $ret = $bd->areaInsert($name,$default);
        if ($ret) {
        	$bd->CommitTrans();
            echo "ok";
        } else {
        	$bd->RollbackTrans();
            return false;
        }
    }

    public function typeInsert() {
        $name = $_POST['typeName'];
        $vardefault = $_POST['defaultType'];
        $status = $_POST['availableType'];
        $classify = $_POST['classificationType'];
        $area = $_POST['areaType'];		
		if(!$vardefault) $vardefault = 0;
		if(!$status) $status = "N";
		if(!$classify) $classify = 0;
        $bd = new services_model();
		$bd->BeginTrans();		
		if($vardefault == 1){
			$clear = $bd->clearDefaultType($area);
			if(!$clear){
				$bd->RollbackTrans();
				return false;
			}
		}		
        $ret = $bd->typeInsert($name, $vardefault, $status, $classify, $area);
        if ($ret) {
            $max = $bd->selectMaxType();
            $max = $max->fields['last'];
			$bd->CommitTrans();
            echo $max;
        } else {
        	$bd->RollbackTrans();
            return false;
        }
    }

    public function typeEdit() {
        $smarty = $this->retornaSmarty();
        $bd = new services_model();
        $select = $bd->selectAreas();
        while (!$select->EOF) {
            $campos[] = $select->fields['idarea'];
            $valores[] = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('areaids', $campos);
        $smarty->assign('areavals', $valores);
        $id = $this->getParam('id');
        $ret = $bd->selectTypeEdit($id);
        $name = $ret->fields['name'];
        $status = $ret->fields['status'];
        $selec = $ret->fields['selected'];
        $classify = $ret->fields['classify'];
        $area = $ret->fields['idarea'];
        $smarty->assign('id', $id);
        $smarty->assign('name', $name);
        $smarty->assign('area', $area);
        $smarty->assign('available', $status);
        $smarty->assign('classify', $classify);
        $smarty->assign('default', $selec);
        $smarty->display('modais/services/typeedit.tpl.html');
    }

    public function itemEdit() {
        $smarty = $this->retornaSmarty();
        $bd = new services_model();
        $id = $this->getParam('id');
        $ret = $bd->selectItemEdit($id);
        $name = $ret->fields['name'];
        $status = $ret->fields['status'];
        $selec = $ret->fields['selected'];
        $classify = $ret->fields['classify'];
        $smarty->assign('name', $name);
        $smarty->assign('id', $id);
        $smarty->assign('available', $status);
        $smarty->assign('classify', $classify);
        $smarty->assign('default', $selec);
        $smarty->display('modais/services/itemedit.tpl.html');
    }

    public function serviceEdit() {
        $smarty = $this->retornaSmarty();
        $db = new services_model();
        $dbgrp = new groups_model();
        $rsgroups = $dbgrp->selectGroup(null, "ORDER BY tbp.name ASC");
        while (!$rsgroups->EOF) {
            $groupscp[] = $rsgroups->fields['idgroup'];
            $groupsval[] = $rsgroups->fields['name'];
            $rsgroups->MoveNext();
        }
        $smarty->assign('groupscp', $groupscp);
        $smarty->assign('groupsvals', $groupsval);
        $rspriority = $db->selectPriority();
        while (!$rspriority->EOF) {
            $prioritycp[] = $rspriority->fields['idpriority'];
            $priorityval[] = $rspriority->fields['name'];
            $rspriority->MoveNext();
        }
        $smarty->assign('prioritycp', $prioritycp);
        $smarty->assign('priorityval', $priorityval);
        $id = $this->getParam('id');
        $ret = $db->selectServiceEdit($id);
        $grp = $db->selectServiceGroup($id);
        $group = $grp->fields['idgroup'];
        $pri = $db->selectServicePriority($id);
        $pri = $pri->fields['idpriority'];
        $name = $ret->fields['name'];
        $status = $ret->fields['status'];
        $selec = $ret->fields['selected'];
        $classify = $ret->fields['classify'];
        $hours = $ret->fields['hours_attendance'];
        $days = $ret->fields['days_attendance'];
		$ihm = $ret->fields['ind_hours_minutes'];
        $smarty->assign('id', $id);
        $smarty->assign('name', $name);
		$smarty->assign('ihm', $ihm);
        $smarty->assign('available', $status);
        $smarty->assign('classify', $classify);
		$smarty->assign('status', $status);
        $smarty->assign('default', $selec);
        $smarty->assign('group', $group);
        $smarty->assign('priority', $pri);
        $smarty->assign('days', $days);
        $smarty->assign('time', $hours);
        $smarty->display('modais/services/serviceedit.tpl.html');
    }

    public function edittype() {
        $name = $_POST['typeName'];
        $vardefault = $_POST['defaultType'];
        $status = $_POST['availableType'];
        $classify = $_POST['classificationType'];
        $area = $_POST['areaType'];
		$id = $_POST['id'];		
		if(!$vardefault) $vardefault = 0;
		if(!$status) $status = "N";
		if(!$classify) $classify = 0;
        $db = new services_model();
		$db->BeginTrans();
		if($vardefault == 1){
			$clear = $db->clearDefaultType($area);
			if(!$clear){
				$db->RollbackTrans();
				return false;
			}
		}	
        $updt = $db->updateType($id, $name, $area, $vardefault, $status, $classify);
        if ($updt) {
        	$db->CommitTrans();
            echo $id;
        } else {
        	$db->RollbackTrans();
            return false;
        }
    }

    public function edititem() {
		$name = $_POST['itemName'];
        $vardefault = $_POST['defaultItem'];
        $status = $_POST['availableItem'];
        $classify = $_POST['classificationItem'];
        $id = $_POST['id'];
        
		if(!$vardefault) $vardefault = 0;
		if(!$status) $status = "N";
		if(!$classify) $classify = 0;		

        $db = new services_model();
		$db->BeginTrans();   
		     
		if($vardefault == 1){
			$getIdType = $db->getIdTypeByItem($id);
			if(!$getIdType){
				$db->RollbackTrans();
				return false;
			}else{
				$clear = $db->clearDefaultItem($getIdType);
				if(!$clear){
					$db->RollbackTrans();
					return false;
				}
			}
		}		
        $idtype = $db->selectType($id);
        $idtype = $idtype->fields['idtype'];
        $updt = $db->updateItem($id, $name, $vardefault, $status, $classify);
        if ($updt) {
        	$db->CommitTrans();
            echo $idtype;
        } else {
        	$db->RollbackTrans();
            return false;
        }
    }

    public function editservice() {
        $name = $_POST['serviceName'];		
        $vardefault = $_POST['defaultService'];		
        $availableitem = $_POST['availableService'];
        $classifyitem = $_POST['classificationService'];
		$id = $_POST['id'];		
        $priority = $_POST['servicePriority'];
		$days = $_POST['limit_days'];
		$limit_time = $_POST['limit_time'];
		$time = $_POST['time'];
		$group = $_POST['serviceGroup'];
		
		if(!$vardefault) $vardefault = 0;
		if(!$availableitem) $availableitem = "N";
		if(!$classifyitem) $classifyitem = 0;
		
		$db = new services_model();
		$db->BeginTrans();
		
		$iditem = $db->selectItem($id);
		if($iditem){
			$iditem = $iditem->fields['iditem'];	
		}else{
			$db->RollbackTrans();
			return false;
		}
		if($vardefault == 1){			
			$clear = $db->clearDefaultService($iditem);
			if(!$clear){
				$db->RollbackTrans();
				return false;
			}			
		}
        $grp = $db->selectPrevGroup($id);
        $grp = $grp->fields['idgroup'];
        if ($grp == $group) {
            $updt = $db->updateService($id, $name, $vardefault, $availableitem, $classifyitem, $priority, $time, $days, $limit_time);
            if ($updt) {
            	$db->CommitTrans();
                echo $iditem;
            } else {
            	$db->RollbackTrans();
                return false;
            }
        } else {
            $updt = $db->updateService($id, $name, $vardefault, $availableitem, $classifyitem, $priority, $time, $days, $limit_time);
            $uptgrp = $db->updateServiceGroup($id, $group);
            if ($updt && $uptgrp) {
            	$db->CommitTrans();
                echo $iditem;
            } else {
            	$db->RollbackTrans();
                return false;
            }
        }
    }

    public function areaChangeStatus() {
        extract($_POST);

        $db = new services_model();
        $updt = $db->areaChangeStatus($id, $check);
        if ($updt) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function typeChangeStatus() {
        extract($_POST);

        $db = new services_model();
        $updt = $db->typeChangeStatus($id, $check);
        if ($updt) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function itemChangeStatus() {
        extract($_POST);

        $db = new services_model();
        $updt = $db->itemChangeStatus($id, $check);
        if ($updt) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function serviceChangeStatus() {
        extract($_POST);

        $db = new services_model();
        $updt = $db->serviceChangeStatus($id, $check);
        if ($updt) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function areaEdit() {
        $smarty = $this->retornaSmarty();
        $bd = new services_model();
        $id = $this->getParam('id');
        $ret = $bd->selectAreaEdit($id);
        $name = $ret->fields['name'];
		$default = $ret->fields['def'];
        $smarty->assign('name', $name);
		$smarty->assign('default', $default);
        $smarty->assign('id', $id);
        $smarty->display('modais/services/areaedit.tpl.html');
    }

    public function editarea() {
        $id = $_POST['id'];
		$name = $_POST['areaName'];
		$default = $_POST['defaultAreaEdit'];
        $db = new services_model();
		$db->BeginTrans();		
		if($default == 1){
			$clear = $db->clearDefaultArea();
			if(!$clear){
				$db->RollbackTrans();
				return false;
			}
		}		
        $updt = $db->updateArea($id, $name, $default);
        if ($updt) {
        	$db->CommitTrans();
            echo $id;
        } else {
        	$db->RollbackTrans();
            return false;
        }		
    }

}

?>
