<?php
class projects_model extends Model {
	public function selectProjects($where = NULL, $order = NULL, $limit = NULL) {
		$ret = $this -> db -> Execute("select
                                proj.idproject as idproject,
                                proj.company_project as idcompany_project,
                                proj.group_project as idgroup_project,
                                proj.name_project as name_project,
                                proj.name_reduzido_project as name_reduzido,
                                proj.creator_project as idcreator,
                                proj.person_project as idperson,
                                proj.url_project as url,
                                proj.date_begin_project as begin_date,
                                proj.date_finish_project as end_date,
                                proj.hour_begin_project as begin_hour,
                                proj.hour_finish_project as end_hour,
                                proj.status_project as status,
                                proj.percentual_complete_project as percent,
                                proj.description_project as description,
                                proj.active_project as active,
                                proj.priority_project as priority, 
                                proj.type_project as type,
                                proj.code_request as code_request,
                                comp.name as company,  
                                grp_u.name as group_name, 
                                usu.name as creator
                                from
                                     prj_tbprojects proj, tbperson as comp, hdk_tbgroup grp, tbperson usu, tbperson as grp_u
                                where
                                     proj.company_project = comp.idperson and grp.idgroup = proj.group_project and usu.idperson = proj.creator_project and grp.idperson=grp_u.idperson  $where $order $limit");
		if (!$ret) {
			$sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this -> db -> ErrorMsg();
			$this -> error($sError);
			return false;
		}
		return $ret;
	}

	public function countProjects($where = NULL, $order = NULL, $limit = NULL) {
		$ret = $this -> select("SELECT count(idproject) as total from
                                     prj_tbprojects proj, tbperson as comp, hdk_tbgroup grp, tbperson usu, tbperson as grp_u
                                where
                                     proj.company_project = comp.idperson and grp.idgroup = proj.group_project and usu.idperson = proj.creator_project and grp.idperson=grp_u.idperson $where $order $limit");
		if (!$ret) {
			$sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this -> db -> ErrorMsg();
			$this -> error($sError);
			return false;
		}
		return $ret;
	}

	public function selectPercentual() {
		$ret = $this -> select("SELECT percentual FROM prj_tbpercentual");
		if (!$ret) {
			$sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this -> db -> ErrorMsg();
			$this -> error($sError);
			return false;
		}
		return $ret;
	}

	public function selectStatus() {
		$ret = $this -> select("SELECT * FROM prj_tbstatus_project");
		if (!$ret) {
			$sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this -> db -> ErrorMsg();
			$this -> error($sError);
			return false;
		}
		return $ret;
	}

	public function selectTypeProject() {
		$ret = $this -> select("SELECT * FROM prj_tbtype_project");
		if (!$ret) {
			$sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this -> db -> ErrorMsg();
			$this -> error($sError);
			return false;
		}
		return $ret;
	}

	public function selectPriority() {
		$ret = $this -> select("SELECT idpriority, name FROM hdk_tbpriority");
		if (!$ret) {
			$sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this -> db -> ErrorMsg();
			$this -> error($sError);
			return false;
		}
		return $ret;
	}

	public function selectDependencies() {
		$ret = $this -> select("SELECT idproject, name_project FROM prj_tbprojects");
		if (!$ret) {
			$sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this -> db -> ErrorMsg();
			$this -> error($sError);
			return false;
		}
		return $ret;
	}

	public function setProject($prj_name, $prj_name_min, $my_id, $prj_person, $prj_person_id, $prj_juridical_id, $prj_company, $prj_group, $prj_url, $prj_description, $prj_dtstart, $prj_dtend, $prj_hourstart, $prj_hourend, $prj_type, $prj_perc, $prj_active, $prj_status, $prj_priority) {
		die("INSERT INTO prj_tbprojects (company_project,group_project,name_project,name_reduzido_project,creator_project,person_project,url_project,date_begin_project,date_finish_project,hour_begin_project,hour_finish_project,status_project,percentual_complete_project,description_project,active_project,priority_project,type_project) VALUES ('$prj_company','$prj_group','$prj_name','$prj_name_min','$my_id','$prj_person_id','$prj_url','$prj_dtstart','$prj_dtend','$prj_hourstart','$prj_hourend','$prj_status','$prj_perc','$prj_description','$prj_active','$prj_priority','$prj_type')");
		if (!$ret) {
			$sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this -> db -> ErrorMsg();
			$this -> error($sError);
			return false;
		}
		return $ret;
	}
	
	public function setDependence($id_prj, $id_prj_pai) {
		$ret = $this -> db -> Execute("INSERT INTO prj_tbdep (idprj, idprj_pai) VALUES ('$id_prj','$id_prj_pai')");
		if (!$ret) {
			$sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this -> db -> ErrorMsg();
			$this -> error($sError);
			return false;
		}
		return $ret;
	}
	
	public function update($values, $id) {
		$ret = $this -> db -> Execute("UPDATE prj_tbprojects set $values where idproject='$id'");
		if (!$ret) {
			$sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this -> db -> ErrorMsg();
			$this -> error($sError);
			return false;
		}
		return $ret;
	}

	public function delete($id) {
		$ret = $this -> db -> Execute("DELETE FROM prj_tbprojects WHERE idproject='$id'");
		if (!$ret) {
			$sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this -> db -> ErrorMsg();
			$this -> error($sError);
			return false;
		}
		return $ret;
	}

}
?>
