<?php


class search
{

    /**
     * Ignore directory
     *
     * @var array
     */
    public $_ignoredDir;

    /**
     * Language path
     *
     * @var array
     */
    public $_langPath;

    /**
     * Language file
     *
     * @var array
     */
    public $_langFile;

    /**
     * Search Path
     *
     * @var array
     */
    public $_searchPath;

    /**
     * Line break
     *
     * @var array
     */
    public $_lb;

    /**
     * Array with returns
     *
     * @var array
     */
    public $_arrayRet;

    /**
     * Array with exceptions (normally modules and programs name)
     *
     * @var array
     */
    public $_arrayExceptions;

    public $_line;


    public function __construct()
    {
        $this->_langPath    = "E:/home/rogerio/htdocs/git/helpdezk/app/lang/";
        $this->_langFile    = "pt_BR.txt";
        $this->_lb          = "<br>";

        $this->_searchPath  = ""   ;
        $this->_ignoredDir  = array('.','..','.DS_Store');

        $this->_arrayRet    = array();
        $this->_line[]    = array();

    }

    public function getLangVariablesUsage()
    {

        $lb="<br>";
        $keyName = '';
        $count = 0;

        $lines = file ($this->_langPath . $this->_langFile,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line_num => $line) {

            if (substr($line, 0, 1) == '#' or substr($line, 0, 1) == '[')
                continue;

            $aLine = explode("=", $line);
            $temp =   str_replace("\"" ,"",$aLine[1]) ;

            $rows[trim($aLine[0])]  = ltrim($temp);

            $count++;

        }

        echo "Lines : {$count}{$this->_lb}";


        $arrayRet = array();

        foreach ($rows as $k => $v) {

            $keyName = $k;

            unset($this->_line);
            $this->_line  = array();

            // Exception handling, usually name of modules and programs,
            // which are not in the PHP code but in the tbmodule and tbprogram tables.
            if (in_array($keyName, $this->_arrayExceptions)){
                $arrayRet1[] =  array ('id' => $keyName , 'file' => 'Exception', 'text' => $v );
            }

            $this->scanDir($keyName,$this->_searchPath);

            foreach ($this->_line as $line) {
                $arrayRet2[] =  array ('id' => $line['id'] , 'file' => $line['file'], 'text' => $v );
            }

        }


        $arrayRet = array_merge($arrayRet1,$arrayRet2);
        return $arrayRet;

    }


    private function scanDir($string,$dirPath){

        foreach(scandir($dirPath) as $dir){
            if(!in_array($dir,$this->_ignoredDir)){
                $tmpDir = "{$dirPath}/{$dir}";

                if(is_dir($tmpDir)){
                    $this->scanDir($string,$tmpDir);
                }
                else{
                    $this->scanFile($string,$tmpDir);
                }
            }
        }

    }

    private function scanFile($findme,$file){

        $lines = file ($file,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line_num => $line) {

            $pattern = '/\b'.$findme.'\b/';

            if(preg_match($pattern,$line)) {
                $this->_line[]  = array(
                    'id' => $findme,
                    'file' => $file
                );

            }


        }

    }


}