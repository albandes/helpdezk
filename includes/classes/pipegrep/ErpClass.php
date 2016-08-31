<?php

class ErpClass extends System {

    public function ParseOfx ($file)
    {

        $source = fopen($file, 'r');
        if (!$source) {
            die ('Unable to open OFX file: ' . $file);
        }

        // skip headers of OFX file
        $headers = array();

        $charsets = array(
            1252 => 'WINDOWS-1251'
        );

        while(!feof($source)) {
            $line = trim(fgets($source));
            if ($line === '') {
                break;
            }
            list($header, $value) = explode(':', $line, 2);
            $headers[$header] = $value;
        }

        $buffer = '';

        // dead-cheap SGML to XML conversion
        // see as well http://www.hanselman.com/blog/PostprocessingAutoClosedSGMLTagsWithTheSGMLReader.aspx
        while(!feof($source)) {
            $line = trim(fgets($source));
            if ($line === '') continue;
            $line = iconv($charsets[$headers['CHARSET']], 'UTF-8', $line);
            if (substr($line, -1, 1) !== '>') {
                list($tag) = explode('>', $line, 2);
                $line .= '</' . substr($tag, 1) . '>';
            }
            $buffer .= $line ."\n";
        }
        // use DOMDocument with non-standard recover mode
        $doc = new DOMDocument();
        $doc->recover = true;
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $save = libxml_use_internal_errors(true);
        $doc->loadXML($buffer);
        libxml_use_internal_errors($save);
        //echo $doc->saveXML();
        $arr = json_decode(json_encode(simplexml_load_string($doc->saveXML())), 1);
        return $arr ;


    }

} 