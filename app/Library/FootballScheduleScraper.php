<?php

namespace App\Library;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use Goutte\Client as CrawlerClient;

use Illuminate\Support\Facades\Log;

use App\Library\Utilities;

/**
 * Description of FootballScheduleScraper
 *
 * @author jacobreadinger
 */
class FootballScheduleScraper {
    
    // Get all games on page for this week
    public function get_schedule($url) {
        
        // Attempt connection
        $connect = new ScoresOddsScraper();
        $crawler = $connect->crawler_connect($url);
        if(!$crawler) {
            return false;
        }
        
        $scheduleArr = array();
        
        // Get the title / header
        $crawler->filter('div.tableHeader')->each(function($node) use(&$scheduleArr) {
                $text       = trim($node->text());
                
                /* $breakpoint = strpos($converted, '&nbsp;');
                $date       = date('m/d/y',strtotime(substr($converted,0,$breakpoint)));
                $name       = substr($converted,$breakpoint); */
                
                $date       = date('m/d/y',strtotime(substr($text,0,10)));
                $name       = substr($text,-3);
            
                $scheduleArr[] = array('full' => $text, 'title' => $name,'date' => $date);
        });
        
        // Getting game data
        $scpointer = 0;
        $crawler->filter('table.data > tbody')->each(function($node) use(&$scheduleArr,&$scpointer) {
                
                $dataArr = array();
                
                // Get times
                $node->filter('tr.time > td')->each(function($node) use(&$dataArr) {
                    $dataArr[]['time'] = trim($node->text());
                });
                
                // Get network
                $netpointer = 0;
                $node->filter('tr.note > td')->each(function($node) use(&$dataArr,&$netpointer) {
                    $dataArr[$netpointer]['network'] = trim($node->text());
                    $netpointer++;
                });
                
                // Vis name / rot
                $vispointer = 0;
                $node->filter('tr.team.odd > td.name')->each(function($node) use(&$dataArr, &$vispointer) {
                    $text       = trim($node->text());
                    $breakpoint = strpos($text, ' ');
                    $rot        = substr($text,0,$breakpoint);
                    $name       = substr($text,$breakpoint);
                    $dataArr[$vispointer]['visitor']['name'] = trim($name);
                    $dataArr[$vispointer]['visitor']['rotation']  = trim($rot);
                    $vispointer++;
                });
                
                // Vis open
                $visopointer = 0;
                $node->filter('tr.team.odd > td.name + td.line')->each(function($node) use(&$dataArr, &$visopointer) {
                    $dataArr[$visopointer]['visitor']['lines']['open'] = trim($node->text());
                    $visopointer++;
                });
                
                // Vis current
                $viscpointer = 0;
                $node->filter('tr.team.odd > td.currentline')->each(function($node) use(&$dataArr, &$viscpointer) {
                    $dataArr[$viscpointer]['visitor']['lines']['current'] = trim($node->text());
                    $viscpointer++;
                });
                
                // Vis money
                $vismpointer = 0;
                $node->filter('tr.team.odd > td.currentline + td.line')->each(function($node) use(&$dataArr, &$vismpointer) {
                    $dataArr[$vismpointer]['visitor']['lines']['money'] = trim($node->text());
                    $vismpointer++;
                });
                
                // Visitor scores
                /*
                $vspointer = 0;
                $node->filter('tr.team.odd > td.finalscore')->each(function($node) use(&$dataArr,&$vspointer) {
                        $score = trim($node->text());
                        $dataArr[$vspointer]['visitor']['scores']['first'] = str_replace(array('FREE','HOT PICK','LOCK','PICK','HUGE','PARLAY',chr(194)),'', $score);
                        $vspointer++;
                });
                 * 
                 */
                $vspointer = 0;
                $node->filter('tr.team.odd > td.score')->each(function($node) use(&$dataArr,&$vspointer) {
                        $score = trim($node->text());
                        $dataArr[$vspointer]['visitor']['scores']['first'] = str_replace(array('FREE','HOT PICK','LOCK','PICK','HUGE','PARLAY',chr(194)),'', $score);
                        $vspointer++;
                });
                
                // Host name / rot
                $hostpointer = 0;
                $node->filter('tr.team.even > td.name')->each(function($node) use(&$dataArr, &$hostpointer) {
                    $text       = trim($node->text());
                    $breakpoint = strpos($text, ' ');
                    $rot        = substr($text,0,$breakpoint);
                    $name       = substr($text,$breakpoint);
                    $dataArr[$hostpointer]['host']['name'] = trim($name);
                    $dataArr[$hostpointer]['host']['rotation']  = trim($rot);
                    $hostpointer++;
                });
                
                // Vis open
                $hostopointer = 0;
                $node->filter('tr.team.even > td.name + td.line')->each(function($node) use(&$dataArr, &$hostopointer) {
                    $dataArr[$hostopointer]['host']['lines']['open'] = trim($node->text());
                    $hostopointer++;
                });
                
                // Vis current
                $hostcpointer = 0;
                $node->filter('tr.team.even > td.currentline')->each(function($node) use(&$dataArr, &$hostcpointer) {
                    $dataArr[$hostcpointer]['host']['lines']['current'] = trim($node->text());
                    $hostcpointer++;
                });
                
                // Vis money
                $hostmpointer = 0;
                $node->filter('tr.team.even > td.currentline + td.line')->each(function($node) use(&$dataArr, &$hostmpointer) {
                    $dataArr[$hostmpointer]['host']['lines']['money'] = trim($node->text());
                    $hostmpointer++;
                });
                
                // Host scores
                /*
                $hspointer = 0;
                $node->filter('tr.team.even > td.finalscore')->each(function($node) use(&$dataArr,&$hspointer) {
                        $score = trim($node->text());
                        $dataArr[$hspointer]['host']['scores']['first'] = str_replace(array('FREE','HOT PICK','LOCK','PICK','HUGE','PARLAY',chr(194)),'', $score);
                        $hspointer++;
                });
                 * 
                 */
                $hspointer = 0;
                $node->filter('tr.team.even > td.score')->each(function($node) use(&$dataArr,&$hspointer) {
                        $score = trim($node->text());
                        $dataArr[$hspointer]['host']['scores']['first'] = str_replace(array('FREE','HOT PICK','LOCK','PICK','HUGE','PARLAY',chr(194)),'', $score);
                        $hspointer++;
                });
            
                $scheduleArr[$scpointer]['games'] = $dataArr;
                $scpointer++;
        });
        
        return $scheduleArr;
        
    }
    
