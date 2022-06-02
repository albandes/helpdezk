<?php

class Controllers extends System {

    protected function view($nome, $vars=NULL) {
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);
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


    // echo htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    public function _sanitize($string)
    {
        return mysql_real_escape_string(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
    }

    public function _makeToken()
    {
        $token =  hash('sha512',rand(100,1000));
        $_SESSION['TOKEN'] =  $token;
        return $token ;
    }

    public function _getToken()
    {
        session_start();
        return $_SESSION['TOKEN'];

    }

    public function _checkToken() {
        if (empty($_POST) || empty($_GET) ) {
            return false;
        } else {
             if($_POST['_token'] == $this->_getToken() || $_GET['_token'] == $this->_getToken()) {
                return true;
            }
        }

        return false;
    }

    //Evita SQL Injection e ataque XSS
    function sanitize_item($item) {
        $item = addslashes(htmlspecialchars($item));
        return $item;
    }


}

?>
