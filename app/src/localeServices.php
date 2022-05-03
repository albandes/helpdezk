<?php

namespace App\src;
use \Nette\Localization\Translator;
use App\modules\admin\dao\mysql\vocabularyDAO;

use App\modules\admin\models\mysql\vocabularyModel;

class localeServices implements Translator{
    /**
	 * Translates the given string.
	 * @param  mixed  $message
	 * @param  mixed  ...$parameters
	 */
	function translate($message, ...$parameters): string{
		$lang = (!isset($parameters['lang'])) ? $_ENV['DEFAULT_LANG'] : $parameters['lang'];
		$vocabularyDAO = new vocabularyDAO();
		$vocabularyModel = new vocabularyModel();

		$vocabularyModel->setKeyName($message)
						->setLocaleName($lang);

		$translate = $vocabularyDAO->getVocabulary($vocabularyModel);
		
        return ($translate['status'] && $translate['push']['object']->getKeyValue() != "") ? $translate['push']['object']->getKeyValue() : $message;
    }
}