    // Get all this week
    public function get_sport_schedule($sport = 'all',$week = 'this') {
        
        // Get all FB games for the week
        $url = "";
        switch ($week) {
            case "this":
                $url = "http://www.scoresandodds.com/footballschedule_thisweek.html";
                break;
            case "next":
                $url = "http://www.scoresandodds.com/footballschedule_nextweek.html";
                break;
            case "bowls":
                $url = "http://www.scoresandodds.com/ncaaf-bowl-games";
                break;
            default:
                $url = "http://www.scoresandodds.com/footballschedule_thisweek.html";
        }
        $data = $this->get_schedule($url);
        
        // If no data false
        if(!$data) {
            return false;
        }
        
        // if sport passed only give back that data
        $sched = array();
        switch ($sport) {
            case 'nfl':
                foreach($data as $day) {
                    $type = substr($day['title'],-3);
                    if($type == 'NFL') {
                        $sched[] = $day;
                    }
                }
                return $sched;
                break;
            case 'fbc':
                foreach($data as $day) {
                    $type = substr($day['title'],-3);
                    if($type == 'FBC') {
                        $sched[] = $day;
                    }
                }
                return $sched;
                break;
            default:
                $sched = $data;
        }
        
    }
    
    // Get sport schedule & HT
    public function get_sport_schedule_with_ht($sport = 'all',$week = 'this') {
        $sportSchedule = $this->get_sport_schedule($sport, $week);
        
        if(!$sportSchedule) { return false; };

        // Attempt connection
        $connect = new ScoresOddsScraper();
        $htLines = $connect->scrape_halftime(Utilities::halftime_section($sport));
        
        // See if we need to add lines
        if($htLines) {
            for($i = 0; $i < count($sportSchedule); $i++) {
                for($j = 0; $j < count($sportSchedule[$i]['games']); $j++) {
                    foreach ($htLines as $line) {
                        if(intval($line['game']) == intval($sportSchedule[$i]['games'][$j]['visitor']['rotation'])) {
                        if($line['date'] === $sportSchedule[$i]['date']) {
                            $sportSchedule[$i]['games'][$j]['visitor']['lines']['ht'] = isset($line['lines']['visitor']) ? $line['lines']['visitor'] : "";
                            $sportSchedule[$i]['games'][$j]['visitor']['scores']['ht'] = isset($line['scores']['visitor']) ? $line['scores']['visitor'] : "";
                            
                            $sportSchedule[$i]['games'][$j]['host']['lines']['ht'] = isset($line['lines']['host']) ? $line['lines']['host'] : "";
                            $sportSchedule[$i]['games'][$j]['host']['scores']['ht'] = isset($line['scores']['host']) ? $line['scores']['host'] : "";

                            $sportSchedule[$i]['games'][$j]['ht']['total'] = isset($line['total']) ? $line['total'] : "";
                        }}
                    }
                }
            }
        }
        
        return $sportSchedule;
        
    }
}