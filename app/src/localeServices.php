<?php

namespace App\src;
use \Nette\Localization\Translator;
use App\modules\admin\dao\mysql\vocabularyDAO;

class localeServices implements Translator{
    /**
	 * Translates the given string.
	 * @param  mixed  $message
	 * @param  mixed  ...$parameters
	 */
	function translate($message, ...$parameters): string{
		/*$retDAO = new VocabularyDAO(); 
		$ret = $retDAO->getVocabulary($message,$_ENV['LANG']);
		echo "<pre>",print_r($ret,true),"</pre>";*/
        return $message;
    }
}