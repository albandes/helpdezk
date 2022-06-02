<?php

$debug = true ;

if ($debug)
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
else
    error_reporting(0);

session_start();

if (isCli()) {
    $CLI = true ;
    ini_set('html_errors', false);
}

$cron_path = getRootPath('cron-jobs') ;
define ('HELPDEZK_PATH', $cron_path) ;

$lineBreak = $CLI ? PHP_EOL : '<br>';

define('SMARTY', HELPDEZK_PATH.'includes/Smarty/');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronSystem.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronController.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronModel.php');

require_once(HELPDEZK_PATH . 'includes/classes/pipegrep/syslog.php');
require_once(HELPDEZK_PATH . 'includes/classes/pipegrep/trello.php');
require_once (HELPDEZK_PATH.'app/modules/admin/models/index_model.php');
require_once (HELPDEZK_PATH.'system/common.php');

$cronSystem = new cronSystem();

$cronSystem->setCronSession() ;
$cronSystem->SetEnvironment();

setLogVariables($cronSystem);
$lineBreak = $cronSystem->getLineBreak();

/*
 *  Models
 */
if(!setRequire(HELPDEZK_PATH.'app/modules/admin/models/features_model.php',$cronSystem)){exit;}
if(!setRequire(HELPDEZK_PATH.'app/modules/helpdezk/models/home_model.php',$cronSystem)){exit;}


$cronSystem->logIt('Run update IT Cards data at '.date("Y-m-d H:i:s").' - Program: ' . __FILE__,6,'general');

$admFeature = new features_model();
$dbHDKHome = new home_model();

$retFeat = getAPIFeature("hdk",5,$admFeature,$cronSystem);
$trello = new trello($retFeat['key'],$retFeat['secret'],$retFeat['token']);

$cards = $trello->getITCards($retFeat['board']);
$rows = count($cards['return']);

$defaultDt = '2050-01-01 08:00:00';

foreach($cards['return'] as $key=>$val){   
    $retList = $trello->getITLists($val['idList']);
    $retListID = $dbHDKHome->getITListID("WHERE `name` = '{$retList['return']['name']}'");
    if(!$retListID['success']){
        $cronSystem->logIt("Can't get card list data - Program: " . __FILE__ , 3, 'general', __LINE__);
        continue;
    }
    $listID = $retListID['data'][0]['iditlist'];
    
    $start = (isset($val['badges']['start']) && !empty($val['badges']['start'])) ? str_replace('T',' ',$val['badges']['start']) : NULL;
    $due = (isset($val['due']) && !empty($val['due'])) ? str_replace('T',' ',$val['due']) : $defaultDt;

    if($val['id'] == '615842ef3d6c018cc72a57c8'){ //model card
        continue;
    }

    $check = $dbHDKHome->getItCardData("WHERE a.id = '{$val['id']}'");
    if(!$check['success']){
        $cronSystem->logIt("Can't get card data - Program: " . __FILE__ , 3, 'general', __LINE__);
        continue;
    }
    
    if(count($check['data']) > 0){
        $cardID = $check['data'][0]['iditcard'];
        $upd = $dbHDKHome->updateITCard($check['data'][0]['iditcard'],$val['id'],$val['name'],$val['desc'],$listID,$start,$due);
        if(!$upd['success']){
            $cronSystem->logIt("Can't update card data - {$upd['message']} - Program: " . __FILE__ , 3, 'general', __LINE__);
            continue;
        }
    }else{
        $ins = $dbHDKHome->insertITCard($val['id'],$val['name'],$val['desc'],$listID,$start,$due);
        if(!$ins['success']){
            $cronSystem->logIt("Can't insert card data - {$ins['message']} - Program: " . __FILE__ , 3, 'general', __LINE__);
            continue;
        }
        $cardID = $ins['id'];
    }
    
    foreach($val['idChecklists'] as $k){
        $retCheckList = processCheckList($cardID,$k,$trello,$dbHDKHome,$cronSystem);
        if(!$retCheckList['success']){
            $cronSystem->logIt("Can't get checklist data - {$retCheckList['message']} - Program: " . __FILE__ , 3, 'general', __LINE__);
            continue;
        }
    }
    
    $delMembers = $dbHDKHome->deleteCardMembers($cardID); //remove card's members

    foreach($val['idMembers'] as $m){
        $retMember = getInChargeID($m,$trello,$dbHDKHome,$cronSystem);

        if(!$retMember){
            $cronSystem->logIt("Can't get member data - Program: " . __FILE__ , 3, 'general', __LINE__);
            continue;
        }

        $insMemLink = $dbHDKHome->insertITCardMember($cardID,$retMember);
        if(!$insMemLink['success']){
            $cronSystem->logIt("Can't insert card's member data - {$insChklist['message']} - Program: " . __FILE__ , 3, 'general', __LINE__);
            continue;
        }
    }
}

$cronSystem->logIt("Ran update IT Cards data at ".date("Y-m-d H:i:s")." - Cards processed: {$rows} - Program: " . __FILE__ ,6,'general');

die("OK\n") ;

function setLogVariables($cronSystem)
{
    $objSyslog = new Syslog();
    $log  = $objSyslog->setLogStatus() ;
    if ($log) {
        $objSyslog->SetFacility(18);
        $cronSystem->_logLevel = $objSyslog->setLogLevel();
        $cronSystem->_logHost = $objSyslog->setLogHost();
        if($cronSystem->_logHost == 'remote')
            $cronSystem->_logRemoteServer = $objSyslog->setLogRemoteServer();
    }
}

