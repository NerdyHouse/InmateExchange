<?php

namespace App\Library\Format;

use App\Library\Format\Email;
use App\Library\Utilities;

/**
 * Description of DailyLines
 *
 * @author jacobreadinger
 */
class RoundballEmail extends Email {
    
    // Generate the formatted emails
    public function create_email_text() {
        
        // Keep track of this
        $totalChars     = 0;
        $totalLines     = 0;
        
        $emails         = array();
        $emailNum       = 0;
        
        // For breaking emails up
        $maxPerLine     = 150;
        $cols           = $this->data['cols'];
        $colW           = $maxPerLine/$cols;
        
        $text  = "";
        
        foreach($this->data['rows'] as $row) {
            foreach($row as $cell) {
                $cols    = intval($cell['cols']);
                $cellW   = $colW*$cols;
                $val     = $cell['val'];
                $valW    = strlen($val);
                $leftSp  = round((($cellW-$valW)/2),0);
                $rightSp = round((($cellW-$valW)/2),0,PHP_ROUND_HALF_DOWN);
                
                for($i = 0; $i < $leftSp; $i++) {
                    $text .= " ";
                    $totalChars++;
                }
                $text .= $val;
                $totalChars += $valW;
                for($i = 0; $i < $rightSp; $i++) {
                    $text .= " ";
                    $totalChars++;
                }
            }
            
            $totalLines++;
            $text .= "\r";
            
            if((($totalChars + $maxPerLine) > $this->maxChars) || ($totalLines + 1 > $this->maxLines)) {
                
                $text .= "##### NEW EMAIL #####"."\r";
                $totalChars = 0;
                $totalLines = 0;
                $emailNum++;
            }
        }
        
        // Finish it up
        $text .= "\r";
        $text .= "##### END #####";
        return $text;
        
    }
}
