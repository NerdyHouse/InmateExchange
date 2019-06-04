<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use Goutte\Client as CrawlerClient;

use App\Library;
use App\Library\ScoresOddsScraper;

class SportsDataController extends Controller
{   
    /*
     *  Schedule
     * ------------
     */
    // Gets data from scores and odds and assembles a fairly easy to work with array
    public function scrapeScheduleScoresOddsCom() {
        
        // Try to connect
        try {
            $client = new CrawlerClient();
            $guzzleClient = new GuzzleClient(array(
                'timeout' => 60,
            ));
            $client->setClient($guzzleClient);
        
            $crawler = $client->request('GET', 'http://www.scoresandodds.coms/');
        } catch(\Exception $e) {
            return response('Crawler Failed',400);
        }
        
        // Easier to work with arrays
        $dataArr    = array();
        $times      = array();
        $visitors   = array();
        $hosts      = array();
        $vrots      = array();
        $hrots      = array();
        $vlines     = array();
        $hlines     = array();
        $networks   = array();
        
        /*
         * Extract data
         */
        // Game times
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.time')->each(function($node) use(&$times) {
                $times[] = $node->text();
        });
        
        // Visitor names w rotation (need to extract rotation num from text)
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.odd > td.name')->each(function($node) use(&$visitors,&$vrots) {
                $vrots[]    = substr($node->text(), 0, 3);
                $visitors[] = substr($node->text(), 4);
        });
        
        // Host names w rotation (need to extract rotation num from text)
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.even > td.name')->each(function($node) use(&$hosts,&$hrots) {
                $hrots[]    = substr($node->text(), 0, 3);
                $hosts[]    = substr($node->text(), 4);
        });
        
        // Network note / info
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.note > td')->each(function($node) use(&$networks) {
                $networks[] = $node->text();
        });
        
        /*
         * Lines
         */
        
        // Visitor open line
        $volinespointer = 0;
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.odd > td.name + td.line')->each(function($node) use(&$vlines,&$volinespointer) {
                $vlines[$volinespointer]['open'] = $node->text();
                $volinespointer++;
        });
        // Visitor current line
        $vclinespointer = 0;
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.odd > td.currentline')->each(function($node) use(&$vlines,&$vclinespointer) {
                $vlines[$vclinespointer]['current'] = $node->text();
                $vclinespointer++;
        });
        // Visitor money line
        $vmlinespointer = 0;
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.odd > td.currentline + td.line')->each(function($node) use(&$vlines,&$vmlinespointer) {
                $vlines[$vmlinespointer]['money'] = $node->text();
                $vmlinespointer++;
        });
        
        // host open line
        $holinespointer = 0;
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.even > td.name + td.line')->each(function($node) use(&$hlines,&$holinespointer) {
                $hlines[$holinespointer]['open'] = $node->text();
                $holinespointer++;
        });
        // host current line
        $hclinespointer = 0;
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.even > td.currentline')->each(function($node) use(&$hlines,&$hclinespointer) {
                $hlines[$hclinespointer]['current'] = $node->text();
                $hclinespointer++;
        });
        // host money line
        $hmlinespointer = 0;
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.even > td.currentline + td.line')->each(function($node) use(&$hlines,&$hmlinespointer) {
                $hlines[$hmlinespointer]['money'] = $node->text();
                $hmlinespointer++;
        });
        
        // Insert into return array
        for($i = 0; $i < count($times); $i++) {
            $dataArr['nfl'][$i]['time']             = $times[$i];
            $dataArr['nfl'][$i]['visitor']['rot']   = $vrots[$i];
            $dataArr['nfl'][$i]['visitor']['team']  = $visitors[$i];
            $dataArr['nfl'][$i]['visitor']['lines'] = $vlines[$i];
            $dataArr['nfl'][$i]['host']['rot']      = $hrots[$i];
            $dataArr['nfl'][$i]['host']['team']     = $hosts[$i];
            $dataArr['nfl'][$i]['host']['lines']    = $hlines[$i];
            $dataArr['nfl'][$i]['network']          = $networks[$i];
        }
        
        // Return the response
        if(array_key_exists('nfl', $dataArr)) {
            return response($dataArr,200);
        } else {
            return response('No NFL Data',204);
        }
    }
    
    public function getHTLines() {
        
        // Gotta get halftime lines
        try {
            $client         = new CrawlerClient();
            $guzzleClient   = new GuzzleClient(array(
                'timeout' => 60,
            ));
            $client->setClient($guzzleClient);

            // Find the link and get URI since it has a date parameter
            $crawler        = $client->request('GET', 'http://props.scoresandodds.com/index.cfm');
            $link           = $crawler->filter('a:contains("NFL First Half Lines")',0)->link();
            
            $uri            = $link->getUri();
        
            // Then get that page as well
            $crawler        = $client->request('GET', $uri);
        } catch(\Exception $e) {
            return response('Crawler Failed',400);
        }
        
        // Get the game # and set as Arr Key
        $htLines = array();
        $crawler->filter('td#contentMain > div.gameSection > div.rightShadow > div.leftShadow > table.data > tbody > tr.team > td.player')->each(function($node) use(&$htLines) {
                $htLines[] = array('game' => $node->text());
        });
        
        // Get lines
        $htLpointer = 0;
        $crawler->filter('td#contentMain > div.gameSection > div.rightShadow > div.leftShadow > table.data > tbody > tr.team > td.player + td + td + td > a')->each(function($node) use(&$htLines, &$htLpointer) {
                $lineHTML   = $node->html();
                $lines      = explode('<br>', $lineHTML);
                
                $visL       = str_replace(array('<b>','</b>'),'',$lines[0]);
                $hostL      = str_replace(array('<b>','</b>'),'',$lines[1]);
                
                $visLArr    = explode('/',$visL);
                $hostLArr   = explode('/',$hostL);
                
                $htLines[$htLpointer]['lines']['visitor'] = $visLArr[0];
                $htLines[$htLpointer]['lines']['host']    = $hostLArr[0];
                
                $htLpointer++;
        });
        
        // Get totals
        $htTpointer = 0;
        $crawler->filter('td#contentMain > div.gameSection > div.rightShadow > div.leftShadow > table.data > tbody > tr.team > td.player + td + td + td + td > a')->each(function($node) use(&$htLines, &$htTpointer) {
                $totalTxt   = $node->text();
                $clTotal    = str_replace(array('Over','Under',' '),"",trim($totalTxt));
                
                $totalArr   = explode('/',$clTotal);
                
                $htLines[$htTpointer]['total'] = $totalArr[0];
                
                $htTpointer++;
        });
        
        return $htLines;
    }
    
    // Get the scores
    public function getScores() {
        
        $client = new CrawlerClient();
        $guzzleClient = new GuzzleClient(array(
            'timeout' => 60,
        ));
        $client->setClient($guzzleClient);
        
        $crawler = $client->request('GET', 'http://www.scoresandodds.com/');
        
        $scores = array();
        
        // Visitor rotation for syncing data
        $rotpointer = 0;
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.odd > td.name')->each(function($node) use(&$scores,&$rotpointer) {
                $scores[] = array('game' => substr($node->text(), 0, 3));
                $rotpointer++;
        });
        
        // Visitor scores
        $vspointer = 0;
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.odd > td.score')->each(function($node) use(&$scores,&$vspointer) {
            
                $score = $node->text();
            
                $scores[$vspointer]['visitor'] = str_replace(array('FREE','HOT PICK','LOCK','PICK'),'', $score);
                $vspointer++;
        });
        
        // Host scores
        $hspointer = 0;
        $crawler->filter('div.header#nfl + div.rightShadow > div.leftShadow > table.data > tbody > tr.team.even > td.score')->each(function($node) use(&$scores,&$hspointer) {
                
                $score = $node->text();
            
                $scores[$hspointer]['host'] = str_replace(array('FREE','HOT PICK','LOCK','PICK'),'', $score);
                $hspointer++;
        });
        
        return $scores;
    }
    
    // Simply outputs raw NFL data array
    public function rawData() {
        $nflData = $this->scrapeScheduleScoresOddsCom();
        
        return view('nfl/data',['data' => $nflData]);
    }
    
    // Output an "email" formatted spread
    public function emailFormatData() {
        
        $dataCollector  = new ScoresOddsScraper('nba');
        $data           = $dataCollector->scrape_homepage();
        
        dd($data);
        
        // Try to get base data
        $baseData = $this->scrapeScheduleScoresOddsCom();
        $bdStatus = $baseData->getStatusCode();
        
        // Handle any errors
        if($bdStatus !== 200) {
            switch ($bdStatus) {
                case 204:
                    return response('No data returned')->withHeaders(['Content-Type' => 'plain-text']);
                    break;
                case 400:
                    return response('The Crawler Failed')->withHeaders(['Content-Type' => 'plain-text']);
                    break;
                default;
            }
        }
        
        $hts    = $this->getHTLines();
        $scores = $this->getScores();
        
        /*
         * Craziness begins
         */
        $fColChars  = 22;
        $sColChars  = 9;
        $tColChars  = 10;
        $frColChars = 10;
        $fvColChars = 12;
        $sxColChars = 15;
        
        $text  = $data['nfl'][0]['time'];
        for($s = 0; $s < $fColChars - strlen($data['nfl'][0]['time']); $s++) {
            $text .= " ";
        }
        
        $text .= 'open     ';
        $text .= 'current   ';
        $text .= 'MLine     ';
        $text .= '(1st H)     ';
        $text .= 'Scores';
        $text .= "\r";
        
        // Add game data stuff to the text
        $loop           = 0;
        $lines          = 1;
        $charsPerLine   = $fColChars + $sColChars + $tColChars + $frColChars + $fvColChars + $sxColChars;
        $totalChars     = $charsPerLine;
        foreach ($data['nfl'] as $game) {
            
            if($loop > 0) {
                $text .= $game['time']."\r";
                $lines++;
                $totalChars += $charsPerLine;
            }
            
            $text .= $game['visitor']['rot']." ".$game['visitor']['team'];
            for($s = 0; $s < $fColChars - (strlen($game['visitor']['team']) + 4); $s++) {
                $text .= " ";
            }
            
            $text .= $game['visitor']['lines']['open'];
            for($s = 0; $s < $sColChars - (strlen($game['visitor']['lines']['open'])); $s++) {
                $text .= " ";
            }
            
            $text .= $game['visitor']['lines']['current'];
            for($s = 0; $s < $tColChars - (strlen($game['visitor']['lines']['current'])); $s++) {
                $text .= " ";
            }
            
            $text .= $game['visitor']['lines']['money'];
            for($s = 0; $s < $frColChars - (strlen($game['visitor']['lines']['money'])); $s++) {
                $text .= " ";
            }
            
            // Halftimes
            if ($hts) {
                foreach($hts as $ht) {
                    if(!empty($ht['lines'])) {
                        if(intval($game['visitor']['rot']) == intval($ht['game'])) {

                            $line = trim($ht['lines']['visitor']);
                            $sign = substr($line,0,1);

                            if($sign == '-') {
                                $text .= $line; 
                                for($s = 0; $s < $fvColChars - (strlen($line)); $s++) {
                                    $text .= " ";
                                }
                            } else {
                                $text .= $ht['total'];
                                for($s = 0; $s < $fvColChars - (strlen($ht['total'])); $s++) {
                                    $text .= " ";
                                }
                            }
                        }
                    }
                }
            }
            
            // Scores
            foreach ($scores as $score) {
                if(intval($game['visitor']['rot']) == intval($score['game'])) {
                    $text .= $score['visitor'];
                }
            }
            
            $text .= "\r";
            $lines++;
            $totalChars += $charsPerLine;
            
            $text .= $game['host']['rot']." ".$game['host']['team'];
            for($s = 0; $s < $fColChars - (strlen($game['host']['team']) + 4); $s++) {
                $text .= " ";
            }
            
            $text .= $game['host']['lines']['open'];
            for($s = 0; $s < $sColChars - (strlen($game['host']['lines']['open'])); $s++) {
                $text .= " ";
            }
            
            $text .= $game['host']['lines']['current'];
            for($s = 0; $s < $tColChars - (strlen($game['host']['lines']['current'])); $s++) {
                $text .= " ";
            }
            
            $text .= $game['host']['lines']['money'];
            for($s = 0; $s < $frColChars - (strlen($game['host']['lines']['money'])); $s++) {
                $text .= " ";
            }
            
            // Halftimes
            if($hts) {
                foreach($hts as $ht) {
                    if(!empty($ht['lines'])) {
                        if(intval($game['visitor']['rot']) == intval($ht['game'])) {

                            $line = trim($ht['lines']['host']);
                            $sign = substr($line,0,1);

                            if($sign == '-') {
                                $text .= $line; 
                                for($s = 0; $s < $fvColChars - (strlen($line)); $s++) {
                                    $text .= " ";
                                }
                            } else {
                                $text .= $ht['total'];
                                for($s = 0; $s < $fvColChars - (strlen($ht['total'])); $s++) {
                                    $text .= " ";
                                }
                            }
                        }
                    }
                }
            }
            
            // Scores
            foreach ($scores as $score) {
                if(intval($game['visitor']['rot']) == intval($score['game'])) {
                    $text .= $score['host'];
                }
            }
            
            $text .= "\r";
            $lines++;
            $totalChars += $charsPerLine;
            
            $text .= $game['network'];
            $totalChars += strlen($game['network']);
            $text .= "\r\r";
            $lines += 2;
            
            $loop++;
        }
        $text .= 'lines: '.$lines.' | Characters: '.$totalChars;
        
        return response($text)->withHeaders(['Content-Type' => 'plain-text']);
    }
}
