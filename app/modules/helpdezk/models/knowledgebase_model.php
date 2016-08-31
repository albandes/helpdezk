<?php
class knowledgebase_model extends Model{

	public function getArticles($where = null, $order = null, $limit = null){
		$database = $this->getConfig('db_connect');

		if ($database == 'mysqlt') {
            $query = "SELECT
						  SQL_CALC_FOUND_ROWS *,
						  a.idbase,
						  b.idcategory,
						  b.name AS category,
						  a.name,
						  a.problem,
						  a.solution,
						  a.date_register,
						  a.date_edit,
						  a.idperson_edit,
						  a.faq,
						  c.name AS author,
						  d.name AS author_edit,
						  e.idattachment,
						  e.real_filename,
						  e.filename
						FROM hdk_base_knowledge a
						  LEFT JOIN tbperson d
						    ON a.idperson_edit = d.idperson
						  LEFT JOIN hdk_base_category b
						    ON a.idcategory = b.idcategory
						  LEFT JOIN tbperson c
						    ON a.idperson = c.idperson
						  LEFT JOIN hdk_base_attachment e
						    ON e.idbase = a.idbase
						  $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "SELECT
                          count(a.idbase) over () totalRows,
						  a.idbase,
						  b.idcategory,
						  b.name AS category,
						  a.name,
						  a.problem,
						  a.solution,
						  to_char(a.date_register,'RRRR-MM-DD HH24:MI:SS') AS date_register,
  						  to_char(a.date_edit,'RRRR-MM-DD HH24:MI:SS') AS date_edit,
						  a.idperson_edit,
						  a.faq,
						  c.name AS author,
						  d.name AS author_edit,
						  e.idattachment,
						  e.real_filename,
						  e.filename
						FROM hdk_base_knowledge a
						  LEFT JOIN tbperson d
						    ON a.idperson_edit = d.idperson
						  LEFT JOIN hdk_base_category b
						    ON a.idcategory = b.idcategory
						  LEFT JOIN tbperson c
						    ON a.idperson = c.idperson
						  LEFT JOIN hdk_base_attachment e
						    ON e.idbase = a.idbase
						   $where $order";
            if($limit){
                $limit = str_replace('LIMIT', "", $limit);
                $p     = explode(",", $limit);
                $start = $p[0] + 1; 
                $end   = $p[0] +  $p[1]; 
                $query =    "
                            SELECT   *
                              FROM   (SELECT                                          
                                            a  .*, ROWNUM rnum
                                        FROM   (  
                                                  
                                                $core 

                                                ) a
                                       WHERE   ROWNUM <= $end)
                             WHERE   rnum >= $start         
                            ";
            }else{
                $query = $core;
            }
        }

        return $this->db->Execute($query);
	}

    public function getCategories($where = null) {
        return $this->db->Execute("
        							SELECT
									  a.idcategory,
									  a.name,
									  a.idcategory_reference,
									  (SELECT count(*) as total FROM hdk_base_category WHERE idcategory_reference = a.idcategory) as total
									FROM hdk_base_category a
									$where
									ORDER BY a.idcategory_reference ASC
        					");
    }
	
	public function setCategory($name,$id){
		return $this->db->Execute("INSERT INTO hdk_base_category (name,idcategory_reference) VALUES ('$name','$id')");
	}
	
	public function updateCategory($name,$idref,$id){
		return $this->db->Execute("UPDATE hdk_base_category SET name = '$name', idcategory_reference = '$idref' WHERE idcategory = $id");
	}
	
	public function setArticle($cmbCategory, $txtTitle, $chkFAQ, $txtDescPro, $txtSolPro, $idperson)
    {
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			return $this->db->Execute("INSERT INTO hdk_base_knowledge (idcategory,name,problem,solution,date_register,idperson,idperson_edit,faq) VALUES ('$cmbCategory','$txtTitle','$txtDescPro','$txtSolPro',NOW(),'$idperson','$idperson','$chkFAQ')");
        } elseif ($database == 'oci8po') {
        	return $this->db->Execute("INSERT INTO hdk_base_knowledge (idcategory,name,problem,solution,date_register,idperson,idperson_edit,faq) VALUES ('$cmbCategory','$txtTitle','$txtDescPro','$txtSolPro',SYSDATE,'$idperson','$idperson','$chkFAQ')");
        }
	}

    /**
     * Update a Article in hdk_base_knowledge table
     *
     * @access public
     * @param int $cmbCategory Category ID .
     * @param string $txtTitle  Article Title
     * @param string $chkFAQ Article is visible in the FAQ
     * @param string $txtDescPro Problem description
     * @param int $idbase hdk_base_knowledge ID
     * @param int $idperson_edit ID of the person who edited the article
     * @return object
     */
	public function updateArticle($cmbCategory, $txtTitle, $chkFAQ, $txtDescPro, $txtSolPro, $idbase, $idperson_edit ){
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			return $this->db->Execute("UPDATE hdk_base_knowledge SET idcategory='$cmbCategory', name='$txtTitle', problem='$txtDescPro', solution='$txtSolPro', faq='$chkFAQ', date_edit=NOW(), idperson_edit='$idperson_edit' WHERE idbase = $idbase");
        } elseif ($database == 'oci8po') {
            $qry = "
                    -- SET DEFINE OFF;
                    DECLARE
                        ClobDesc CLOB := '$txtDescPro';
                        ClobSol  CLOB := '$txtSolPro' ;
                    BEGIN
                        UPDATE hdk_base_knowledge SET idcategory='$cmbCategory', name='$txtTitle', problem = ClobDesc, solution = ClobSol, faq='$chkFAQ', date_edit=sysdate, idperson_edit='$idperson_edit' WHERE idbase = $idbase ;
                    END;
                    ";
            $ret = $this->db->Execute($qry);
            if (!$ret) {
                $sError = $vSQL . "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
                $this->error($sError);
                return false;
            }
            return $ret;
        }

	}

	public function deleteArticle($id){
		return $this->db->Execute("DELETE FROM hdk_base_knowledge WHERE idbase = $id");
	}

	public function hasChild($idcategory){
		return $this->db->Execute("SELECT count(*) as total FROM hdk_base_category WHERE idcategory_reference = $idcategory");
	}

	public function maxfile() {
		$database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
           $ret = $this->select("SELECT Auto_increment as cod FROM information_schema.tables WHERE table_name = 'hdk_base_attachment' AND table_schema = DATABASE()");
        } elseif ($database == 'oci8po') {
           $ret = $this->select("SELECT hdk_base_attachment_seq.nextval as cod FROM dual");  
        }
        return $ret;
    }
	
	public function insertAttachment($idbase, $filename, $real_filename){
		return $this->db->Execute("INSERT INTO hdk_base_attachment (filename, idbase, real_filename) VALUES ('$filename',$idbase,'$real_filename')");
	}

	public function getAttachment($where = null){
		return $this->db->Execute("SELECT idattachment, filename, idbase, real_filename FROM hdk_base_attachment $where");
	}

	public function deleteAttachment($id){
		return $this->db->Execute("DELETE FROM hdk_base_attachment WHERE idattachment = $id");
	}
	
}
