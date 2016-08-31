<?php

class home_model extends Model {

    public function selectUserLogin($cod_usu) {
        $ret = $this->select("select login from tbperson where idperson = $cod_usu");
		$nom = $ret->fields['login'];
        return $nom;
    }

    public function selectMenu($idperson, $type) {
        return $this->select("	select
								  m.idmodule           as idmodule_pai,
								  m.name               as module,
								  cat.idmodule          as idmodule_origem,
								  cat.name              as category,
								  cat.idprogramcategory as category_pai,
								  cat.smarty  as cat_smarty,
								  pr.idprogramcategory  as idcategory_origem,
								  pr.name               as program,
								  pr.controller         as controller,
								  pr.idprogram          as idprogram,
								  pr.smarty as pr_smarty,
								  a.idaccesstype    as permission
								from tbperson  p,
								  tbtypepersonpermission  g,
								  tbaccesstype  a,
								  tbprogram  pr,
								  tbmodule  m,
								  tbprogramcategory  cat,
								  tbtypeperson  tp
								where g.idaccesstype = a.idaccesstype
									and g.idprogram = pr.idprogram
									and m.idmodule = cat.idmodule
									and cat.idprogramcategory = pr.idprogramcategory
									and tp.idtypeperson = g.idtypeperson
									AND m.status = 'A'
									AND pr.status = 'A'
									AND g.allow = 'Y'
									AND p.idperson = '$idperson'
									AND g.idaccesstype = '1'
									AND g.idtypeperson = '$type'");
    }

    public function selectPersonPermission($idperson, $idprogram) {
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			//$concat = "concat(pr.name, '–' ,a.idaccesstype) as programname";
			$concat = "concat(pr.name, '–' ,p.idaccesstype) as programname";
		} elseif ($database == 'oci8po') {
			$concat = "pr.name || '–' || p.idaccesstype as programname";
		}
	
		$query = "
				select
				   p.idpermission,
				   p.idaccesstype,
				   p.idprogram,
				   p.idperson,
				   p.allow,
				   m.idmodule     as module,
				   ".$concat."
				from tbpermission  p,
				   tbprogram  pr,
				   tbprogramcategory  cat,
				   tbmodule  m
				where m.idmodule = cat.idmodule
					 and pr.idprogramcategory = cat.idprogramcategory
					 and p.idprogram = pr.idprogram
					 and m.status = 'A'
					 and pr.status = 'A'
					 AND idperson = '$idperson'
					 AND p.idprogram = '$idprogram'
				";	
        $individualperm = $this->select($query);        
       // if ($individualperm->fields['idpermission']) {
            return $individualperm;
       // }
    }

    public function selectGroupPermission($idperson, $idprogram, $type) {
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$concat = "concat(pr.name, '–' ,a.idaccesstype) as programname";
		} elseif ($database == 'oci8po') {
			$concat = "pr.name || '–' || a.idaccesstype as programname";
		}
		
