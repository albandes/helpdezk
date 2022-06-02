<?php 

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class lgpReportGroup extends lgpCommon
{

    /**
     * Create an instance, check session time
     * Common controller construct
     * Recovery of data gathered by common
     * Instantiation of the program model
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     * 
     * @access public
     */
    public function __construct()
    {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        $this->idPerson = $_SESSION['SES_COD_USUARIO'];

        // Module to which the program belongs
        $this->modulename = 'LGPD' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Program log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('lgpReportGroup');

        // Model instance of this program
        $this->loadModel('lgpreportgroup_model');
        $this->dbGroup = new lgpreportgroup_model();

        //Instance of the "logos" class, from the admin module
        $this->loadModel('admin/logos_model');
        $dbLogo = new logos_model();
        $this->dbLogo = $dbLogo;

    }
   
    /**
     * index
     * smarty processing of the program's home page
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function index()
    {

        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        // Processing basic templates
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);


        // Processing of data from the logos_model class
        $reportslogo = $this->dbLogo->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);

        // ASSIGN COMBOS 

        // Configuration group filter combo
        $typePerson = $this->dbGroup->getLgpTypePerson("LGP_group"); //First get the respective idtyperson in tbperson

        // Configuration of the report type filter combo
        $aRelGroup = $this->_comboRelGroup($typePerson['data'][0]['idtypeperson']);

        $smarty->assign('GroupIds', $aRelGroup['ids']); // Returns an array with the original keys
        $smarty->assign('GroupVals', $aRelGroup['values']); // Returns an array with the values ​​of the original
        $smarty->assign('idgroup', '');

        // Security token processing
        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Processing another template
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language

        // Processing the template
        $smarty->display('lgp-reportgroup.tpl');

    }

    public function _comboRelGroup($typePerson){

        $rs = $this->dbGroup->getGroups($typePerson);

        foreach($rs['data'] as $k=>$v){
            $fieldsID[] = $v['idgroup'];
            $values[]   = $v['name'];
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
        
    }

    /*
    *  Programming after form submission
    */
    public function getReport(){

        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $groupOpt = $_POST['cmbGroup'];

        $typePerson = $this->dbGroup->getLgpTypePerson("LGP_group");
        $typePerson = $typePerson['data'][0]['idtypeperson'];

        $where = " AND b.idtypeperson = $typePerson";
        //If the variable $groupOpt is equal to "ALL", will be a search for ALL groups
        //If not, that is, if the variable is different from "ALL", will be a search for one group
        $where .= $groupOpt == "ALL" ? "": " AND a.idgroup = $groupOpt"; 

        $order = "ORDER BY d.name ASC";

        // The variable receives the return from SQL
        $ret = $this->dbGroup->getPersonsByGroup($where, $order, $limit);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt("{$ret['message']}\nProgram: {$this->program}. Method: ". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        // Array for sending the type of report and period (start and end date) to the defineTable () method
        $arrGroup = array(
            "group_choosed" =>$groupOpt,
            "group_name" => $grpName
        );

        //Subheader
        // If it is equal to "ALL" the value will be "All", if not the name of the field brought by SQL
        $subcab = array(
            "{$this->getLanguageWord('lgp_group')}"=> $groupOpt == "ALL" ? "{$this->getLanguageWord('all')}" : $ret['data']->fields['group_name'] 
        );

        //If not data is return by the search
        if($ret['data'] == NULL){
                    
            // Modal "Record not found"
            echo json_encode(false);

        }else{

            // Create the table using the data gathered by the research
            $table_data = $this->defineTable($arrGroup, $ret['data'], $subcab);

            // Returns, for Ajax, the final content of the concatenation performed in the defineTable() method
            // The final product is a string of the contents of the table body
            echo json_encode($table_data);

        }

    }

    public function defineTable($arrGroup = array(), $data_search = array(), $subheader = array()){

        $arrList = array();

        // For creating the table with colspan
        $grupoAtual = "X";
        while(!$data_search->EOF){

            // If the current group is different from the previous group
            if($data_search->fields['group_name'] != $grupoAtual){

                // The previous group becomes the current group
                $grupoAtual = $data_search->fields['group_name'];

                // A new colspan cell is created that will receive the name of the group
                $tr_list .= "<tr>
                        <td class = 'text-center' colspan='2' style='background-color: #78909c;color: #000;'>
                            {$data_search->fields['group_name']}
                        </td>
                    </tr>";

                // A new colspan cell is created that will receive the word "members"
                $tr_list .= "<tr>
                    <td class = 'text-center' colspan='2' style='background-color: #78909c;color: #000;'>
                        Membros
                    </td>
                </tr>";
            
            
            }// If not, if the current area is the same as "previous area" (current so far)

            // Now the data that will be used in the table's <td> are rescued
            $memberName = "{$data_search->fields['person_name']}";

            //Now, the variable next to the fixed header you received, adds the structure of the <tds> and their values
            $tr_list .= "<tr>
                    <td>{$memberName}</td>
                </tr>";

            // This array receives the data used in the table data of the table
            array_push($arrList, array($grupoAtual, $memberName));

            // To prevent everlasting loops
            $data_search->MoveNext();
        }
            
        // This session is created so that the data can be used in the Export method
        // If not, the data would have to be returned to Ajax, and from itself back to PHP
        $_SESSION['reportDataExport'] =  array(
            "titulo1" => "{$this->getLanguageWord('lgp_relgroups')}",
            "subcabecalho" => $subheader,
            "tabhead" => array("{$subheader['Grupo']}","{$this->getLanguageWord('lgp_members')}"),
            "rows" => $arrList,
            "wth" => array(120,60),
            "orientation" => "P",
            "wLine" => 200,
            "wh2" => 190,
            "wrow" => array(120,60),
            "alignrow" => array("L","R"),
            "colspan" => true,
            "subrow_member" => true
            );

        // Return of the built table, in either case
        return $tr_list;

    }

    public function exportReport(){

        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        // Recovery of data saved in the session
        $arrReportData = $_SESSION['reportDataExport'];
        
        switch ($_POST['typeFile']) {

            //if the export choice was "CSV"
            // In this case, only data from table data (td) are sent
            case "CSV": 
            
                $fileNameUrl = $this->makeCsvFile($arrReportData['rows']);

                header('Set-Cookie: fileDownload=true; path=/');
                header('Cache-Control: max-age=60, must-revalidate');
                header("Content-type: text/csv");
                header('Content-Disposition: attachment; filename="'.$fileNameUrl.'"');

                echo $fileNameUrl;
                break;

            case "XLS":

                header('Set-Cookie: fileDownload=true; path=/');
                header('Cache-Control: max-age=60, must-revalidate');
                header('Content-type: application/x-msexcel');
                header('Content-Length: ' . filesize($fileNameWrite));
                header('Content-Transfer-Encoding: binary');
                header('Content-Disposition: attachment; filename="'.$fileNameUrl.'"');

                echo $table;

                break;
            
            //if the export choice was "PDF"
            // Calling the method of building the PDF, sending the composition data from the table
            // In this case, the dimension data from the table is also sent
            case "PDF": 

                echo $this->makePdfFile($arrReportData); 
                break;
        }

    }

    public function makeCsvFile($arrReportData){

        $csv = array();

        foreach ($arrReportData as $row=>$val) {
            $bus = "";
            foreach($val as $r=>$v){
                // Each variable receives the configuration of each cell
                $bus .= str_replace('&nbsp;', '', $this->clearAccent($v))."|";
            }
            // In the last item, the concatention point is subtracted (because there will be no next item)
            $bus = substr($bus,0,-1);

            // The $csv variable receives the final structure, of concatenated cells
           array_push($csv, $bus) ;
        }

       $filename = $_SESSION['SES_NAME_PERSON'] . "_report_".time().".csv";
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
            if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                $this->logIt("Export Enrollment Report " . ' - Directory ' . $this->helpdezkPath . '/app/tmp' . ' is not writable ' ,3,'general',__LINE__);

        }

        $fp = fopen($fileNameWrite, 'w');

        if(!$_POST['txtDelimiter'])	$_POST['txtDelimiter'] = ",";
        foreach ($csv as $line) {
            fputcsv($fp, explode('|', $line), $_POST['txtDelimiter']);
        }
        fclose($fp);

        $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

        // Returns the file URL
        return $fileNameUrl;

    }

    public function makePdfFile($rowsData) 
    {

        // class FPDF with extension to parsehtml
        // Create a instance of library
        $pdf = $this->_returnfpdfhdk();

        //Font parameters to be used in the report.
        $FontFamily = 'Arial';
        $FontStyle  = '';
        $FontSize   = 8;
        $CelHeight = 4;

        $title =  html_entity_decode(utf8_decode($rowsData['titulo1']),ENT_QUOTES, "ISO8859-1"); //Title //$rowsData['titulo1']?
        $PdfPage = (utf8_decode($this->getLanguageWord('PDF_Page'))) ; //Page numbering
        $leftMargin = 10; 

        $logo = array("file" => $this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage(),
            "posx" => $leftMargin + 10,
            "posy" => 8
        );

        //Cycles through the data array that forms the subheader
        //Its accessible via the "subcabecalho" key of the session variable data structure
        foreach($rowsData['subcabecalho'] as $key => $value){

                $h2[$key] = array(

                    "txt"=>html_entity_decode(utf8_decode("{$key}: {$value}"),ENT_QUOTES, "ISO8859-1"), 
                    "width"=>$rowsData['wh2'],
                    "height" => $CelHeight,
                    "border" => 0,
                    "ln"=>1,
                    "fill"=>0,
                    "align" => 'L');

        }

        $headerparams = array(
            "leftMargin" => $leftMargin,
            "pdfpage" => $PdfPage,
            "FontFamily" => $FontFamily,
            "FontStyle"  => $FontStyle,
            "FontSyze"  => $FontSize,
            "logo" => $logo,
            "title" => $title,
            "tableHeader" => $th,
            "h2" => $h2,
            "lineWidth" => $rowsData['wLine']
        );

        $pdf->AliasNbPages();
        $pdf->AddPage($rowsData['orientation'],'A4',$headerparams); //Add new page in file

        $pdf->SetWidths($rowsData['wrow']); 
        $pdf->SetAligns($rowsData['alignrow']);

        $grupo_atual = "X";
        // $rowsData['rows'] has, in each of its indexes, another data array
        // Each of these data arrays has, in the first position 0, the name of the area
        foreach($rowsData['rows'] as $linha => $value){

                if($rowsData['rows'][$linha][0] != $grupo_atual){

                    // The "area" field of the current line becomes the "area_atual"
                    $grupo_atual = $rowsData['rows'][$linha][0];

                    // The colspan column is created with the value / name of the current area
                    $pdf->setFillColor(120,144,156);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->Cell($leftMargin);            
                    $pdf->Cell(180.1,4,html_entity_decode(utf8_decode($grupo_atual),ENT_QUOTES, "ISO8859-1"),0,1,'C',1);

                    // The colspan column is created with the value / name of the current area
                    $pdf->setFillColor(120,144,156);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->Cell($leftMargin);            
                    $pdf->Cell(180.1,4,html_entity_decode(utf8_decode("Membros"),ENT_QUOTES, "ISO8859-1"),0,1,'C',1);
                    
                }
            
            // "Normal" lines are created for listing items
            $pdf->setRowFillColor(205,205,205);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell($leftMargin);

            // Now we go through the data array of the current position
            foreach($value as $k => $v){               

                $td[$k] = html_entity_decode(utf8_decode($value[$k +1]),ENT_QUOTES, "ISO8859-1");
            }

            $pdf->row($td);
        }

        $filename = $_SESSION['SES_NAME_PERSON'] . "_report_".time().".pdf";
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;
        $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
            if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                $this->logIt("Export Person Report " . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp' . ' is not writable ' ,3,'general',__LINE__);

        }

        $pdf->Output($fileNameWrite,'F');

        // Returns the file URL
        return $fileNameUrl;

    }

    /**
     * _returnfpdfhdk
     * fpdfhdk class instance
     * This class is the original FPDF class, but changed
     * 
     * @return object fpdfhdk class instance
     * 
     * @author Marcelo Moreira <marcelo.moreira@marioquintana.com.br>
     */
    public function _returnfpdfhdk() {
        require_once(FPDF . 'fpdfhdk.php');
        $pdf = new fpdfhdk();
        return $pdf;
    }


}