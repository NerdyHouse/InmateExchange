<?php
/**
 * Description of ScoresOddsScraper
 *
 * @author jacobreadinger
 */

namespace App\Library;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use Goutte\Client as CrawlerClient;

use Illuminate\Support\Facades\Log;

use App\Library\Utilities;

class ScoresOddsScraper {
    
    public $_sport;
    
    public function __construct($sport = 'all') {
        $this->_sport = $sport;
    }
    
    // Scrape homepage stats for sport
    public function crawler_connect($url) {
        
        // Try to connect
        try {
            
            $client = new CrawlerClient();
            $guzzleClient = new GuzzleClient(array(
                'timeout' => 30,
            ));
            $client->setClient($guzzleClient);
        
            $crawler = $client->request('GET',$url);
            
        } catch(\Exception $e) {
            // Log and return response
            Log::error('Crawler failed to connect: '.$url.' : '.$e);
            return false;
        }
        
        // Return the crawler if we've connected
        return $crawler;
    }
    
    // Scrape homepage stats for sport
    public function crawler_connect_and_click($url,$link) {
        
        // Try to connect
        try {
            
            $client = new CrawlerClient();
            $guzzleClient = new GuzzleClient(array(
                'timeout' => 30,
            ));
            $client->setClient($guzzleClient);
        
            $crawler = $client->request('GET',$url);
            $link           = $crawler->filter('a:contains("'.$link.'")',0)->link();
            $uri            = $link->getUri();
            $crawler        = $client->request('GET', $uri);
            
        } catch(\Exception $e) {
            // Log and return response
            Log::error('Crawler failed to connect: '.$url.' : '.$e);
            return false;
        }
        
        // Return the crawler if we've connected
        return $crawler;
    }
    
    /***************
     * Scrape homepage for games and stats
     */
    public function scrape_base_data($url = 'http://www.scoresandodds.com/') {
        
        // Attempt connection
        $crawler = $this->crawler_connect($url);
        if(!$crawler) {
            return false;
        }
        
        $dataArr    = array();
        
        $date = date('m/d/y');
        $crawler->filter('div.header#'.$this->_sport.' > div.date')->each(function($node) use(&$date) {
            $rawDate = trim($node->text());
            $date = date('m/d/y', strtotime($rawDate));
        });
        
        /*
         * Extract data
         */
        $crawler->filter('div.header#'.$this->_sport.' + div.rightShadow > div.leftShadow > table.data > tbody')->each(function($node) use(&$dataArr, &$date) {
            
            // Game times
            $node->filter('tr.time')->each(function($node) use(&$dataArr,&$date) {
                $dataArr[] = array('time' => trim($node->text()),'date' => $date);
            });
            
            // Get network
            $netpointer = 0;
            $node->filter('tr.note > td')->each(function($node) use(&$dataArr,&$netpointer) {
                $dataArr[$netpointer]['network'] = trim($node->text());
                $netpointer++;
            });
            
            // Team names and rotations
            $vispointer = 0;
            $node->filter('tr.team.odd > td.name')->each(function($node) use(&$dataArr,&$vispointer) {
                $text       = trim($node->text());
                $breakpoint = strpos($text, ' ');
                $rot        = substr($text,0,$breakpoint);
                $name       = substr($text,$breakpoint);
                $dataArr[$vispointer]['visitor']['rotation']    = trim($rot);
                $dataArr[$vispointer]['visitor']['name']        = trim($name);
                $vispointer++;
            });
            
            $hostpointer = 0;
            $node->filter('tr.team.even > td.name')->each(function($node) use(&$dataArr,&$hostpointer) {
                $text       = trim($node->text());
                $breakpoint = strpos($text, ' ');
                $rot        = substr($text,0,$breakpoint);
                $name       = substr($text,$breakpoint);
                $dataArr[$hostpointer]['host']['rotation']    = trim($rot);
                $dataArr[$hostpointer]['host']['name']        = trim($name);
                $hostpointer++;
            });
            
            // Open Lines
            $olinespointer = 0;
            $node->filter('tr.team.odd > td.name + td.line')->each(function($node) use(&$dataArr,&$olinespointer) {
                $dataArr[$olinespointer]['visitor']['lines']['open'] = trim($node->text());
                $olinespointer++;
            });
            $olinespointer = 0;
            $node->filter('tr.team.even > td.name + td.line')->each(function($node) use(&$dataArr,&$olinespointer) {
                $dataArr[$olinespointer]['host']['lines']['open'] = trim($node->text());
                $olinespointer++;
            });
            
            // Current Lines
            $clinespointer = 0;
            $node->filter('tr.team.odd > td.currentline')->each(function($node) use(&$dataArr,&$clinespointer) {
                    $dataArr[$clinespointer]['visitor']['lines']['current'] = trim($node->text());
                    $clinespointer++;
            });
            $clinespointer = 0;
            $node->filter('tr.team.even > td.currentline')->each(function($node) use(&$dataArr,&$clinespointer) {
                    $dataArr[$clinespointer]['host']['lines']['current'] = trim($node->text());
                    $clinespointer++;
            });
            
            // Money Lines / Pucklines
            // Puckline is saved as money for easy universal use
            $mlinespointer = 0;
            $node->filter('tr.team.odd > td.currentline + td.line')->each(function($node) use(&$dataArr,&$mlinespointer) {
                    $dataArr[$mlinespointer]['visitor']['lines']['money'] = trim($node->text());
                    $mlinespointer++;
            });
            $mlinespointer = 0;
            $node->filter('tr.team.even > td.currentline + td.line')->each(function($node) use(&$dataArr,&$mlinespointer) {
                    $dataArr[$mlinespointer]['host']['lines']['money'] = trim($node->text());
                    $mlinespointer++;
            });
        });
        
        // Return the response
        //return $dataArr;
        if(!empty($dataArr)) {
            return $dataArr;
        } else {
            false;
        }
    }
    
