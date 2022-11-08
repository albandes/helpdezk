<?php
/**
 * Created by PhpStorm.
 * User: Rogério Albandes
 * Date: 03/04/15
 * Time: 08:55
 */

session_start();
$arrRelData = $_SESSION['relData'] ;

switch ($_POST['tpFile']) {
    case "CSV":
        $csv = array();

        foreach ($arrRelData as $row) {
            $Reldata = json_decode($row);
            array_push($csv, str_replace('&nbsp;', '', $Reldata->code) . '|' . str_replace('&nbsp;', '', utf8_decode($Reldata->name)) . '|' . str_replace('&nbsp;', '', $Reldata->date) . '|' . str_replace('&nbsp;', '', $Reldata->credit) . '|' . str_replace('&nbsp;', '', $Reldata->debit)) ;
        }

        $filename = "report_".time().".csv";
        $fileNameWrite = DOCUMENT_ROOT . path . '/app/downloads/tmp/'. $filename ;

        $fp = fopen($fileNameWrite, 'w');

        if(!$_POST['txtSeparator'])	$_POST['txtSeparator'] = ",";
        foreach ($csv as $line) {
            fputcsv($fp, explode('|', $line), $_POST['txtSeparator']);
        }
        fclose($fp);

        $fileNameUrl = path . '/app/downloads/tmp/'. $filename ;
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate');
        header("Content-type: text/csv");
        header('Content-Disposition: attachment; filename="'.$fileNameUrl.'"');

        echo $fileNameUrl;
        break;
}