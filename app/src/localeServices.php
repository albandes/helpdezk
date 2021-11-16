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
		$lang = (!isset($parameters['lang'])) ? $_ENV['DEFAULT_LANG'] : $parameters['lang'];
		$vocabularyDAO = new vocabularyDAO(); 
		$translate = $vocabularyDAO->getVocabulary($message,$lang);
		
        return (!is_null($translate) && !empty($translate)) ? $translate->getKeyValue() : $message;
    }
}