    /***************
     * Scrape for halftime lines
     */
    public function scrape_halftime($section = false) {
        // Attempt connection
        
        if(!$section) {
            $url = 'http://props.scoresandodds.com/index.cfm';
            $link = Utilities::halftime_link($this->_sport);
            if(!$link) {
                return false;
            }

            $crawler = $this->crawler_connect_and_click($url,$link);
            if(!$crawler) {
                return false;
            }
        } else {
            $url = 'http://props.scoresandodds.com/section_display.cfm?section_id='.$section;
            $crawler = $this->crawler_connect($url);
            if(!$crawler) {
                return false;
            }
        }
        
        // Get the game # and set as Arr Key
        $htLines = array();
        $crawler->filter('td#contentMain > div.gameSection > div.rightShadow > div.leftShadow > table.data > tbody > tr.team > td.player')->each(function($node) use(&$htLines) {
                $htLines[] = array('game' => trim($node->text()));
        });
        
        // Gotta get dates as another comparison parameter
        $datepointer = 0;
        $crawler->filter('td#contentMain > div.gameSection > div.rightShadow > div.leftShadow > table.data > tbody > tr.team > td.name')->each(function($node) use(&$htLines, &$datepointer) {
                $dateHTML   = $node->html();
                $dateParts  = explode('<br>', $dateHTML);
                
                $date       = str_replace(array('<b>','</b>'),'',$dateParts[0]);
                $time       = str_replace(array('<b>','</b>'),'',$dateParts[1]);
                
                $htLines[$datepointer]['date'] = trim($date);
                $htLines[$datepointer]['time'] = trim($time);
                
                $datepointer++;
        });
        
        // Team names
        $teampointer = 0;
        $crawler->filter('td#contentMain > div.gameSection > div.rightShadow > div.leftShadow > table.data > tbody > tr.team > td.player + td')->each(function($node) use(&$htLines, &$teampointer) {
                $teamHTML   = $node->html();
                $teams      = explode('<br>', $teamHTML);
                
                $visN       = str_replace(array('<b>','</b>'),'',$teams[0]);
                $hostN      = str_replace(array('<b>','</b>'),'',$teams[1]);
                
                $htLines[$teampointer]['teams']['visitor'] = trim($visN);
                $htLines[$teampointer]['teams']['host']    = trim($hostN);
                
                $teampointer++;
        });
        
        // Get lines
        $htLpointer = 0;
        $crawler->filter('td#contentMain > div.gameSection > div.rightShadow > div.leftShadow > table.data > tbody > tr.team > td.player + td + td + td')->each(function($node) use(&$htLines, &$htLpointer) {
                $lineHTML   = $node->html();
                $lines      = explode('<br>', $lineHTML);
                
                //$visL       = str_replace(array('<b>','</b>'),'',$lines[0]);
                //$hostL      = str_replace(array('<b>','</b>'),'',$lines[1]);
                
                $visL       = isset($lines[0]) ? trim(strip_tags($lines[0])) : "";
                $hostL      = isset($lines[1]) ? trim(strip_tags($lines[1])) : "";
                
                $visLArr    = explode('/',$visL);
                $hostLArr   = explode('/',$hostL);
                
                $htLines[$htLpointer]['lines']['visitor'] = isset($visLArr[0]) ? trim($visLArr[0]) : "";
                $htLines[$htLpointer]['lines']['host']    = isset($hostLArr[0]) ? trim($hostLArr[0]) : "";
                
                $htLpointer++;
        });
        
        // Get totals
        $htTpointer = 0;
        $crawler->filter('td#contentMain > div.gameSection > div.rightShadow > div.leftShadow > table.data > tbody > tr.team > td.player + td + td + td + td')->each(function($node) use(&$htLines, &$htTpointer) {
                $totalTxt   = $node->text();
                
                if(trim($totalTxt) == 'OFF') {
                    $htLines[$htTpointer]['total'] = "";
                } else {
                    $clTotal    = str_replace(array('Over','Under',' '),"",trim($totalTxt));
                    $totalArr   = explode('/',$clTotal);
                    $htLines[$htTpointer]['total'] = trim($totalArr[0]);
                }
                
                $htTpointer++;
        });
        
        // Get HT Score
        $scpointer = 0;
        $crawler->filter('td#contentMain > div.gameSection > div.rightShadow > div.leftShadow > table.data > tbody > tr.team > td.player + td + td + td + td + td + td')->each(function($node) use(&$htLines, &$scpointer) {
                
                $scHTML   = $node->html();
                $scs      = explode('<br>', $scHTML);
                
                if(is_array($scs) && count($scs) > 1) {
                    $visSc    = trim(str_replace(array('<b>','</b>'),'',$scs[0]));
                    $hostSc   = trim(str_replace(array('<b>','</b>'),'',$scs[1]));
                } else {
                    $visSc  = "";
                    $hostSc = "";
                }
                
                $htLines[$scpointer]['scores']['visitor'] = trim($visSc);
                $htLines[$scpointer]['scores']['host']    = trim($hostSc);
                
                $scpointer++;
        });
        
        if(!empty($htLines)) {
            return $htLines;
        } else {
            return false;
        }
    }
    
