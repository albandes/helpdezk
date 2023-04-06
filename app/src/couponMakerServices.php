<?php

namespace App\src;

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class couponMakerServices{
	
	/**
     * @var object
     */
    protected $couponLogger;
    
    /**
     * @var object
     */
    protected $couponEmailLogger;

	/**
     * @var int
     */
    protected $_colNumber;

	public function __construct($colNumber=40)
    {
        $appSrc = new appServices();
        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);
        
        $stream = $appSrc->_getStreamHandler();
        $stream->setFormatter($formatter);

        $this->couponLogger  = new Logger('helpdezk');
        $this->couponLogger->pushHandler($stream);
        
        // Clone the first one to only change the channel
        $this->couponEmailLogger = $this->couponLogger->withName('email');

        $this->_colNumber = $colNumber;

    }

    /**
     * en_us Adds the necessary amount of spaces at the beginning of the informed string to make it centered on the screen
	 * pt_br Adiciona a quantidade necessária de espaços no inicio da string informada para deixa-la centralizada na tela
     *
     * @param string $info String to be centered
     * @return string
     */
    public function centralize($info)
    {
        $aux = strlen($info);

        if ($aux < $this->_colNumber) {
            // calculates how many spaces should be added before the string to make it centered
            $spaces = floor(($this->_colNumber - $aux) / 2);

            $space = '';
            for ($i = 0; $i < $spaces; $i++){
                $space .= ' ';
            }

            // returns the string with the spaces needed to center it
            return $space.$info;

        } else {
            // if greater than or equal to the number of columns returns the string trimmed with the maximum number of columns.
            return substr($info, 0, $this->_colNumber);
        }

    }

    /**
     * en_us Adds the number of spaces informed in the String passed in the informed position.
	 * 
	 * 		 If the informed string is greater than the number of positions declared, then it cuts the string so that it has the number
	 * 		 exact characters of the positions.
	 * 
	 * pt_br Adiciona a quantidade de espaços informados na String passada na possição informada.
     *
     * 		 Se a string informada for maior que a quantidade de posições informada, então corta a string para ela ter a quantidade
     * 		 de caracteres exata das posições.
     *
     * @param string $string 	String to have spaces added.
     * @param int 	 $positions Number of column positions
     * @param string $place 	Where will add the spaces. I (start) or F (end).
     * @return string
     */
    public function addSpaces($string, $positions, $place)
    {

        $aux = strlen($string);

        if ($aux >= $positions)
            return substr ($string, 0, $positions);

        $dif = $positions - $aux;

        $spaces = '';

        for($i = 0; $i < $dif; $i++) {
            $spaces .= ' ';
        }

        if ($place === 'I')
            return $spaces.$string;
        else
            return $string.$spaces;

    }
}