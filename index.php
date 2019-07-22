<?php
/*  
 --- Copyright 2011, 2012 Pipegrep. ---
 
   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   
   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>
 
  
  
   Este programa é um software livre; você pode redistribui-lo e/ou 
   modifica-lo dentro dos termos da Licença Pública Geral GNU como 
   publicada pela Fundação do Software Livre (FSF); na versão 2 da 
   Licença, ou (na sua opinião) qualquer versão.



   Este programa é distribuido na esperança que possa ser  util, 
   mas SEM NENHUMA GARANTIA; sem uma garantia implicita de ADEQUAÇÂO a qualquer
   MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
   Licença Pública Geral GNU para maiores detalhes.


   Você deve ter recebido uma cópia da Licença Pública Geral GNU
   junto com este programa, se não, escreva para a Fundação do Software
   Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
error_reporting(E_ERROR|E_PARSE);

ini_set('xdebug.max_nesting_level', 512);

$curr_url = $_GET['url'];
$curr_url = explode("/", $curr_url);

define('CONTROLLERS', 'app/modules/'.$curr_url[0].'/controllers/');
define('VIEWS', 'app/modules/'.$curr_url[0].'/views/');
define('MODELS', 'app/modules/'.$curr_url[0].'/models/');
define('SMARTY', 'includes/smarty/');
define('FPDF',   'includes/classes/fpdf/');

require_once('system/system.php');
require_once('system/controller.php');
require_once('system/model.php');
require_once('system/common.php');

/*
function __autoload( $file ){
   if (strpos($file, "PHPExcel") === false  ) {
        if(file_exists(MODELS.$file.'.php')) {
            require_once (MODELS.$file.'.php');
        } else {
            die ('The model file does not exist: ' . MODELS.$file.'.php' );
        }
    }
    //require_once (MODELS.$file.'.php'); }
}
*/

$system = new System();

$path_default = $system->getConfig("path_default");
if(substr($path_default, 0,1)!='/'){
    $path_default='/'.$path_default;
}
if ($path_default == "/..") {   
	define(path,"");
} else {
    define(path,$path_default);
}
// no caso localhost document root seria D:/xampp/htdocs
$document_root=$_SERVER['DOCUMENT_ROOT'];
if(substr($document_root, -1)!='/'){
    $document_root=$document_root.'/';
}        
define('DOCUMENT_ROOT',$document_root);
define('LANGUAGE',$system->getConfig("lang"));
define('theme',$system->getConfig("theme"));
define('`PATH_ANEXO','');
// Since April 18, 2017
define ('HELPDEZK_PATH', realpath(DOCUMENT_ROOT.path)) ;

$system->run();

?>