		$query = "
					select
					  g.idpermissiongroup,
					  pr.idprogram        as idprogram,
					  pr.smarty  as pr_smarty,
					  a.idaccesstype,
					  m.idmodule          as idmodule,
					  tp.idtypeperson     as idtypeperson,
					  ".$concat.",
					  g.allow              
					from tbperson  p,
					  tbtypepersonpermission  g,
					  tbaccesstype  a,
					  tbprogram  pr,
					  tbmodule  m,
					  tbprogramcategory  cat,
					  tbtypeperson  tp
					WHERE g.idaccesstype = a.idaccesstype
						and g.idprogram = pr.idprogram
						and m.idmodule = cat.idmodule
						and cat.idprogramcategory = pr.idprogramcategory
						and tp.idtypeperson = g.idtypeperson
						AND m.status = 'A'
						AND pr.status = 'A'
						AND p.idperson = '$idperson'
						AND tp.idtypeperson = '$type'            
						AND pr.idprogram = '$idprogram'
				";	
        $groupperm = $this->select($query);
        //if ($groupperm->fields['idpermissiongroup']) {
            return $groupperm;
       // }
    }

    public function selectProgramID($name) {
        $sel = $this->select("select idprogram from tbprogram where name = '$name'");
        return $sel->fields['idprogram'];
    }
    
    public function selectProgramIDByController($controller) {
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$sel = $this->select("select idprogram from tbprogram where controller = '$controller' || controller = '".substr($controller,0,-1)."'");
		} elseif ($database == 'oci8po') {
			$sel = $this->select("select idprogram from tbprogram where controller = '$controller' or controller = '".substr($controller,0,-1)."'");
		}
		return $sel->fields['idprogram'];
    }
    

    public function selectTypePerson($id) {
        $ret = $this->select("select idtypeperson from tbperson where idperson='$id'");
		return $ret->fields['idtypeperson'];
    }

    public function selectPersonPermissionMenu($idperson) {
        $individualperm = $this->select("
										select
										  m.idmodule           as idmodule_pai,
										  m.name               as module,
										  cat.idmodule          as idmodule_origem,
										  cat.name              as category,
										  cat.idprogramcategory as category_pai,
										  cat.smarty as cat_smarty,
										  pr.idprogramcategory  as idcategory_origem,
										  pr.name               as program,
										  pr.controller         as controller,
										  pr.smarty  as pr_smarty,
										  pr.idprogram          as idprogram,
										  p.allow
										from tbperson  per,
										  tbpermission  p,
										  tbprogram  pr,
										  tbmodule  m,
										  tbprogramcategory  cat,
										  tbaccesstype  acc
										where m.idmodule = cat.idmodule
											and pr.idprogramcategory = cat.idprogramcategory
											and per.idperson = p.idperson
											AND pr.idprogram = p.idprogram
											and m.status = 'A'
											and pr.status = 'A'
											AND p.idperson = '$idperson'
											AND p.idaccesstype = acc.idaccesstype
											AND p.idaccesstype = '1'
											and m.idmodule =< 3



											");
		
		if ($individualperm->fields['idmodule_pai'])    return $individualperm;
			
    }

    public function getPermissionMenu($idperson, $typeperson, $andModule) {
        $groupperm = $this->select("
									(
									select
									  m.idmodule           as idmodule_pai,
									  m.name               as module,
									  cat.idmodule          as idmodule_origem,
									  cat.name              as category,
									  cat.idprogramcategory as category_pai,
									  cat.smarty as cat_smarty,
									  pr.idprogramcategory  as idcategory_origem,
									  pr.name               as program,
									  pr.controller         as controller,
									  pr.smarty  as pr_smarty,
									  pr.idprogram          as idprogram,
									  g.allow
									from tbperson  p,
									  tbtypepersonpermission  g,
									  tbaccesstype  a,
									  tbprogram  pr,
									  tbmodule  m,
									  tbprogramcategory  cat,
									  tbtypeperson  tp
									WHERE g.idaccesstype = a.idaccesstype
										and g.idprogram = pr.idprogram
										and m.idmodule = cat.idmodule
										and cat.idprogramcategory = pr.idprogramcategory
										and tp.idtypeperson = g.idtypeperson
										AND m.status = 'A'
										AND pr.status = 'A'
										AND p.idperson = '$idperson'
										AND tp.idtypeperson = '$typeperson'
										AND g.idaccesstype = '1'
										AND g.allow = 'Y'
										AND $andModule
									)
									UNION
									(
										select
										  m.idmodule           as idmodule_pai,
										  m.name               as module,
										  cat.idmodule          as idmodule_origem,
										  cat.name              as category,
										  cat.idprogramcategory as category_pai,
										  cat.smarty as cat_smarty,
										  pr.idprogramcategory  as idcategory_origem,
										  pr.name               as program,
										  pr.controller         as controller,
										  pr.smarty  as pr_smarty,
										  pr.idprogram          as idprogram,
										  p.allow
										from tbperson  per,
										  tbpermission  p,
										  tbprogram  pr,
										  tbmodule  m,
										  tbprogramcategory  cat,
										  tbaccesstype  acc
										where m.idmodule = cat.idmodule
											and pr.idprogramcategory = cat.idprogramcategory
											and per.idperson = p.idperson
											AND pr.idprogram = p.idprogram
											and m.status = 'A'
											and pr.status = 'A'
											AND p.idperson = '$idperson'
											AND p.idaccesstype = acc.idaccesstype
											AND p.idaccesstype = '1'
											AND $andModule
									)
									");

        if ($groupperm->fields['idmodule_pai'])       return $groupperm;

    }

    public function selectGroupPermissionMenu($idperson, $type) {
        $groupperm = $this->select("
									(
									select
									  m.idmodule           as idmodule_pai,
									  m.name               as module,
									  cat.idmodule          as idmodule_origem,
									  cat.name              as category,
									  cat.idprogramcategory as category_pai,
									  cat.smarty as cat_smarty,
									  pr.idprogramcategory  as idcategory_origem,
									  pr.name               as program,
									  pr.controller         as controller,
									  pr.smarty  as pr_smarty,                
									  pr.idprogram          as idprogram,
									  g.allow
									from tbperson  p,
									  tbtypepersonpermission  g,
									  tbaccesstype  a,
									  tbprogram  pr,
									  tbmodule  m,
									  tbprogramcategory  cat,
									  tbtypeperson  tp
									WHERE g.idaccesstype = a.idaccesstype
										and g.idprogram = pr.idprogram
										and m.idmodule = cat.idmodule
										and cat.idprogramcategory = pr.idprogramcategory
										and tp.idtypeperson = g.idtypeperson
										AND m.status = 'A'
										AND pr.status = 'A'
										AND p.idperson = '$idperson'
										AND tp.idtypeperson = '$type'
										AND g.idaccesstype = '1'
										AND g.allow = 'Y'
										and m.idmodule <= 3
									)
									UNION
									(
										select
										  m.idmodule           as idmodule_pai,
										  m.name               as module,
										  cat.idmodule          as idmodule_origem,
										  cat.name              as category,
										  cat.idprogramcategory as category_pai,
										  cat.smarty as cat_smarty,
										  pr.idprogramcategory  as idcategory_origem,
										  pr.name               as program,
										  pr.controller         as controller,
										  pr.smarty  as pr_smarty,
										  pr.idprogram          as idprogram,
										  p.allow
										from tbperson  per,
										  tbpermission  p,
										  tbprogram  pr,
										  tbmodule  m,
										  tbprogramcategory  cat,
										  tbaccesstype  acc
										where m.idmodule = cat.idmodule
											and pr.idprogramcategory = cat.idprogramcategory
											and per.idperson = p.idperson
											AND pr.idprogram = p.idprogram
											and m.status = 'A'
											and pr.status = 'A'
											AND p.idperson = '$idperson'
											AND p.idaccesstype = acc.idaccesstype
											AND p.idaccesstype = '1'
											and m.idmodule <= 3
									)
									");
		
		if ($groupperm->fields['idmodule_pai'])       return $groupperm;
        
    }

    public function countPrograms() {
        $count = $this->select("select max(idprogram) as total from tbprogram");
		return $count->fields['total'];
    }

    public function countCategories() {
        $count = $this->select("select max(idprogramcategory) as total from tbprogramcategory");
        return $count->fields['total'];
    }

    public function countModules() {
        $count = $this->select("select max(idmodule) as total from tbmodule where status = 'A'");
		return $count->fields['total'];
    }

    public function getIdTypePerson($id) {
        $ret = $this->select("select idtypeperson from tbperson where idperson = $id");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret->fields['idtypeperson'];
    }

    public function mysqlVersion() {
        $ret = $this->select("select version() as version");
        return $ret->fields['version'];
    }	
    
    public function foundRows(){
		return $this->select("SELECT FOUND_ROWS() AS `found_rows`");
	}

    /*
     * Execute query in database
     *
     * @access public
     * @param $query Query to execute
     *
     * @return array ret bollean , msg string Error message
     */
    public function systemUpdateExecute($query)
    {
        $ret =  $this->db->Execute($query);

        $msg = '';
        if (!$ret) $msg = $this->db->ErrorMsg();
        return array("ret" => $ret,
                     "msg" => $msg
                    ) ;



    }


}


?>