function getRootPath ($module)
{

    $path =  str_replace(DIRECTORY_SEPARATOR ."app".DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR . $module ,"",__DIR__ ) ;
    return $path . DIRECTORY_SEPARATOR;
}

function cronSets($cronSystem)
{

    $cron_path = getRootPath('cron-jobs');

    define('path', $cronSystem->_getPathDefault());

    if (isCli()) {
        define('DOCUMENT_ROOT', substr( $cron_path,0, strpos($cron_path,$cronSystem->pathDefault)).'/');
    } else {
        define('DOCUMENT_ROOT', $cronSystem->_getDocumentRoot());
    }
    define('LANGUAGE',$cronSystem->getConfig("lang"));
    define('HELPDEZK_PATH', realpath(DOCUMENT_ROOT.path)) ;
}

function isCli()
{
    if ( defined('STDIN') )
    {
        return true;
    }

    if ( php_sapi_name() === 'cli' )
    {
        return true;
    }

    if ( array_key_exists('SHELL', $_ENV) ) {
        return true;
    }

    if ( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0)
    {
        return true;
    }

    if ( !array_key_exists('REQUEST_METHOD', $_SERVER) )
    {
        return true;
    }

    return false;
}

function getAPIFeature($prefix,$categoryID,$admFeature,$cronSystem)
{
    $aFeature = array('IT Trello key','IT Trello token','IT Trello secret','IT Trello boards');
    $aRet = array();
    $ret = $admFeature->getConfigs($prefix,$categoryID);
    if(!$ret){
        $cronSystem->logIt("Can't get Config Data - Program: " . __FILE__ , 3, 'general', __LINE__);
        return false;
    }
    
    while(!$ret->EOF){
        if(in_array($ret->fields['config_name'],$aFeature)){
            if($ret->fields['config_name'] == 'IT Trello boards')
                $aRet['board'] = $ret->fields['value'];
            if($ret->fields['config_name'] == 'IT Trello key')
                $aRet['key'] = $ret->fields['value'];
            if($ret->fields['config_name'] == 'IT Trello token')
                $aRet['token'] = $ret->fields['value'];
            if($ret->fields['config_name'] == 'IT Trello secret')
                $aRet['secret'] = $ret->fields['value'];
        }

        $ret->MoveNext();
    }
    
    return $aRet;

}

function setRequire($requireFile,$cronSystem){
    if (!file_exists($requireFile)) {
        $cronSystem->logIt("{$requireFile} does not exist - Program: ". __FILE__ ,3,'email',__LINE__);
        return false;
    }else{
        return require_once($requireFile);
    }
}

function processCheckList($cardID,$checkListID,$trello,$dbHDKHome,$cronSystem)
{    
    $st = 0;
    $retCheckList = $trello->getChecklist($checkListID);
    if(!$retCheckList['success']){
        $cronSystem->logIt("Can't get checklist data - {$retCheckList['message']} - Program: " . __FILE__ , 3, 'general', __LINE__);
    }

    foreach ($retCheckList['return']['checkItems'] as $key=>$value) {
        $complete = $value['state'] == 'incomplete' ? 0 : 1;
        $inChargeID = ($value['idMember'] && !empty($value['idMember'])) ? getInChargeID($value['idMember'],$trello,$dbHDKHome,$cronSystem) : NULL;
        $due = (isset($value['due']) && !empty($value['due'])) ? str_replace('T',' ',$value['due']) : NULL;
        
        $check = $dbHDKHome->getActivityData("WHERE id = '{$value['id']}'");
        if(!$check['success']){
            $cronSystem->logIt("Can't get activity data - Program: " . __FILE__ , 3, 'general', __LINE__);
            continue;
        }

        if(count($check['data']) > 0){
            $activityID = $check['data'][0]['idactivity'];
            $upd = $dbHDKHome->updateActivity($activityID,$value['id'],$value['name'],$inChargeID,$due,$complete);
            if(!$upd['success']){
                $cronSystem->logIt("Can't update activy data - {$upd['message']} - Program: " . __FILE__ , 3, 'general', __LINE__);
                continue;
            }
            $st++;
        }else{
            $ins = $dbHDKHome->insertActivity($cardID,$value['id'],$value['name'],$inChargeID,$due,$complete);
            if(!$ins['success']){
                $cronSystem->logIt("Can't insert activy data - {$ins['message']} - Program: " . __FILE__ , 3, 'general', __LINE__);
                continue;
            }
            $activityID = $ins['id'];
            $st++;
        }
    }

    return $st <= 0 ? array('success'=>false,'message'=>'') :  array('success'=>true,'message'=>'');    

}

function getInChargeID($memberID,$trello,$dbHDKHome,$cronSystem){
    $ret = $trello->getMemberData($memberID);
    if(!$ret['success']){
        $cronSystem->logIt("Can't get member data - {$ret['message']} - Program: " . __FILE__ , 3, 'general', __LINE__);
        return null;
    }
    
    if(!empty($ret['return']['email'])){
        $aLogin = explode('@',$ret['return']['email']);
        $retID = $dbHDKHome->getPersonID("WHERE login = '{$aLogin[0]}'");
        if(!$retID['success']){
            $cronSystem->logIt("Can't get person data - {$retID['message']} - Program: " . __FILE__ , 3, 'general', __LINE__);
            return null;
        }
        return $retID['data'][0]['idperson'];
    }else{
        $retID = $dbHDKHome->getCardMemberID("WHERE b.idmember = '{$memberID}'");
        if(!$retID['success']){
            $cronSystem->logIt("Can't get person data - {$retID['message']} - Program: " . __FILE__ , 3, 'general', __LINE__);
            return null;
        }
        return $retID['data'][0]['idperson'];
    }

}
