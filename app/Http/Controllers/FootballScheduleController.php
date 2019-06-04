<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use Goutte\Client as CrawlerClient;

use App\Library;
use App\Library\FootballScheduleScraper;
use App\Library\Format\WeeklyScheduleEmail;

class FootballScheduleController extends Controller
{
    
    
    // Show the NFL schedule for this week
    public function getNflSchedule() {
        
        // Collect the data
        $dataCollector = new FootballScheduleScraper();
        $data = $dataCollector->get_sport_schedule_with_ht('nfl', 'this');
        
        // If no data, send bad request
        if(!$data) {
            return false;
        }
        
        // Create the email format
        $format = new WeeklyScheduleEmail($data,'nfl');
        $email  = $format->create_email_text();
        
        return $email;
    }
    public function showNflSchedulePlainText() {
        $data = $this->getNflSchedule();
        if(!$data) {
            return response('No data was returned',400)->withHeaders(['Content-Type' => 'plain-text']);
        }
        return response($data)->withHeaders(['Content-Type' => 'plain-text']);
    }
    
    // Show the NCAA FB schedule for this week
    public function getNcaaFbSchedule() {
        // Collect the data
        $dataCollector = new FootballScheduleScraper();
        $data = $dataCollector->get_sport_schedule_with_ht('fbc', 'this');
        
        // If no data, send bad request
        if(!$data) {
            return false;
        }
        
        // Create the email format
        $format = new WeeklyScheduleEmail($data,'fbc');
        $email  = $format->create_email_text();
        
        return $email;
    }
    public function showNcaaFbSchedulePlainText() {
        $data = $this->getNcaaFbSchedule();
        if(!$data) {
            return response('No data was returned',400)->withHeaders(['Content-Type' => 'plain-text']);
        }
        return response($data)->withHeaders(['Content-Type' => 'plain-text']);
    }
    
    // Show the NFL schedule for next week
    public function getNextNflSchedule() {
        // Collect the data
        $dataCollector = new FootballScheduleScraper();
        $data = $dataCollector->get_sport_schedule_with_ht('nfl', 'next');
        
        // If no data, send bad request
        if(!$data) {
            return false;
        }
        
        // Create the email format
        $format = new WeeklyScheduleEmail($data,'nfl');
        $email  = $format->create_email_text();
        
        return $email;
    }
    public function showNextNflSchedulePlainText() {
        $data = $this->getNextNflSchedule();
        if(!$data) {
            return response('No data was returned',400)->withHeaders(['Content-Type' => 'plain-text']);
        }
        return response($data)->withHeaders(['Content-Type' => 'plain-text']);
    }
    
    // Show the NCAA FB schedule for next week
    public function getNextNcaaFbSchedule() {
        // Collect the data
        $dataCollector = new FootballScheduleScraper();
        $data = $dataCollector->get_sport_schedule_with_ht('fbc', 'next');
        
        // If no data, send bad request
        if(!$data) {
            return false;
        }
        
        // Create the email format
        $format = new WeeklyScheduleEmail($data,'fbc');
        $email  = $format->create_email_text();
        
        return $email;
    }
    public function showNextNcaaFbSchedulePlainText() {
        $data = $this->getNextNcaaFbSchedule();
        if(!$data) {
            return response('No data was returned',400)->withHeaders(['Content-Type' => 'plain-text']);
        }
        return response($data)->withHeaders(['Content-Type' => 'plain-text']);
    }
    
    // Show the NCAA FB Bowl schedule for next week
    public function getNcaaFbBowlSchedule() {
        // Collect the data
        $dataCollector = new FootballScheduleScraper();
        $data = $dataCollector->get_sport_schedule_with_ht('fbc', 'bowls');
        
        // If no data, send bad request
        if(!$data) {
            return false;
        }
        
        // Create the email format
        $format = new WeeklyScheduleEmail($data,'fbc');
        $email  = $format->create_email_text();
        
        return $email;
    }
    public function showNcaaFbBowlSchedulePlainText() {
        $data = $this->getNcaaFbBowlSchedule();
        if(!$data) {
            return response('No data was returned',400)->withHeaders(['Content-Type' => 'plain-text']);
        }
        return response($data)->withHeaders(['Content-Type' => 'plain-text']);
    }
}