    /***************
     * Scrape for scores
     */
    public function scrape_scores($url = 'http://www.scoresandodds.com/') {
        
        // Attempt connection
        $crawler = $this->crawler_connect($url);
        if(!$crawler) {
            return false;
        }
        
        $scores = array();
        
        // Visitor rotation for syncing data
        $rotpointer = 0;
        $crawler->filter('div.header#'.$this->_sport.' + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.odd > td.name')->each(function($node) use(&$scores,&$rotpointer) {
                $scores[] = array('game' => substr($node->text(), 0, 3));
                $rotpointer++;
        });
        
        // Visitor scores
        $vspointer = 0;
        $crawler->filter('div.header#'.$this->_sport.' + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.odd > td.score > span.first')->each(function($node) use(&$scores,&$vspointer) {
            
                $score = $node->text();
            
                $scores[$vspointer]['visitor']['first'] = str_replace(array('FREE','HOT PICK','LOCK','PICK','HUGE','PARLAY','POWER',chr(194)),'', $score);
                $vspointer++;
        });
        $vspointer2 = 0;
        $crawler->filter('div.header#'.$this->_sport.' + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.odd > td.score > span.progress')->each(function($node) use(&$scores,&$vspointer2) {
            
                $score = $node->text();
            
                $scores[$vspointer2]['visitor']['progress'] = str_replace(array('FREE','HOT PICK','LOCK','PICK','HUGE','PARLAY','POWER',chr(194)),'', $score);
                $vspointer2++;
        });
        
        // Host scores
        $hspointer = 0;
        $crawler->filter('div.header#'.$this->_sport.' + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.even > td.score > span.first')->each(function($node) use(&$scores,&$hspointer) {
                
                $score = $node->text();
            
                $scores[$hspointer]['host']['first'] = str_replace(array('FREE','HOT PICK','LOCK','PICK','HUGE','PARLAY','POWER',chr(194)),'', $score);
                $hspointer++;
        });
        $hspointer2 = 0;
        $crawler->filter('div.header#'.$this->_sport.' + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.even > td.score > span.progress')->each(function($node) use(&$scores,&$hspointer2) {
                
                $score = $node->text();
            
                $scores[$hspointer2]['host']['progress'] = str_replace(array('FREE','HOT PICK','LOCK','PICK','HUGE','PARLAY','POWER',chr(194)),'', $score);
                $hspointer2++;
        });
        
        if(!empty($scores)) {
            return $scores;
        } else {
            return false;
        }
    }
    
