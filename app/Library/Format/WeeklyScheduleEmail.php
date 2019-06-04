<?php

namespace App\Library\Format;

use App\Library\Format\Email;
use App\Library\Utilities;

/**
 * Description of WeeklyScheduleEmail
 *
 * @author jacobreadinger
 */
class WeeklyScheduleEmail extends Email {
    
    // Generate the formatted emails
    public function create_email_text() {
        
        // Keep track of this
        $totalChars     = 0;
        $totalLines     = 0;
        
        $emails         = array();
        $emailNum       = 0;
        
        // For breaking emails up
        $maxPerLine     = array_sum($this->chars);
        $maxPerGame     = $maxPerLine*4;
        $maxPerDay      = $maxPerLine*5;
        
        $text       = "";
        $dayloop    = 0;
        $first      = true;
        
        // Setup the headers
        if($this->sport == 'nhl') {
            $headers = array('open','current','PuckLine','(1st HL)','1HSc','Final Score');
        } else {
            $headers = array('open','current','MLine','(1st HL)','1HSc','Final Score');
        }
        
        foreach ($this->data as $day) {
            
            //$title = str_replace(array(chr(194), strtoupper($this->sport)),"",$day['title']);
            $title = $day['date'];
            $text .= $title;
            if($first) {
                // Account for title
                for($s = 0; $s < $this->chars[0] - strlen($title); $s++) {
                    $text .= " ";
                    $totalChars++;
                }
                // Account for headers
                for($i = 0; $i < count($headers); $i++) {
                    $text .= $headers[$i];
                    $totalChars += strlen($headers[$i]);
                    for($j = 0; $j < $this->chars[$i+1] - strlen($headers[$i]); $j++) {
                        $text .= " ";
                        $totalChars++;
                    }
                }
                $first = false;
            }
            
            $text .= "\r";
            $totalChars += strlen($title);
            $totalLines++;
            
            $gameloop = 0;
            foreach($day['games'] as $game) {
                $time = $game['time'];
                $text .= $time;
                $totalChars += strlen($time);
                $text .= "\r";
                $totalLines++;
                
                /*****************
                * Visitor Row
                */
                $rotation   = $game['visitor']['rotation'];
                $name       = Utilities::shorten_team_name($game['visitor']['name']);

                $lines      = array(
                    $game['visitor']['lines']['open'],
                    $game['visitor']['lines']['current'],
                    $game['visitor']['lines']['money']
                );
                
                // Might as well do this here
                $totalChars += strlen($rotation.$name.$lines[0].$lines[1].$lines[2]);
                
                // Team name stuff
                $text .= $rotation;
                for($s = 0; $s < 4 - strlen($rotation); $s++) {
                    $text .= " ";
                    $totalChars++;
                }
                $text .= strtoupper($name);
                for($s = 0; $s < ($this->chars[0] - (strlen($name) + 4)); $s++) {
                    $text .= " ";
                    $totalChars++;
                }
                
                // Known Lines
                for($i = 0; $i < count($lines); $i++) {
                    $text .= $lines[$i];
                    for($j = 0; $j < $this->chars[$i+1] - strlen($lines[$i]); $j++) {
                        $text .= " ";
                        $totalChars++;
                    }
                }
                
                // Stuff that may not exist...
            
                // Halftime lines
                $line = "";
                if(array_key_exists('ht', $game['visitor']['lines'])) {
                    $line = trim($game['visitor']['lines']['ht']);
                    $sign = substr($line,0,1);

                    if($sign !== '-' && $sign !== '+') {
                        $line = 'pk';
                    } else if($sign !== '-') {
                        $line = $game['ht']['total'];
                    }

                    $text .= $line; 
                    $totalChars += strlen($line);
                }
                for($s = 0; $s < $this->chars[4] - (strlen($line)); $s++) {
                    $text .= " ";
                    $totalChars++;
                }

                // Scores
                $htScore    = "";
                $finalScore = "";
                if(array_key_exists('scores', $game['visitor'])) {
                    if(array_key_exists('ht', $game['visitor']['scores'])) {
                        $htScore = $game['visitor']['scores']['ht'];
                    }
                    if(array_key_exists('first', $game['visitor']['scores'])) {
                        $finalScore .= $game['visitor']['scores']['first'];
                    }
                    if(array_key_exists('progress', $game['visitor']['scores'])) {
                        $finalScore .= " ".$game['visitor']['scores']['progress'];
                    }
                }
                $text .= $htScore; 
                $totalChars += strlen($htScore);
                for($s = 0; $s < $this->chars[5] - (strlen($htScore)); $s++) {
                    $text .= " ";
                    $totalChars++;
                }
                $text .= $finalScore; 
                $totalChars += strlen($finalScore);

                $text .= "\r";
                $totalLines++;
                
                /*****************
                * Host Row
                */
                // Get team stuff
                $rotation   = $game['host']['rotation'];
                $name       = Utilities::shorten_team_name($game['host']['name']);

                $lines      = array(
                    $game['host']['lines']['open'],
                    $game['host']['lines']['current'],
                    $game['host']['lines']['money']
                );
                
                // Might as well do this here
                $totalChars += strlen($rotation.$name.$lines[0].$lines[1].$lines[2]);
                
                // Team name stuff
                $text .= $rotation;
                for($s = 0; $s < 4 - strlen($rotation); $s++) {
                    $text .= " ";
                    $totalChars++;
                }
                $text .= strtoupper($name);
                for($s = 0; $s < ($this->chars[0] - (strlen($name) + 4)); $s++) {
                    $text .= " ";
                    $totalChars++;
                }
                
                // Known Lines
                for($i = 0; $i < count($lines); $i++) {
                    $text .= $lines[$i];
                    for($j = 0; $j < $this->chars[$i+1] - strlen($lines[$i]); $j++) {
                        $text .= " ";
                        $totalChars++;
                    }
                }
                
                // Stuff that may not exist...
            
                // Halftime lines
                $line = "";
                if(array_key_exists('ht', $game['host']['lines'])) {
                    $line = trim($game['host']['lines']['ht']);
                    $sign = substr($line,0,1);

                    if($sign !== '-') {
                        $line = $game['ht']['total'];

                    }

                    $text .= $line; 
                    $totalChars += strlen($line);
                }
                for($s = 0; $s < $this->chars[4] - (strlen($line)); $s++) {
                    $text .= " ";
                    $totalChars++;
                }

                // Scores
                $htScore    = "";
                $finalScore = "";
                if(array_key_exists('scores', $game['host'])) {
                    if(array_key_exists('ht', $game['host']['scores'])) {
                        $htScore = $game['host']['scores']['ht'];
                    }
                    if(array_key_exists('first', $game['host']['scores'])) {
                        $finalScore .= $game['host']['scores']['first'];
                    }
                    if(array_key_exists('progress', $game['visitor']['scores'])) {
                        $finalScore .= " ".$game['host']['scores']['progress'];
                    }
                }
                $text .= $htScore; 
                $totalChars += strlen($htScore);
                for($s = 0; $s < $this->chars[5] - (strlen($htScore)); $s++) {
                    $text .= " ";
                    $totalChars++;
                }
                $text .= $finalScore; 
                $totalChars += strlen($finalScore);

                $text .= "\r";
                $totalLines++;
                
                // And add network
                $text .= $game['network'];
                $totalChars += strlen($game['network']);
                
                if(isset($this->data[$dayloop]['games'][$gameloop+1])) {
                    if((($totalChars + $maxPerGame) > $this->maxChars) || ($totalLines + 3 > $this->maxLines)) {
                        $totalLines++;
                        $text .= "\r";
                        //$text .= "EMAIL STATS: Characters = ".$totalChars." | Lines = ".$totalLines;
                        //$text .= "\r\r";
                        $text .= "##### NEW EMAIL #####"."\r";
                        //$text .= "\r\r";
                        
                        $totalChars = 0;
                        $totalLines = 0;
                        $emailNum++;
                    } else {
                        $text .= "\r\r";
                        $totalLines += 2;
                    }
                }
                
                $gameloop++;
                
            }
            
            $emails[$emailNum] = $totalChars;
            // See if should end email at this day
            if(isset($this->data[$dayloop+1])) {
                if((($totalChars + $maxPerDay) > $this->maxChars) || ($totalLines + 5 > $this->maxLines)) {
                    $totalLines++;
                    $text .= "\r";
                    //$text .= "EMAIL STATS: Characters = ".$totalChars." | Lines = ".$totalLines;
                    //$text .= "\r\r";
                    $text .= "##### NEW EMAIL #####"."\r";
                    //$text .= "\r\r";

                    $totalChars = 0;
                    $totalLines = 0;
                    $emailNum++;

                } else {
                    $text .= "\r\r";
                    $totalLines += 2;
                }
            }
            
            $dayloop++;
            
        }
        
        // Finish it up
        $text .= "\r";
        //$text .= "EMAIL STATS: Characters = ".$totalChars." | Lines = ".$totalLines;
        //$text .= "\r\r";
        //$text .= "TOTAL EMAILS = ".count($emails);
        //$text .= "\r\r";
        $text .= "##### END #####";
        //$text .= "\r\r";
        
        return $text;
        
    }
}
