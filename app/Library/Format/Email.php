<?php

namespace App\Library\Format;

/**
 * Description of Email
 *
 * @author jacobreadinger
 */
class Email {
    
    public $data;
    public $chars;
    public $sport;
    public $maxChars;
    public $maxLines;

    public function __construct($data,$sport = 'all') {
        $this->data         = $data;
        $this->chars        = array(25,9,10,10,12,10,12);
        $this->sport        = $sport;
        $this->maxChars     = 12500;
        //$this->maxChars     = 1000;
        $this->maxLines     = 300;
    }
    
}
