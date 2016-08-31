<?php

class Controllers extends System {

    protected function view($nome, $vars=NULL) {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();
        $langVars2 = $smarty->get_template_vars();
        
        if (is_array($vars) && count($vars) > 0) {
            extract($vars, EXTR_PREFIX_ALL, 'view');
        }
        require_once(VIEWS . $nome);
    }

    public function setLimit($sql, $limit, $offset=0) { 
		$result = $sql." LIMIT $limit OFFSET $offset"; 
		return $result;          
	} 


	public function setLimitOracle($sql, $limit, $offset=0) { 
        $max = $offset + $limit; 
        $sql = "SELECT
                    *
                    FROM
                        (SELECT ROWNUM as num_line, T.* FROM ($sql) T WHERE ROWNUM <= $max)
                    WHERE num_line > $offset";
            return $sql;
    } 

}

?>