    /*******************
     * Scrape all and combine
     */
    public function scrape_all($type = 'daily') {
        
        // Get data
        switch($type) {
            case 'daily':
                $games      = $this->scrape_base_data();
                $htLines    = $this->scrape_halftime(Utilities::halftime_section($this->_sport));
                //$htLines    = $this->scrape_halftime();
                $scores     = $this->scrape_scores();
                break;
            case 'overnight':
                $games      = $this->scrape_base_data('http://www.scoresandodds.com/tomorrow.html');
                $htLines    = $this->scrape_halftime(Utilities::halftime_section($this->_sport));
                //$htLines    = $this->scrape_halftime();
                $scores     = $this->scrape_scores('http://www.scoresandodds.com/tomorrow.html');
                break;
            case 'yesterday':
                $games      = $this->scrape_base_data('http://www.scoresandodds.com/yesterday.html');
                $htLines    = $this->scrape_halftime(Utilities::halftime_section($this->_sport));
                //$htLines    = $this->scrape_halftime();
                $scores     = $this->scrape_scores('http://www.scoresandodds.com/yesterday.html');
                break;
            default:
                $games      = $this->scrape_homepage();
                $htLines    = $this->scrape_halftime();
                $scores     = $this->scrape_scores();
        }
        
        if($games) {
            
            try {
                // Loop through all games and add the stuff
                // Halftime lines and scores
                if($htLines) {
                    for($i = 0; $i < count($games); $i++) {
                        foreach ($htLines as $line) {
                            if(intval($line['game']) == intval($games[$i]['visitor']['rotation'])) {
                            if($line['date'] === $games[$i]['date']) {
                                $games[$i]['visitor']['lines']['ht'] = $line['lines']['visitor'];
                                $games[$i]['visitor']['scores']['ht'] = $line['scores']['visitor'];

                                $games[$i]['host']['lines']['ht'] = $line['lines']['host'];
                                $games[$i]['host']['scores']['ht'] = $line['scores']['host'];

                                $games[$i]['ht']['total'] = isset($line['total']) ? $line['total'] : "";

                            }}
                        }
                    }
                }

                // Final Scores
                if($scores) {
                    for($i = 0; $i < count($scores); $i++) {
                        foreach ($scores as $score) {
                            if(intval($score['game']) == intval($games[$i]['visitor']['rotation'])) {
                                $games[$i]['visitor']['scores']['first'] = $score['visitor']['first'];
                                $games[$i]['visitor']['scores']['progress'] = $score['visitor']['progress'];

                                $games[$i]['host']['scores']['first'] = $score['host']['first'];
                                $games[$i]['host']['scores']['progress'] = $score['host']['progress'];
                            }
                        }
                    }
                }
                return $games;
            } catch(\Exception $e) {
                Log::error('Error parsing halftime data and scores. Details: '.$e);
                return $games;
            }
            
        } else {
            return false;
        }
    }
}
